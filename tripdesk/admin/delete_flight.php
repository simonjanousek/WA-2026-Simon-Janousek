<?php
require_once '../includes/db.php';
session_start();

// jen admin smí mazat
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Nepovolený přístup.");
}

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        // Smažeme let z tabulky flights
        $stmt = $pdo->prepare("DELETE FROM flights WHERE id = ?");
        $stmt->execute([$id]);
        
        // Přesměrování zpět na seznam letů s potvrzením
        header("Location: ../index.php?msg=flight_deleted");
        exit();
    } catch (PDOException $e) {
        die("Chyba při mazání letu: " . $e->getMessage());
    }
} else {
    header("Location: ../index.php");
    exit();
}