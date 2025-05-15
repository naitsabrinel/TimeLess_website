<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['order_id'])) {
    header('Location: Accueil.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$order_id = (int)$_SESSION['order_id'];

try {
    $stmt = $pdo->prepare("CALL FinaliserCommandeEtViderPanier(?, ?)");
    $stmt->execute([$user_id, $order_id]);
    
    $stmt = $pdo->prepare("CALL GetCompleteOrderDetails(?, ?)");
    $stmt->execute([$order_id, $user_id]);
    
    $header = [];
    $articles = [];
    $total = 0;
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        switch ($row['section']) {
            case 'header':
                $header = $row;
                break;
            case 'detail':
                $articles[] = $row;
                break;
            case 'total':
                $total = $row['total'];
                break;
        }
    }
    
    if (empty($header)) {
        throw new Exception("Commande introuvable");
    }
    
    unset($_SESSION['order_id']);
    
} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
    header('Location: Panier.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
     <title>Timeless</title>
    <meta name="keywords" content="montres, montre homme, montre femme, montres de luxe, montres classiques, montres sport, boutique de montres, accessoires, horlogerie,
     montre élégante, montre moderne, montre vintage, Timeless, Sabrinel Naitcherif, montre automatique, montre connectée, montre bracelet cuir, 
     montre acier inoxydable, montre quartz, montre tendance, montre pas chère, montre design, montre mode, luxury watches, classic watches,
     men's watches, women's watches, fashion watches, timeless watches,Sabrinel Naitcherif">
    <meta name="author" content="Sabrinel Naitcherif">
    <link rel="stylesheet" href="Style1.css">
    <style>
        .confirmation-section {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .order-info {
            background: #f9f9f9;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 2rem;
        }
        .order-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }
        .order-item img {
            width: 80px;
            border-radius: 4px;
        }
        .order-total {
            text-align: right;
            font-size: 1.2rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #896045;
        }
        .continue-shopping {
            display: inline-block;
            margin-top: 2rem;
            padding: 0.8rem 1.5rem;
            background: #896045;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s;
        }
        .continue-shopping:hover {
            background: #6d4b30;
        }
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            background: #4CAF50;
            color: white;
            border-radius: 3px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>    
    <section class="confirmation-section">
        <h2>Commande confirmée #<?= $header['idcommande'] ?></h2>
        <p>Merci pour votre commande, <?= explode(' ', $header['client'])[0] ?> !</p>
        <p>Votre panier a été vidé avec succès.</p>
        
        <div class="order-info">
            <h3>Récapitulatif</h3>
            <p><strong>Date :</strong> <?= date('d/m/Y H:i', strtotime($header['date_commande'])) ?></p>
            <p><strong>Statut :</strong> <span class="status-badge"><?= $header['statut'] ?></span></p>
            <p><strong>Client :</strong> <?= $header['client'] ?></p>
            <p><strong>Email :</strong> <?= $header['email'] ?></p>
        </div>
        
        <div class="order-items">
            <h3>Articles commandés</h3>
            <?php foreach ($articles as $article): ?>
                <div class="order-item">
                    <img src="images/<?= basename($article['image']) ?>" alt="<?= htmlspecialchars($article['article']) ?>">
                    <div class="item-details">
                        <h4><?= htmlspecialchars($article['article']) ?></h4>
                        <p><?= $article['quantite'] ?> × <?= number_format($article['prix_unitaire'], 2, '.', ' ') ?> DA</p>
                        <p class="item-subtotal"><?= number_format($article['sous_total'], 2, '.', ' ') ?> DA</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="order-total">
            <h3>Total à payer: <?= number_format($total, 2, '.', ' ') ?> DA</h3>
        </div>
    
        <a href="Accueil.php" class="continue-shopping" ;>Confirmation</a>
    </section>
    
    <footer>
        <div class="footer-content">
            <div class="contact-info" id="contact">
                <h3>CONTACT</h3>
                <ul>
                    <li>
                        <i class="fas fa-phone"></i>
                        <span>+213 777 777 777</span>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <span>naitsabrinel@gmail.com</span>
                    </li>
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Bab Ezzouar</span>
                    </li>
                </ul>
            </div>
            <div class="copyright">
                copyright © 2025 NAIT CHERIF SABRINEL
            </div>
            <div class="footer-right">
            <img src="images/logo.png" alt="Timeless Logo" class="footer-logo">
           </div>
        </div>
    </footer>

</body>
</html>