<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = 'Veuillez vous connecter';
    header('Location: login.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];

try {
    if (isset($_POST['increase'])) {
        $product_id = (int)$_POST['increase'];
         $stmt = $pdo->prepare("UPDATE panier SET quantite = quantite + 1 WHERE idutilisateur = ? AND idmontre = ?");
        $stmt->execute([$user_id, $product_id]);
    } 
    elseif (isset($_POST['decrease'])) {
        $product_id = (int)$_POST['decrease'];
         $stmt = $pdo->prepare("SELECT quantite FROM panier WHERE idutilisateur = ? AND idmontre = ?");
        $stmt->execute([$user_id, $product_id]);
        $quantite = $stmt->fetchColumn();
        
        if ($quantite > 1) {
             $stmt = $pdo->prepare("UPDATE panier SET quantite = quantite - 1 WHERE idutilisateur = ? AND idmontre = ?");
            $stmt->execute([$user_id, $product_id]);
        } else {
             $stmt = $pdo->prepare("DELETE FROM panier WHERE idutilisateur = ? AND idmontre = ?");
            $stmt->execute([$user_id, $product_id]);
        }
    } 
    elseif (isset($_POST['remove'])) {
        $product_id = (int)$_POST['remove'];
         $stmt = $pdo->prepare("DELETE FROM panier WHERE idutilisateur = ? AND idmontre = ?");
        $stmt->execute([$user_id, $product_id]);
    }
    elseif (isset($_POST['checkout'])) {
         $pdo->beginTransaction();
        
         $stmt = $pdo->prepare("SELECT COUNT(*) FROM panier WHERE idutilisateur = ?");
        $stmt->execute([$user_id]);
        if ($stmt->fetchColumn() == 0) {
            throw new Exception("Votre panier est vide");
        }
        
         $stmt = $pdo->prepare("
            SELECT p.idmontre, p.quantite, m.prix, m.quantiteStock, m.titre
            FROM panier p
            JOIN montre m ON p.idmontre = m.idmontre
            WHERE p.idutilisateur = ?
        ");
        $stmt->execute([$user_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($items as $item) {
            if ($item['quantite'] > $item['quantiteStock']) {
                throw new Exception("Stock insuffisant pour " . $item['titre']);
            }
        }
        
         $stmt = $pdo->prepare("
            INSERT INTO COMMANDE (idutilisateur, statut) 
            VALUES (?, 'en attente')
        ");
        $stmt->execute([$user_id]);
        $order_id = $pdo->lastInsertId();
        
         $stmt_detail = $pdo->prepare("
            INSERT INTO COMMANDE_DETAIL 
            (idcommande, idmontre, quantite, prixunitaire) 
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($items as $item) {
             $stmt_detail->execute([
                $order_id,
                $item['idmontre'],
                $item['quantite'],
                $item['prix']
            ]);
         }
        
        $stmt = $pdo->prepare("UPDATE COMMANDE SET statut = 'faite' WHERE idcommande = ?");
        $stmt->execute([$order_id]);
        
        $stmt = $pdo->prepare("DELETE FROM panier WHERE idutilisateur = ?");
        $stmt->execute([$user_id]);
        
        $pdo->commit();
        
        $_SESSION['order_id'] = $order_id;
        header('Location: confirmation.php');
        exit;
    }
    
    header('Location: Panier.php');
    exit;
    
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error_message'] = $e->getMessage();
    header('Location: Panier.php');
    exit;
}