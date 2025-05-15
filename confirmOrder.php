<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Veuillez vous connecter d\'abord']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];

try {
    $pdo->beginTransaction();
        $stmt = $pdo->prepare("
        SELECT COUNT(*) as count
        FROM panier
        WHERE idutilisateur = ?
    ");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] == 0) {
        echo json_encode(['success' => false, 'message' => 'Votre panier est vide']);
        exit;
    }
        $stmt = $pdo->prepare("
        SELECT p.*, m.prix, m.quantiteStock
        FROM panier p
        JOIN montre m ON p.idmontre = m.idmontre
        WHERE p.idutilisateur = ?
    ");
    $stmt->execute([$user_id]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cartItems as $item) {
        if ($item['quantite'] > $item['quantiteStock']) {
            $pdo->rollBack();
            echo json_encode([
                'success' => false, 
                'message' => 'Stock insuffisant pour ' . $item['idmontre']
            ]);
            exit;
        }
    }
        $stmt = $pdo->prepare("
        INSERT INTO commande (idutilisateur, statut)
        VALUES (?, 'en attente')
    ");
    $stmt->execute([$user_id]);
    $order_id = $pdo->lastInsertId();
    
    $stmt = $pdo->prepare("
        INSERT INTO commande_detail (idcommande, idmontre, quantite, prixunitaire)
        VALUES (?, ?, ?, ?)
    ");
    
    foreach ($cartItems as $item) {
        $stmt->execute([
            $order_id,
            $item['idmontre'],
            $item['quantite'],
            $item['prix']
        ]);
                $new_stock = $item['quantiteStock'] - $item['quantite'];
        $update = $pdo->prepare("UPDATE montre SET quantiteStock = ? WHERE idmontre = ?");
        $update->execute([$new_stock, $item['idmontre']]);
    }
    
    $stmt = $pdo->prepare("DELETE FROM panier WHERE idutilisateur = ?");
    $stmt->execute([$user_id]);
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Commande confirmée avec succès',
        'order_id' => $order_id
    ]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
}
