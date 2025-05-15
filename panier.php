<?php
session_start();
require 'includes/db.php';

 if (!isset($_SESSION['user_id'])) {
     $cart_items_cookie = isset($_COOKIE['cart_items']) ? json_decode($_COOKIE['cart_items'], true) : [];
    
    if (!empty($cart_items_cookie)) {
        $product_ids = array_keys($cart_items_cookie);
        $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM montre WHERE idmontre IN ($placeholders)");
            $stmt->execute($product_ids);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $cart_items = [];
            $cart_total = 0;
            
            foreach ($products as $product) {
                $quantity = $cart_items_cookie[$product['idmontre']];
                $product['quantite'] = $quantity;
                $cart_items[] = $product;
                $cart_total += $product['prix'] * $quantity;
            }
        } catch (PDOException $e) {
            $error_message = "Erreur de chargement des produits: " . $e->getMessage();
        }
    }
    
     ?>
    <!DOCTYPE html>
    <html lang="en">
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
            .cart-notice {
                background-color: #f8f9fa;
                padding: 15px;
                border-radius: 5px;
                margin-bottom: 20px;
                text-align: center;
                border: 1px solid #ddd;
            }
            .login-prompt {
                text-align: center;
                margin-top: 20px;
            }
            .login-prompt a {
                color: #896045;
                text-decoration: underline;
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
                <li>PANIER</li>
                <li><a href="#contact" style="color:inherit; text-decoration:none;">CONTACT</a></li>
            </ul>
        </nav>
        <a href="login.php" class="login-link">
            <button>Connexion</button>
        </a>
    </header>

    <section class="cart-section">
        <h2>MON PANIER</h2>
        
        <div class="cart-container">
            <?php if(empty($cart_items)): ?>
                <div class="empty-cart">
                    <p>Votre panier est vide</p>
                    <div class="login-prompt">
                        <a href="login.php?redirect=Panier.php">Connectez-vous</a> pour enregistrer votre panier.
                    </div>
                </div>
            <?php else: ?>
                <div class="cart-notice">
                    <p>Vous n'êtes pas connecté. <a href="login.php?redirect=Panier.php">Connectez-vous</a> pour enregistrer votre panier.</p>
                </div>
                
                <?php foreach($cart_items as $item): ?>
                    <div class="cart-item">
                        <div class="cart-item-image">
                            <img src="images/<?= htmlspecialchars($item['pathImage']) ?>" alt="<?= htmlspecialchars($item['titre']) ?>">
                        </div>
                        <div class="cart-item-info">
                            <h3><?= htmlspecialchars($item['titre']) ?></h3>
                            <p><?= htmlspecialchars($item['description']) ?></p>
                            <span class="cart-item-price"><?= number_format($item['prix'] * $item['quantite'], 2, '.', ' ') ?> DA</span>
                            <div class="quantity-display">
                                <span>Quantité: <?= (int)$item['quantite'] ?></span>
                            </div>
                            <form method="post" action="removeFromCartCookie.php">
                                <input type="hidden" name="product_id" value="<?= (int)$item['idmontre'] ?>">
                                <button type="submit" class="remove-item-btn">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="cart-summary">
                    <div class="total-price">
                        <span>Total :</span>
                        <span class="total-amount"><?= number_format($cart_total, 2, '.', ' ') ?> DA</span>
                    </div>
                    <div class="checkout-notice">
                        <p>Connectez-vous pour passer commande</p>
                        <a href="login.php?redirect=Panier.php" class="login-link">
                            <button>Se connecter</button>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
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
    <?php
    exit;
}

 $user_id = (int)$_SESSION['user_id'];
$cart_items = [];
$cart_total = 0;

try {
    $stmt = $pdo->prepare("
        SELECT p.*, m.titre, m.description, m.prix, m.pathImage, m.quantiteStock
        FROM panier p
        JOIN montre m ON p.idmontre = m.idmontre
        WHERE p.idutilisateur = ?
    ");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($cart_items as $item) {
        $cart_total += $item['prix'] * $item['quantite'];
    }
} catch (PDOException $e) {
    $error_message = "Erreur de chargement du panier: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
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
    <script src="js/function.js"></script>
    <style>
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 4px;
            color: white;
            font-weight: bold;
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 0.3s, transform 0.3s;
            z-index: 1000;
            max-width: 300px;
        }
        .notification.success {
            background-color: #4CAF50;
        }
        .notification.error {
            background-color: #f44336;
        }
        .empty-cart {
            text-align: center;
            padding: 40px 0;
            font-size: 18px;
            color: #666;
        }
        .stock-error {
            color: #d9534f;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 8px 12px;
            border-radius: 4px;
            margin: 10px 0;
            font-size: 14px;
        }
        .error-item {
            border-left: 3px solid #f44336;
            padding-left: 10px;
        }
        .history-btn {
            background-color: #896045;
            font-family: 'Georgia', serif;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
            transition: background-color 0.3s;
        }
        .history-btn:hover {
            background-color: #6d4b30;
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
                <li>PANIER</li>
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

    <section class="cart-section">
        <h2>MON PANIER</h2>
                <?php if (!empty($_SESSION['stock_errors'])): ?>
            <div class="notification error">
                <h3>Problème de stock</h3>
                <?php foreach ($_SESSION['stock_errors'] as $error): ?>
                    <p><?= htmlspecialchars($error['message']) ?></p>
                <?php endforeach; ?>
                <?php unset($_SESSION['stock_errors']); ?>
            </div>
        <?php endif; ?>
        
        <div class="cart-container">
           <a href="historique_commande.php" class="history-link">
                <button class="history-btn">HISTORIQUE  </button>
            </a>
            <?php if(isset($error_message)): ?>
                <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
            <?php elseif(empty($cart_items)): ?>
                <div class="empty-cart">
                    <p>Votre panier est vide</p>
                </div>
            <?php else: ?>
                <form method="post" action="updateCart.php">
                    <?php foreach($cart_items as $item): 
                        $has_stock_error = $item['quantite'] > $item['quantiteStock'];
                    ?>
                        <div class="cart-item <?= $has_stock_error ? 'error-item' : '' ?>">
                            <?php if ($has_stock_error): ?>
                                <div class="stock-error">
                                    La quantité demandée (<?= $item['quantite'] ?>) dépasse le stock disponible (<?= $item['quantiteStock'] ?>)
                                </div>
                            <?php endif; ?>
                            
                            <div class="cart-item-image">
                                <img src="images/<?= htmlspecialchars($item['pathImage']) ?>" alt="<?= htmlspecialchars($item['titre']) ?>">
                            </div>
                            <div class="cart-item-info">
                                <h3><?= htmlspecialchars($item['titre']) ?></h3>
                                <p><?= htmlspecialchars($item['description']) ?></p>
                                <span class="cart-item-price"><?= number_format($item['prix'] * $item['quantite'], 2, '.', ' ') ?> DA</span>
                                <div class="quantity-control">
                                    <button type="submit" name="decrease" value="<?= (int)$item['idmontre'] ?>" class="quantity-btn">-</button>
                                    <span class="quantity"><?= (int)$item['quantite'] ?></span>
                                    <button type="submit" name="increase" value="<?= (int)$item['idmontre'] ?>" class="quantity-btn">+</button>
                                </div>
                                <button type="submit" name="remove" value="<?= (int)$item['idmontre'] ?>" class="remove-item-btn">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="cart-summary">
                        <div class="total-price">
                            <span>Total :</span>
                            <span class="total-amount"><?= number_format($cart_total, 2, '.', ' ') ?> DA</span>
                        </div>
                        <form method="post" action="checkStock.php">
                        <button type="submit" name="checkout" class="checkout-btn">Confirmer la commande</button>
                         </form>
                    </div>
                </form>
            <?php endif; ?>
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