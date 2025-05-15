<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];

try {
     $stmt = $pdo->prepare("CALL FinaliserCommande(?)");
    $stmt->execute([$user_id]);
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $order_id = $result['id_commande'];
    
    $_SESSION['order_id'] = $order_id;
    
    header('Location: confirmation.php');
    exit;
    
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Erreur lors de la finalisation de la commande: " . $e->getMessage();
    header('Location: Panier.php');
    exit;
}
?>