<?php
// 1. Připojení k DB -  ze složky actions/ o úroveň výš
require_once '../includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $airline_id = $_POST['airline_id'] ?? null;
    $rating = $_POST['rating'] ?? 5;
    $comment = $_POST['comment'] ?? '';

    if ($airline_id) {
        try {
            // Uložení komentáře do databáze
            $stmt = $pdo->prepare("INSERT INTO airline_reviews (airline_id, user_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$airline_id, $user_id, $rating, $comment]);

            // Přesměrování zpět na detail aerolinky o úroveň výš
            header("Location: ../airline_details.php?id=" . $airline_id . "&msg=comment_added");
            exit();
        } catch (PDOException $e) {
            die("Chyba při ukládání komentáře: " . $e->getMessage());
        }
    } else {
        header("Location: ../index.php");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}