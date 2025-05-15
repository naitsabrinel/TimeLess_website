<?php
session_start();
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    
    if (isset($_SESSION['user_id'])) {
         $user_id = (int)$_SESSION['user_id'];
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM panier WHERE idutilisateur = ? AND idmontre = ?");
            $stmt->execute([$user_id, $product_id]);
            $existing_item = $stmt->fetch();
            
            if ($existing_item) {
                 $stmt = $pdo->prepare("UPDATE panier SET quantite = quantite + 1 WHERE idutilisateur = ? AND idmontre = ?");
                $stmt->execute([$user_id, $product_id]);
            } else {
                 $stmt = $pdo->prepare("INSERT INTO panier (idutilisateur, idmontre, quantite) VALUES (?, ?, 1)");
                $stmt->execute([$user_id, $product_id]);
            }
            
            $_SESSION['success_message'] = "Article ajouté au panier";
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Erreur lors de l'ajout au panier: " . $e->getMessage();
        }
    } else {
         $cart_items = isset($_COOKIE['cart_items']) ? json_decode($_COOKIE['cart_items'], true) : [];
        
        if (isset($cart_items[$product_id])) {
            $cart_items[$product_id]++;
        } else {
            $cart_items[$product_id] = 1;
        }
        
        setcookie('cart_items', json_encode($cart_items), time() + (86400 * 30), "/"); // 30 jours
        $_SESSION['success_message'] = "Article ajouté au panier temporaire";
    }
    
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}
?>