<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['idcommande'])) {
    header('Location: historique.php');
    exit;
}

$idcommande = $_POST['idcommande'];
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("UPDATE COMMANDE SET statut = 'annulée' WHERE idcommande = ? AND idutilisateur = ?");
$stmt->execute([$idcommande, $user_id]);

header("Location: historique_commande.php");exit;
?>
