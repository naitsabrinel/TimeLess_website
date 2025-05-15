<?php
require 'includes/db.php';
session_start();
 $idMontre = $_GET['id'] ?? null;

if (!$idMontre) {
    header('Location: index.php');
    exit;
}
try {
     $stmt = $pdo->prepare(query: "SELECT * FROM montre WHERE idmontre = ?");
    $stmt->execute([$idMontre]);
    $montre = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$montre) {
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}
 
 if (isset($_SESSION['success_message'])) {
    echo '<div class="notification success">'.htmlspecialchars($_SESSION['success_message']).'</div>';
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    echo '<div class="notification error">'.htmlspecialchars($_SESSION['error_message']).'</div>';
    unset($_SESSION['error_message']);
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timeless</title>
    <meta name="keywords" content="montres, montre homme, montre femme, montres de luxe, montres classiques, montres sport, boutique de montres, accessoires, horlogerie,
     montre élégante, montre moderne, montre vintage, Timeless, Sabrinel Naitcherif, montre automatique, montre connectée, montre bracelet cuir, 
     montre acier inoxydable, montre quartz, montre tendance, montre pas chère, montre design, montre mode, luxury watches, classic watches,
     men's watches, women's watches, fashion watches, timeless watches,Sabrinel Naitcherif">
    <meta name="author" content="Sabrinel Naitcherif">
    <link rel="stylesheet" href="Style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script>
    const CURRENT_USER_ID = <?= isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null' ?>;
        </script>
    <script src="js/function.js"></script></header>
</head>
<body>
    <header>
        <h2>TIMELESS</h2>
        <nav>
            <ul>
                <li><a href="Accueil.php"style="color:inherit; text-decoration:none;">ACCUEIL</a></li>
                <li><a href="Accueil.php#watches-section" style="color:inherit; text-decoration:none;">NOS MONTRES</a></li>
                <li>
                 <?php if(isset($_SESSION['user_id'])): ?>
             <a href="Panier.php" style="color:inherit; text-decoration:none;">PANIER</a>
                     <?php else: ?>
               <a href="login.php?redirect=Panier.php" style="color:inherit; text-decoration:none;">PANIER</a>
                 <?php endif; ?>
                </li>
                <li><a href="#contact" style="color:inherit; text-decoration:none;">CONTACT</a></li>
            </ul>
        </nav>
         
    </header>

    <section class="featured-watch">
        <div class="featured-container">
            <div class="featured-image">
                <img src="images/<?= htmlspecialchars($montre['pathImage']) ?>" alt="<?= htmlspecialchars($montre['titre']) ?>">
            </div>
            <div class="featured-content">
                <h2><?= htmlspecialchars($montre['titre']) ?></h2>
                <p class="subtitle"><?= htmlspecialchars($montre['description']) ?></p>
                
                <div class="specs">
                    <h3>Détails techniques</h3>
                    <ul>
                        <li><strong>Prix :</strong> <?= htmlspecialchars($montre['prix']) ?> DA</li>
                        <li><strong>Couleur :</strong> <?= htmlspecialchars($montre['couleur']) ?></li>
                        <li><strong>Catégorie :</strong> <?= htmlspecialchars($montre['categorie']) ?></li>
                        <li><strong>Sexe :</strong> <?= htmlspecialchars($montre['sexe']) ?></li>
                        <li><strong>Disponibilité :</strong> <?= ($montre['quantiteStock'] > 0) ? 'En stock' : 'Rupture' ?></li>
                    </ul>
                </div>

             <form method="post" action="addToCart.php">
             <input type="hidden" name="product_id" value="<?= (int)$montre['idmontre'] ?>">
             <button type="submit" class="cart-button">Ajouter au panier</button>
            </form>
            </div>
        </div>
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