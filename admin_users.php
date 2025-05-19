<?php
session_start();
require 'includes/db.php';


if(isset($_POST['delete_user']) && isset($_POST['user_id'])) {
    $user_id = (int)$_POST['user_id'];
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM COMMANDE WHERE idutilisateur = ?");
        $stmt->execute([$user_id]);
        $hasOrders = $stmt->fetchColumn() > 0;
        
        if($hasOrders) {
            $_SESSION['error_message'] = "Impossible de supprimer cet utilisateur car il a des commandes associées.";
        } else {
            $stmt = $pdo->prepare("DELETE FROM PANIER WHERE idutilisateur = ?");
            $stmt->execute([$user_id]);
         
            $stmt = $pdo->prepare("DELETE FROM UTILISATEUR WHERE idutilisateur = ?");
            $stmt->execute([$user_id]);
            
            $_SESSION['success_message'] = "Utilisateur supprimé avec succès.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Erreur lors de la suppression : " . $e->getMessage();
    }
    
    header('Location: admin_users.php');
    exit();
}

try {
    $stmt = $pdo->query("SELECT * FROM UTILISATEUR ORDER BY date_inscription DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $users = [];
    $_SESSION['error_message'] = "Erreur lors de la récupération des utilisateurs : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs - Timeless Admin</title>
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
        
        .view-btn, .edit-btn, .delete-btn {
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            color: white;
        }
        
        .view-btn {
            background-color: #17a2b8;
        }
        
        .edit-btn {
            background-color: #ffc107;
            color: #212529;
        }
        
        .delete-btn {
            background-color: #dc3545;
        }
        
        .view-btn:hover, .edit-btn:hover, .delete-btn:hover {
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
            border: 1px solid #ddd;
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
            background-color: #896045;
        }
        
        .modal-bg {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 95%;
            max-width: 400px;
            text-align: center;
        }
        
        .modal-title {
            color: #896045;
            margin-top: 0;
        }
        
        .modal-buttons {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }
        
        .modal-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
        }
        
        .confirm-btn {
            background-color: #dc3545;
        }
        
        .cancel-btn {
            background-color: #6c757d;
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
        <h1 class="admin-title">Gestion des Utilisateurs</h1>
        <a href="admin_dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Retour au tableau de bord</a>
    </div>
    
    <?php if(isset($_SESSION['success_message'])): ?>
        <div class="notification success"><?= htmlspecialchars($_SESSION['success_message']) ?></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['error_message'])): ?>
        <div class="notification error"><?= htmlspecialchars($_SESSION['error_message']) ?></div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Rechercher un utilisateur...">
        <button onclick="searchTable()"><i class="fas fa-search"></i></button>
    </div>
    
    <?php if(empty($users)): ?>
        <div class="empty-message">
            <p>Aucun utilisateur trouvé.</p>
        </div>
    <?php else: ?>
        <table id="usersTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Adress</th>
                    <th>Date d'inscription</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $user): ?>
                     <?php if((int)$user['idutilisateur'] === 1) continue; ?>
                <tr>
                    <td><?= (int)$user['idutilisateur'] ?></td>
                    <td><?= htmlspecialchars($user['nom']) ?></td>
                    <td><?= htmlspecialchars($user['prenom']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['numeroTel']) ?></td>
                    <td><?= htmlspecialchars($user['adresse']) ?></td>
                    <td><?= htmlspecialchars($user['date_inscription']) ?></td>
                    <td class="action-btns">
                
                        <button class="delete-btn" onclick="confirmDelete(<?= (int)$user['idutilisateur'] ?>)"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div id="deleteModal" class="modal-bg">
    <div class="modal-content">
        <h3 class="modal-title">Confirmer la suppression</h3>
        <p>Êtes-vous sûr de vouloir supprimer cet utilisateur ?</p>
        <p>Cette action est irréversible.</p>
        
        <div class="modal-buttons">
            <form id="deleteForm" method="post" action="admin_users.php">
                <input type="hidden" id="user_id" name="user_id" value="">
                <input type="hidden" name="delete_user" value="1">
                <button type="submit" class="modal-btn confirm-btn">Supprimer</button>
            </form>
            <button class="modal-btn cancel-btn" onclick="closeModal()">Annuler</button>
        </div>
    </div>
</div>

<script>
    function searchTable() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        const table = document.getElementById('usersTable');
        const rows = table.getElementsByTagName('tr');
        
        for (let i = 1; i < rows.length; i++) {  
            const cells = rows[i].getElementsByTagName('td');
            let found = false;
            
            for (let j = 0; j < cells.length - 1; j++) { 
                const cellText = cells[j].textContent.toLowerCase();
                if (cellText.includes(input)) {
                    found = true;
                    break;
                }
            }
            
            rows[i].style.display = found ? '' : 'none';
        }
    }
    
   
    
    function confirmDelete(userId) {
        document.getElementById('user_id').value = userId;
        document.getElementById('deleteModal').style.display = 'flex';
    }
    
    function closeModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }
    
     window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target === modal) {
            closeModal();
        }
    };
</script>



</body>
</html>