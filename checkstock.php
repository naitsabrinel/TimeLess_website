<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];

try {
     $stmt = $pdo->prepare("
        SELECT p.idmontre, p.quantite, m.titre, m.quantiteStock, m.prix
        FROM panier p
        JOIN montre m ON p.idmontre = m.idmontre
        WHERE p.idutilisateur = ?
    ");
    $stmt->execute([$user_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $out_of_stock = [];
    foreach ($items as $item) {
        if ($item['quantite'] > $item['quantiteStock']) {
            $out_of_stock[] = [
                'titre' => $item['titre'],
                'demande' => $item['quantite'],
                'disponible' => $item['quantiteStock'],
                'message' => "La quantité demandée pour '".$item['titre']."' (".$item['quantite'].") dépasse le stock disponible (".$item['quantiteStock'].")"
            ];
        }
    }
    
    if (!empty($out_of_stock)) {
        $_SESSION['stock_errors'] = $out_of_stock;
        header('Location: Panier.php');
        exit;
    }

     $pdo->beginTransaction();
    $order_stmt = $pdo->prepare("
        INSERT INTO commande (idutilisateur, datecommande, statut)
        VALUES (?, NOW(), 'en attente')
    ");
    $order_stmt->execute([$user_id]);
    $order_id = $pdo->lastInsertId();
        foreach ($items as $item) {
        $detail_stmt = $pdo->prepare("
            INSERT INTO commande_detail (idcommande, idmontre, quantite, prixunitaire)
            VALUES (?, ?, ?, ?)
        ");
        $detail_stmt->execute([
            $order_id,
            $item['idmontre'],
            $item['quantite'],
            $item['prix']
        ]);
        
        $update_stmt = $pdo->prepare("
            UPDATE montre
            SET quantiteStock = quantiteStock - ?
            WHERE idmontre = ?
        ");
        $update_stmt->execute([
            $item['quantite'],
            $item['idmontre']
        ]);
    }
    
    $clear_cart = $pdo->prepare("DELETE FROM panier WHERE idutilisateur = ?");
    $clear_cart->execute([$user_id]);
    
    $pdo->commit();
    
    $_SESSION['order_success'] = "Commande confirmée avec succès!";
    header('Location: commandeSuccess.php');
    exit;

} catch (PDOException $e) {
    $pdo->rollBack();
    
    if ($e->getCode() == '45000') {
        $_SESSION['error'] = "Erreur lors de la commande: ".$e->getMessage();
    } else {
        $_SESSION['error'] = "Erreur technique: ".$e->getMessage();
    }
    
    header('Location: Panier.php');
    exit;
}