<?php
session_start();
require 'includes/db.php';

 
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';
$whereClause = "";
$params = [];

if($statusFilter !== 'all') {
    $whereClause = " WHERE c.statut = ?";
    $params[] = $statusFilter;
}

try {
     
    $sql = "SELECT c.idcommande, c.datecommande, c.statut, 
                   u.nom, u.prenom, u.email,
                   COALESCE(SUM(cd.quantite * cd.prixunitaire), 0) as montant_total
            FROM COMMANDE c 
            JOIN UTILISATEUR u ON c.idutilisateur = u.idutilisateur
            LEFT JOIN COMMANDE_DETAIL cd ON c.idcommande = cd.idcommande" . 
            $whereClause . "
            GROUP BY c.idcommande, c.datecommande, c.statut, u.nom, u.prenom, u.email
            ORDER BY c.datecommande DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $orders = [];
    $_SESSION['error_message'] = "Erreur lors de la récupération des commandes : " . $e->getMessage();
}

if (isset($_GET['confirm'])) {
    $idCommande = (int)$_GET['confirm'];
    $statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all'; 

    try {
        $checkSql = "SELECT statut FROM COMMANDE WHERE idcommande = ?";
        $stmtCheck = $pdo->prepare($checkSql);
        $stmtCheck->execute([$idCommande]);
        $status = $stmtCheck->fetchColumn();

        if ($status === 'en attente') {
            $updateSql = "UPDATE COMMANDE SET statut = 'faite' WHERE idcommande = ?";
            $stmtUpdate = $pdo->prepare($updateSql);

            if ($stmtUpdate->execute([$idCommande])) {
                $_SESSION['success_message'] = "Commande #$idCommande confirmée avec succès.";
            } else {
                $_SESSION['error_message'] = "Erreur lors de la confirmation de la commande #$idCommande.";
            }
        } else {
            $_SESSION['error_message'] = "Commande #$idCommande n'est pas en attente ou a déjà été traitée.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Erreur lors du traitement de la commande : " . $e->getMessage();
    }

    header("Location: admin_orders.php?status=$statusFilter");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Commandes - Timeless Admin</title>
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
        <h1 class="admin-title">Gestion des Commandes</h1>
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
    
     <!-- Filtres par statut -->
        <div class="status-filter">
            <a href="?status=all" class="status-btn <?php if($statusFilter === 'all') echo 'active'; ?>">Toutes</a>
            <a href="?status=en attente" class="status-btn <?php if($statusFilter === 'en attente') echo 'active'; ?>">En attente</a>
            <a href="?status=faite" class="status-btn <?php if($statusFilter === 'faite') echo 'active'; ?>">Faite</a>
            <a href="?status=annulée" class="status-btn <?php if($statusFilter === 'annulée') echo 'active'; ?>">Annulée</a>
        </div>
    
 
<?php if(empty($orders)): ?>
    <div class="empty-message">
        <p>Aucune commande trouvée.</p>
    </div>
<?php else: ?>
    <table id="ordersTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Client</th>
                <th>Date</th>
                <th>Montant</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($orders as $order): 
                $badgeClass = '';
                
                switch(strtolower($order['statut'])) {
                    case 'en attente':
                        $badgeClass = 'badge-pending';
                        break;
                    case 'faite':
                        $badgeClass = 'badge-completed';
                        break;
                    case 'annulée':
                        $badgeClass = 'badge-cancelled';
                        break;
                }
            ?>
            <tr>
                <td><?= (int)$order['idcommande'] ?></td>
                <td>
                    <?= htmlspecialchars($order['nom']) ?> <?= htmlspecialchars($order['prenom']) ?><br>
                    <small><?= htmlspecialchars($order['email']) ?></small>
                </td>
                <td><?= htmlspecialchars($order['datecommande']) ?></td>
                <td><?= number_format($order['montant_total'], 2, ',', ' ') ?> DA</td>
                <td>
                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($order['statut']) ?></span>
                    <?php if (strtolower($order['statut']) === 'en attente'): ?>
                        <a href="admin_orders.php?confirm=<?= $order['idcommande'] ?>" title="Confirmer la commande" style="margin-left:10px; color:green;">
                            <i class="fas fa-check-circle fa-lg"></i>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</div>

</body>
</html>