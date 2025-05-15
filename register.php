<?php
session_start();
require 'includes/db.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $datedenaissance = $_POST['datedenaissance'] ?? '';
    $numeroTel = trim($_POST['numeroTel'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');

    if (empty($nom)) $errors[] = "Le nom est obligatoire";
    if (empty($prenom)) $errors[] = "Le prénom est obligatoire";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide";
    if (strlen($password) < 6) $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
    if ($password !== $confirm_password) $errors[] = "Les mots de passe ne correspondent pas";
    if (!preg_match('/^[0-9]{10}$/', $numeroTel)) $errors[] = "Numéro de téléphone invalide";
    if (empty($adresse)) $errors[] = "L'adresse est obligatoire";

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT email FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) $errors[] = "Cet email est déjà utilisé";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO utilisateur 
                                  (nom, prenom, email, motdepasse, datedenaissance, numeroTel, adresse) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $nom,
                $prenom,
                $email,
                $password,  
                $datedenaissance ?: null,
                $numeroTel,
                $adresse
            ]);
            
            $success = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'inscription : " . $e->getMessage();
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
        body {
            font-family: 'Georgia', serif;
            line-height: 1.6;
            background-color: #ffff;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #896045;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="date"],
        input[type="tel"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .error {
            color: red;
            font-size: 0.9em;
        }
        .success {
            color: green;
            font-size: 1.1em;
            text-align: center;
            margin-bottom: 15px;
        }
        button {
            background-color: #896045;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        button:hover {
            background-color: #6d4b30;
        }
        .login-link {
            text-align: center;
            margin-top: 15px;
            
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
             <li><a href="#contact" style="color:inherit; text-decoration:none;">CONTACT</a></li>
            </ul>
        </nav>
    </header>


    <div class="container">
        <h1>Inscription</h1>
        
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    <p><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="nom">Nom *</label>
                <input type="text" id="nom" name="nom" required value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="prenom">Prénom *</label>
                <input type="text" id="prenom" name="prenom" required value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe (6 caractères minimum) *</label>
                <input type="password" id="password" name="password" required minlength="6">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe *</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
            </div>
            
            <div class="form-group">
                <label for="datedenaissance">Date de naissance</label>
                <input type="date" id="datedenaissance" name="datedenaissance" value="<?= htmlspecialchars($_POST['datedenaissance'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="numeroTel">Numéro de téléphone *</label>
                <input type="tel" id="numeroTel" name="numeroTel" required 
                       pattern="[0-9]{10}" title="10 chiffres"
                       value="<?= htmlspecialchars($_POST['numeroTel'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="adresse">Adresse *</label>
                <textarea id="adresse" name="adresse" required><?= htmlspecialchars($_POST['adresse'] ?? '') ?></textarea>
            </div>
            
            <button type="submit">S'inscrire</button>
        </form>
        
        <div class="login-link">
            Déjà inscrit ? <a href="login.php" style="text-decoration: underline;">Connectez-vous ici</a>
        </div>
    </div>
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