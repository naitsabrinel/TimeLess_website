<?php
session_start();
require 'includes/db.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Admin - Timeless</title>
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
        
        .admin-title {
            color: #896045;
            margin: 0;
        }
        
        .admin-menu {
            display: grid;
            grid-template-columns: repeat(3, 1fr);     
            gap: 20px;
            margin-top: 20px;
        }
        
        .menu-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .menu-icon {
            font-size: 2.5rem;
            color: #896045;
            margin-bottom: 15px;
        }
        
        .menu-title {
            color: #333;
            font-size: 1.2rem;
            margin-bottom: 10px;
        }
        
        .menu-desc {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }
        
        .menu-btn {
            display: inline-block;
            padding: 8px 15px;
            background-color: #896045;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .menu-btn:hover {
            background-color: #896045;
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
    </style>
</head>
<body>

<header>
    <h2>TIMELESS ADMIN</h2>
    <nav>
        <ul>
            <li><a href="logout.php" style="color:inherit; text-decoration:none;">DÉCONNEXION</a></li>
        </ul>
    </nav>
</header>

<div class="admin-container">
    <div class="admin-header">
        <h1 class="admin-title">Tableau de Bord Administrateur</h1>
    </div>
    
    <?php if(isset($_SESSION['success_message'])): ?>
        <div class="notification success"><?= htmlspecialchars($_SESSION['success_message']) ?></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['error_message'])): ?>
        <div class="notification error"><?= htmlspecialchars($_SESSION['error_message']) ?></div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    
    <div class="admin-menu">
        <div class="menu-card">
            <div class="menu-icon"><i class="fas fa-users"></i></div>
            <h3 class="menu-title">Gestion des Utilisateurs</h3>
            <p class="menu-desc">Voir et gérer tous les utilisateurs enregistrés</p>
            <a href="admin_users.php" class="menu-btn">Accéder</a>
        </div>
        
        <div class="menu-card">
            <div class="menu-icon"><i class="fas fa-shopping-bag"></i></div>
            <h3 class="menu-title">Gestion des Commandes</h3>
            <p class="menu-desc">Voir et gérer toutes les commandes</p>
            <a href="admin_orders.php" class="menu-btn">Accéder</a>
        </div>
        
        <div class="menu-card">
            <div class="menu-icon"><i class="fas fa-clock"></i></div>
            <h3 class="menu-title">Gestion des Montres</h3>
            <p class="menu-desc">Ajouter, modifier ou supprimer des montres</p>
            <a href="admin_add_watch.php" class="menu-btn">Ajouter</a>
            <a href="admin_watches.php" class="menu-btn">Accéder</a>
        </div>
        
     
    </div>
</div>


</body>
</html>