<?php
require 'db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['idutilisateur'])) {
    echo json_encode(['success' => false, 'message' => 'Veuillez vous connecter']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

try {
     $stmt = $pdo->prepare("SELECT * FROM panier WHERE idutilisateur = ? AND idmontre = ?");
    $stmt->execute([$_SESSION['idutilisateur'], $data['idMontre']]);
    
    if ($stmt->rowCount() > 0) {
         $pdo->prepare("UPDATE panier SET quantite = quantite + 1 
                      WHERE idutilisateur = ? AND idmontre = ?")
           ->execute([$_SESSION['idutilisateur'], $data['idMontre']]);
    } else {
         $pdo->prepare("INSERT INTO panier (idutilisateur, idmontre, quantite) 
                      VALUES (?, ?, ?)")
           ->execute([$_SESSION['idutilisateur'], $data['idMontre'], 1]);
    }
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>