
<?php
session_start();
require 'includes/db.php';

$error = '';
$max_attempts = 3; 
$block_time = 300; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     if (isset($_SESSION['blocked_until']) && time() < $_SESSION['blocked_until']) {
        $remaining_time = $_SESSION['blocked_until'] - time();
        $error = "Trop de tentatives. Veuillez réessayer dans ".gmdate("i:s", $remaining_time);
    } else {
         if (isset($_SESSION['blocked_until']) && time() >= $_SESSION['blocked_until']) {
            unset($_SESSION['blocked_until']);
            unset($_SESSION['login_attempts']);
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        try {
            if ($email === 'admin@gmail.com' && $password === '2004') {
                $_SESSION['user_id'] = 0;  // ID de l'admin
                $_SESSION['user_email'] = 'admin@gmail.com';
                $_SESSION['user_prenom'] = 'Admin';
                $_SESSION['user_nom'] = 'Admin';
                
                 transferCartFromCookies(0);
                
                header('Location: admin_add_watch.php');
                exit;
            }
            
            $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && $password === $user['motdepasse']) {
                 unset($_SESSION['login_attempts']);
                unset($_SESSION['blocked_until']);

                $_SESSION['user_id'] = $user['idutilisateur'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_prenom'] = $user['prenom'];
                $_SESSION['user_nom'] = $user['nom'];
                
                 transferCartFromCookies($user['idutilisateur']);
                
                 if (isset($_GET['redirect'])) {
                    header("Location: " . $_GET['redirect']);
                } else {
                    header('Location: Accueil.php');
                }
                exit;
            } else {
                 $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;

                if ($_SESSION['login_attempts'] >= $max_attempts) {
                    $_SESSION['blocked_until'] = time() + $block_time;
                    $error = "Trop de tentatives. Compte bloqué pendant 5 minutes.";
                } else {
                    $remaining_attempts = $max_attempts - $_SESSION['login_attempts'];
                    $error = "Email ou mot de passe incorrect. Il vous reste $remaining_attempts tentative(s).";
                }
            }
        } catch (PDOException $e) {
            $error = "Erreur de connexion : ".$e->getMessage();
        }
    }
}

 
function transferCartFromCookies($user_id) {
    global $pdo;
    
    if (isset($_COOKIE['cart_items'])) {
        $cart_items = json_decode($_COOKIE['cart_items'], true);
        
        if (!empty($cart_items)) {
            try {
                foreach ($cart_items as $product_id => $quantity) {
                    $product_id = (int)$product_id;
                    $quantity = (int)$quantity;
                    
                     $stmt = $pdo->prepare("SELECT * FROM panier WHERE idutilisateur = ? AND idmontre = ?");
                    $stmt->execute([$user_id, $product_id]);
                    $existing_item = $stmt->fetch();
                    
                    if ($existing_item) {
                         $stmt = $pdo->prepare("UPDATE panier SET quantite = quantite + ? WHERE idutilisateur = ? AND idmontre = ?");
                        $stmt->execute([$quantity, $user_id, $product_id]);
                    } else {
                         $stmt = $pdo->prepare("INSERT INTO panier (idutilisateur, idmontre, quantite) VALUES (?, ?, ?)");
                        $stmt->execute([$user_id, $product_id, $quantity]);
                    }
                }
                
                 setcookie('cart_items', '', time() - 3600, "/");
                $_SESSION['success_message'] = "Votre panier temporaire a été transféré dans votre compte";
            } catch (PDOException $e) {
                $_SESSION['error_message'] = "Erreur lors du transfert du panier: " . $e->getMessage();
            }
        }
    }
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
    <style>
        .login-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 2rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .form-group label {
            font-weight: bold;
            color: #896045;
        }
        .form-group input {
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        .login-button {
            background-color: #896045;
            color: white;
            padding: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
        }
        .login-button:hover {
            background-color: #6d4b30;
        }
        .error-message {
            color: #d32f2f;
            margin-bottom: 1rem;
            text-align: center;
        }
        .register-link {
            text-align: center;
            color: #000;
            margin-top: 1rem;
        }
        .login-link a {
         text-decoration: underline !important;
        }
    </style>
</head>
<body>
    <header>
        <h2>TIMELESS</h2>
        <nav>
            <ul>
            <li><a href="Accueil.php" style="color:inherit; text-decoration:none;">ACCUEIL</a></li>
             <li><a href="#contact" style="color:inherit; text-decoration:none;">CONTACT</a></li>
            </ul>
        </nav>
    </header>

    <main class="login-container">
        <h1>Connexion</h1>
        
        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form class="login-form" method="POST">
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required 
                       placeholder="votre@email.com">
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required 
                       placeholder="********">
            </div>
            
            <button type="submit" class="login-button">
                <i class="fas fa-sign-in-alt"></i> Se connecter
            </button>
        </form>
        
        <div class="register-link">
            <p class="login-link">Pas encore de compte ? <a href="register.php" style="text-decoration: underline;">Créer un compte</a></p>
        </div>
    </main>

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
        </div>
    </footer>
</body>
</html>