<?php
session_start();

require 'includes/db.php'; 
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $couleur = $_POST['couleur'];
    $categorie = $_POST['categorie'];
    $sexe = $_POST['sexe'];
    $quantiteStock = $_POST['quantiteStock'];

 if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $uploadsDir = 'uploads/'; // Dossier pour stocker les images
    $filename = basename($_FILES['image']['name']);
    $targetPath = $uploadsDir . $filename;
    $absolutePath = __DIR__ . '/' . $targetPath;

     if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0755, true);
    }

     if (move_uploaded_file($_FILES['image']['tmp_name'], $absolutePath)) {
        $imagePath = $filename; // <- stocke uniquement le nom du fichier
    } else {
        $_SESSION['error_message'] = "Erreur lors de l'envoi de l'image.";
        header('Location: admin_add_watch.php');
        
        exit();
    }
} else {
    $_SESSION['error_message'] = "Veuillez télécharger une image pour la montre.";
    header('Location: admin_add_watch.php');
    exit();
}
    try {
        $sql = "INSERT INTO MONTRE (titre, description, prix, couleur, categorie, sexe, pathImage, quantiteStock) 
                VALUES (:titre, :description, :prix, :couleur, :categorie, :sexe, :pathImage, :quantiteStock)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':titre' => $titre,
            ':description' => $description,
            ':prix' => $prix,
            ':couleur' => $couleur,
            ':categorie' => $categorie,
            ':sexe' => $sexe,
            ':pathImage' => $imagePath,
            ':quantiteStock' => $quantiteStock
        ]);
        $success = "Ajout réussie ! ";
      
    } catch (PDOException $e) {
        $errors[] = "Erreur lors de l'ajout' : " . $e->getMessage();
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
            background-color: #fff;
            margin: 0;
            padding: 0;
        }
        
        nav ul {
            list-style: none;
            padding: 0;
            margin: 10px 0 0;
        }
        nav ul li {
            display: inline;
            margin: 0 10px;
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
        input[type="number"],
        input[type="file"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            background-color: #fff;
        }
        textarea {
            resize: vertical;
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
        .notification {
            padding: 12px;
            border-radius: 4px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .notification.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .notification.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
         .back-btn {
            background-color: #896045;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
        }
        
        .back-btn:hover {
            background-color: #6d4b30;
        }
            .admin-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
         .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }
        
    </style>
</head>
<body>

<header>
    <h2>TIMELESS ADMIN</h2>
    <nav>
        <ul>
            <li><a href="admin_dashboard.php" style="color:inherit; text-decoration:none;">TABLEAU DE BORD</a></li>
            <li><a href="logout.php" style="color:inherit; text-decoration:none;">DÉCONNEXION</a></li>
        </ul>
    </nav>
</header>

<div class="admin-container">
   <div class="admin-header">
        <h1 >Ajouter une nouvelle montre</h1>
        <a href="admin_dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Retour au tableau de bord</a>
    </div>
    
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
        

    <form action="admin_add_watch.php" method="post" enctype="multipart/form-data">
        <label for="titre">Titre</label>
        <input type="text" name="titre" id="titre" required>

        <label for="description">Description</label>
        <textarea name="description" id="description" required></textarea>

        <label for="prix">Prix</label>
        <input type="number" name="prix" id="prix" step="0.01" required>

        <label for="couleur">Couleur</label>
        <input type="text" name="couleur" id="couleur">

        <label for="categorie">Catégorie</label>
        <select name="categorie" id="categorie" required>
            <option value="classique">Classique</option>
            <option value="sport">Sport</option>
            <option value="luxe">Luxe</option>
        </select>

        <label for="sexe">Sexe</label>
        <select name="sexe" id="sexe" required>
            <option value="HOMME">Homme</option>
            <option value="FEMME">Femme</option>
            <option value="MIX">Mixte</option>
        </select>

        <label for="quantiteStock">Quantité en Stock</label>
        <input type="number" name="quantiteStock" id="quantiteStock" required>

        <label for="image">Image de la Montre</label>
        <input type="file" name="image" id="image" accept="*" required>

        <button type="submit">Ajouter la Montre</button>
    </form>
</div>

</body>
</html>
