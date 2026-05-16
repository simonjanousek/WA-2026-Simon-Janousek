<?php
session_start();
require_once '../includes/db.php';

// Kontrola role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("CHYBA: Do této sekce mají přístup pouze administrátoři!");
}

$id_to_delete = $_GET['id'];
// Logika pro smazání...
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id_to_delete]);

header("Location: manage_users.php?msg=Smazáno");