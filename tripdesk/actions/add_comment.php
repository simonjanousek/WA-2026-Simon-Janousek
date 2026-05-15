<?php
// Zapneme hlášení chyb pro vývoj
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. OPRAVA CESTY: Skáčeme o úroveň výš do includes
require_once __DIR__ . '/../includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $airline_id = $_POST['airline_id'] ?? null;
    $user_id = $_SESSION['user_id'];
    $content = trim($_POST['content'] ?? '');

    if ($airline_id && !empty($content)) {
        try {
            // Vložení do DB
            $stmt = $pdo->prepare("INSERT INTO airline_comments (airline_id, user_id, comment_text) VALUES (?, ?, ?)");
            $stmt->execute([$airline_id, $user_id, $content]);

            // 2. OPRAVA PŘESMĚROVÁNÍ: Musíme se vrátit o úroveň výš (../)
            header("Location: ../airline_details.php?id=" . $airline_id . "&msg=comment_added");
            exit();
        } catch (PDOException $e) {
            die("Databázová chyba: " . $e->getMessage());
        }
    } else {
        die("Chyba: Obsah komentáře nesmí být prázdný.");
    }
} else {
    // Návrat na hlavní stránku (opět o úroveň výš)
    header("Location: ../index.php");
    exit();
}