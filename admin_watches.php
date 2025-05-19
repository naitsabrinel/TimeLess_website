<?php
session_start();
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $idmontre = $_POST['idmontre'];
    $new_stock = (int)$_POST['new_stock'];
    
    try {
        $sql = "UPDATE MONTRE SET quantiteStock = ? WHERE idmontre = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$new_stock, $idmontre]);
        
        $_SESSION['success_message'] = "Stock mis à jour avec succès!";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Erreur lors de la mise à jour du stock: " . $e->getMessage();
    }
}

$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);

$sql = "SELECT * FROM MONTRE ORDER BY date_ajout DESC";
$stmt = $pdo->query($sql);
$montres = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Liste des Montres</title>
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
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #896045;
        }
        
        th {
            background-color: #896045;
            color: #fff;
            font-weight: bold;
        }
        
        tr:hover {
            background-color: #f9f9f9;
        }
        
        .action-btns {
            display: flex;
            gap: 10px;
        }
        
        .view-btn {
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            color: white;
            background-color: #17a2b8;
        }
        
        .view-btn:hover {
            opacity: 0.85;
        }
        
        .empty-message {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        
        .search-bar {
            margin-bottom: 20px;
            width: 100%;
            display: flex;
        }
        
        .search-bar input {
            flex-grow: 1;
            padding: 8px 12px;
            border: 1px solid #896045;
            border-radius: 4px 0 0 4px;
        }
        
        .search-bar button {
            padding: 8px 15px;
            background-color: #896045;
            color: white;
            border: none;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
        }
        
        .search-bar button:hover {
            background-color: #6d4b30;
        }
        
        .status-filter {
            display: flex;
            margin-bottom: 20px;
            gap: 10px;
        }
        
        .status-btn {
            padding: 8px 15px;
            background-color: #f8f8f8;
            border: 1px solid #896045;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .status-btn.active {
            background-color: #896045;
            color: white;
            border-color: #896045;
        }
        
        .status-btn:hover:not(.active) {
            background-color: #fff;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-pending {
            background-color: #ffc107;
            color: #212529;
        }
        
        .badge-completed {
            background-color: #28a745;
            color: white;
        }
        
        .badge-cancelled {
            background-color: #dc3545;
            color: white;
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
        <h1 class="admin-title">Liste des montres</h1>
        <a href="admin_dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Retour au tableau de bord</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Description</th>
                <th>Prix</th>
                <th>Couleur</th>
                <th>Catégorie</th>
                <th>Sexe</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($montres as $montre): ?>
                <tr>
                    <td><?= htmlspecialchars($montre['idmontre']) ?></td>
                    <td><?= htmlspecialchars($montre['titre']) ?></td>
                    <td><?= htmlspecialchars($montre['description']) ?></td>
                    <td><?= htmlspecialchars($montre['prix']) ?> </td>
                    <td><?= htmlspecialchars($montre['couleur']) ?></td>
                    <td><?= htmlspecialchars($montre['categorie']) ?></td>
                    <td><?= htmlspecialchars($montre['sexe']) ?></td>
                    <td><?= htmlspecialchars($montre['quantiteStock']) ?></td>
                    <td>
                        <form class="stock-form" method="POST">
                            <input type="hidden" name="idmontre" value="<?= $montre['idmontre'] ?>">
                            <input type="number" name="new_stock" class="stock-input" 
                                   value="<?= $montre['quantiteStock'] ?>" min="0">
                            <button type="submit" name="update_stock" class="update-btn">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>

</html>
