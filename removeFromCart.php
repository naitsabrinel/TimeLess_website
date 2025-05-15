<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non connecté']);
    exit;
}

$product_id = (int)$_POST['product_id'];
$user_id = (int)$_SESSION['user_id'];

try {
     $stmt = $pdo->prepare("DELETE FROM panier WHERE idutilisateur = ? AND idmontre = ?");
    $stmt->execute([$user_id, $product_id]);
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM panier WHERE idutilisateur = ?");
    $stmt->execute([$user_id]);
    $count = $stmt->fetchColumn();
    $stmt = $pdo->prepare("
        SELECT SUM(p.quantite * m.prix) as total
        FROM panier p
        JOIN montre m ON p.idmontre = m.idmontre
        WHERE p.idutilisateur = ?
    ");
    $stmt->execute([$user_id]);
    $total = $stmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'cart_count' => $count,
        'cart_total' => $total ? number_format($total, 2, '.', ' ') : '0.00'
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}