<?php
session_start();
require 'includes/db.php'; 

 if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

 $stmt = $pdo->prepare("CALL GetOrderHistory(?)");
$stmt->execute([$user_id]);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

 $stmt->closeCursor();

 $annulees = $pdo->prepare("SELECT * FROM HISTORIQUE_COMMANDES WHERE idutilisateur = ? ORDER BY dateannulation DESC");
$annulees->execute([$user_id]);
$annulations = $annulees->fetchAll(PDO::FETCH_ASSOC);
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
        table { border-collapse: collapse; width: 90%; margin: 2rem auto; }
        th, td { border: 1px solid #896045; padding: 0.75rem; text-align: center; }
        th { background-color: #896045; color: white; }
        h2 { text-align: center; 
            font-size: 2.5rem;
    color: #896045;
    margin-bottom: 0.5rem;}
    .annuler-btn {
    background-color: #c0392b;
    color: white;
    border: none;
    padding: 8px 14px;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

.annuler-btn:hover {
    background-color: #a93226;
}

    </style>
</head>
<body>

<header>
        <h2>TIMELESS</h2>
        <nav>
            <ul>
                <li><a href="Accueil.php" style="color:inherit; text-decoration:none;">ACCUEIL</a></li>
                <li><a href="Accueil.php#watches-section" style="color:inherit; text-decoration:none;">NOS MONTRES</a></li>
                <li><a href="panier.php" style="color:inherit; text-decoration:none;">PANIER  </a></li>
                <li><a href="#contact" style="color:inherit; text-decoration:none;">CONTACT</a></li>
            </ul>
        </nav>
        <?php if(isset($_SESSION['user_id'])): ?>
        <div class="user-welcome">
            <span class="welcome-text">Bienvenue, </span>
            <span class="user-name">
                <?= htmlspecialchars(
                    $_SESSION['user_nom'] ?? 
                    (isset($_SESSION['user_prenom']) ? explode(' ', $_SESSION['user_prenom'])[0] : 'Utilisateur'
                )) ?>
            </span>
            <form action="logout.php" method="post" class="logout-form">
                <button type="submit">Déconnexion</button>
            </form>
            
        </div>
        <?php else: ?>
            <a href="login.php" class="login-link">
                <button>Connexion</button>
            </a>
        <?php endif; ?>
    </header>

<h2>Historique de vos commandes</h2>

<?php if (count($commandes) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID Commande</th>
                <th>Date</th>
                <th>Statut</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($commandes as $cmd): ?>
                <tr>
                    <td><?= htmlspecialchars($cmd['idcommande']) ?></td>
                    <td><?= htmlspecialchars($cmd['datecommande']) ?></td>
                    <td><?= htmlspecialchars(!empty($cmd['statut']) ? $cmd['statut'] : 'En attente') ?></td>
                    <td><?= number_format($cmd['montant_total'], 2) ?> DA</td>
                  <td>
    <?php if ($cmd['statut'] !== 'annulée'): ?>
        <form action="annuler_commande.php" method="post" onsubmit="return confirm('Voulez-vous vraiment annuler cette commande ?');">
            <input type="hidden" name="idcommande" value="<?= htmlspecialchars($cmd['idcommande']) ?>">
            <button type="submit" class="annuler-btn">Annuler</button>
        </form>
    <?php else: ?>
        <span style="color: #c0392b;">Commande annulée</span>
    <?php endif; ?>
</td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p style="text-align:center;">Aucune commande trouvée.</p>
<?php endif; ?>


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