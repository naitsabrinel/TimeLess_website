<?php
session_start();
require 'includes/db.php';
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
    <style>
        .cart-count {
            background-color: #896045;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            margin-left: 5px;
            vertical-align: top;
        }
    </style>
    <script>
        const CURRENT_USER_ID = <?= isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null' ?>;
    </script>
</head>
<body>
<header>
    <h2>TIMELESS</h2>
    <nav>
        <ul>
            <li><a href="Accueil.php" style="color:inherit; text-decoration:none;">ACCUEIL</a></li>
            <li><a href="#watches-section">NOS MONTRES</a></li>
             <li>
    <a href="Panier.php" style="color:inherit; text-decoration:none;">
        PANIER 
        <?php 
        if(isset($_SESSION['user_id'])) {
             try {
                $stmt = $pdo->prepare("SELECT SUM(quantite) as total FROM panier WHERE idutilisateur = ?");
                $stmt->execute([(int)$_SESSION['user_id']]);
                $total = $stmt->fetchColumn();
                if ($total > 0) echo '<span class="cart-count">'.$total.'</span>';
            } catch (PDOException $e) {
             }
        } elseif(isset($_COOKIE['cart_items'])) {
             $cart_items = json_decode($_COOKIE['cart_items'], true);
            $total = array_sum($cart_items);
            if ($total > 0) echo '<span class="cart-count">'.$total.'</span>';
        }
        ?>
    </a>
</li>
            <li><a href="#contact" style="color:inherit; text-decoration:none;">CONTACT</a></li>
        </ul>
    </nav>

    <?php if(isset($_SESSION['user_id'])): ?>
        <div class="user-welcome">
            <span class="welcome-text">Bienvenue, </span>
            <span class="user-name">
                <?= htmlspecialchars($_SESSION['user_nom'] ?? explode(' ', $_SESSION['user_prenom'])[0] ?? 'Utilisateur') ?>
            </span>
            <form action="logout.php" method="post" class="logout-form">
                <button type="submit">Déconnexion</button>
            </form>
        </div>
    <?php else: ?>
        <a href="login.php" class="login-link"><button>Connexion</button></a>
    <?php endif; ?>
</header>

<section class="main-section">
    <div class="text-content">
        <h1>Timeless by Sabrinel</h1>
        <p>Plongez dans l’univers de Time Less, votre boutique en ligne dédiée aux montres qui allient style, qualité et intemporalité. Chaque modèle est soigneusement sélectionné pour sublimer votre poignet et marquer vos instants les plus précieux.</p>
    </div>
    <div class="video-container">
        <video autoplay muted loop class="main-video">
            <source src="video.mp4" type="video/mp4">
            Votre navigateur ne supporte pas les vidéos HTML5.
        </video>
    </div>
</section>

<section class="gallery-section">
    <h2>Brillez avec Timeless</h2>
    <div class="gallery-wrapper">
        <!-- Contrôles radio -->
        <input type="radio" name="slider" id="slide1" checked>
        <input type="radio" name="slider" id="slide2">
        <input type="radio" name="slider" id="slide3">

        <div class="gallery-container">
            <div class="gallery-track">
                <div class="gallery-slide">
                    <div class="gallery-item"><img src="galerie/photo1.jpg" alt="Client 1"></div>
                    <div class="gallery-item"><img src="galerie/photo2.jpg" alt="Client 2"></div>
                    <div class="gallery-item"><img src="galerie/photo10.jpg" alt="Client 3"></div>
                    <div class="gallery-item"><img src="galerie/photo9.jpg" alt="Client 4"></div>
                </div>
                <div class="gallery-slide">
                    <div class="gallery-item"><img src="galerie/photo5.jpg" alt="Client 5"></div>
                    <div class="gallery-item"><img src="galerie/photo3.jpg" alt="Client 6"></div>
                    <div class="gallery-item"><img src="galerie/photo11.jpg" alt="Client 7"></div>
                    <div class="gallery-item"><img src="galerie/photo8.jpg" alt="Client 8"></div>
                </div>
                <div class="gallery-slide">
                    <div class="gallery-item"><img src="galerie/photo12.jpg" alt="Client 9"></div>
                    <div class="gallery-item"><img src="galerie/photo6.jpg" alt="Client 10"></div>
                    <div class="gallery-item"><img src="galerie/photo4.jpg" alt="Client 11"></div>
                    <div class="gallery-item"><img src="galerie/photo07.jpg" alt="Client 12"></div>
                </div>
            </div>
        </div>

        <div class="gallery-dots">
            <label for="slide1"></label>
            <label for="slide2"></label>
            <label for="slide3"></label>
        </div>
    </div>
</section>

 <section class="watches-section" id="watches-section">
    <form method="get" action="#watches-section" class="search-container">
        <div class="search-bar">
            <input type="text" name="search" placeholder="Rechercher une montre..." 
                value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            <button type="submit" class="search-button">Rechercher</button>
        </div>
        <div class="filters">
            <select name="categorie" class="filter-select">
                <option value="">CATÉGORIES</option>
                <option value="classique" <?= (isset($_GET['categorie']) && $_GET['categorie'] == 'classique') ? 'selected' : '' ?>>Classique</option>
                <option value="sport" <?= (isset($_GET['categorie']) && $_GET['categorie'] == 'sport') ? 'selected' : '' ?>>Sport</option>
                <option value="luxe" <?= (isset($_GET['categorie']) && $_GET['categorie'] == 'luxe') ? 'selected' : '' ?>>Luxe</option>
            </select>
            <select name="sexe" class="filter-select">
                <option value="">SEXE</option>
                <option value="HOMME" <?= (isset($_GET['sexe']) && $_GET['sexe'] == 'HOMME') ? 'selected' : '' ?>>Homme</option>
                <option value="FEMME" <?= (isset($_GET['sexe']) && $_GET['sexe'] == 'FEMME') ? 'selected' : '' ?>>Femme</option>
                <option value="MIX" <?= (isset($_GET['sexe']) && $_GET['sexe'] == 'MIX') ? 'selected' : '' ?>>Mixte</option>
            </select>
            <a href="Accueil.php" class="reset-filters">Réinitialiser</a>
        </div>
    </form>
     <div class="watches-container">
        <?php
        try {
            $sql = "SELECT * FROM montre WHERE quantiteStock > 0";
            $params = [];

            if (!empty($_GET['search'])) {
                $sql .= " AND (titre LIKE :search OR description LIKE :search)";
                $params[':search'] = '%' . $_GET['search'] . '%';
            }
            if (!empty($_GET['categorie'])) {
                $sql .= " AND categorie = :categorie";
                $params[':categorie'] = $_GET['categorie'];
            }
            if (!empty($_GET['sexe'])) {
                $sql .= " AND sexe = :sexe";
                $params[':sexe'] = $_GET['sexe'];
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $montres = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($montres)) {
                echo '<div class="no-results">Aucune montre ne correspond à vos critères de recherche.</div>';
            } else {
                foreach ($montres as $montre) {
                    echo '
                    <div class="watch-card">
                        <div class="watch-image">
                            <img src="images/'.htmlspecialchars($montre['pathImage']).'" alt="'.htmlspecialchars($montre['titre']).'">
                        </div>
                        <div class="watch-info">
                            <h3>'.htmlspecialchars($montre['titre']).'</h3>
                            <p>'.htmlspecialchars($montre['description']).'</p>
                            <span class="watch-price">'.htmlspecialchars($montre['prix']).' DA</span>
                            <div class="button-group">
                                <button class="watch-button" onclick="window.location.href=\'details_montre.php?id='.(int)$montre['idmontre'].'\'">Afficher plus</button>
                                <form method="post" action="addToCart.php" class="inline-form">
                                    <input type="hidden" name="product_id" value="'.(int)$montre['idmontre'].'">
                                    <button type="submit" class="cart-button">Ajouter</button>
                                </form>
                            </div>
                        </div>
                    </div>';
                }
            }
        } catch (PDOException $e) {
            echo '<div class="error">Erreur de chargement des montres : '.htmlspecialchars($e->getMessage()).'</div>';
        }
        ?>
    </div>
</section>

<?php
if (isset($_SESSION['success_message'])) {
    echo '<div class="notification success">'.htmlspecialchars($_SESSION['success_message']).'</div>';
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    echo '<div class="notification error">'.htmlspecialchars($_SESSION['error_message']).'</div>';
    unset($_SESSION['error_message']);
}
?>
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
