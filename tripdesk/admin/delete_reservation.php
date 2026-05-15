<?php
// admin/delete_reservation.php
require_once __DIR__ . '/../includes/db.php';
session_start();

// 1. Ochrana: Jen Admin může mazat
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// 2. Chytáme parametr 'id'
$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $pdo->beginTransaction();

        // Smažeme historii (Change Log)
        $stmt1 = $pdo->prepare("DELETE FROM reservation_history WHERE reservation_id = ?");
        $stmt1->execute([$id]);

        // Smažeme samotnou rezervaci
        $stmt2 = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
        $stmt2->execute([$id]);

        $pdo->commit();
        
        // Vracíme se na seznam rezervací (který je o úroveň výš)
        header("Location: ../manage_reservations.php?msg=deleted");
        exit();

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        die("Chyba při mazání: " . $e->getMessage());
    }
} else {
    header("Location: ../manage_reservations.php");
    exit();
}