<?php
// admin/delete_comment.php

// 1. Načtení databáze (z admin o úroveň výš)
require_once __DIR__ . '/../includes/db.php';
session_start();

// 2.  je uživatel skutečně ADMIN ?
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Chyba: Nemáte oprávnění k této akci.");
}

// 3. Získání ID komentáře a aerolinky z URL
$comment_id = $_GET['id'] ?? null;
$airline_id = $_GET['airline_id'] ?? null;

if ($comment_id) {
    try {
        // 4. Smazání z databáze
        $stmt = $pdo->prepare("DELETE FROM airline_comments WHERE id = ?");
        $stmt->execute([$comment_id]);

        // 5. Úspěšný návrat o úroveň výš na detail aerolinky
        if ($airline_id) {
            header("Location: ../airline_details.php?id=" . $airline_id . "&msg=deleted");
        } else {
            header("Location: ../index.php");
        }
        exit();

    } catch (PDOException $e) {
        die("Chyba při mazání: " . $e->getMessage());
    }
} else {
    // Pokud chybí ID, návrat na hlavní stranu
    header("Location: ../index.php");
    exit();
}