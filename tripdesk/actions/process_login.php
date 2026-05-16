<?php
// Použijeme __DIR__, abychom se vyhnuli problémům s cestou v XAMPPu
require_once __DIR__ . '/../includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // 1. Najdeme uživatele v DB
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // 2. Ověření hesla (funguje pro password_hash z registrace)
    if ($user && password_verify($password, $user['password'])) {
        
        // Nastavení session údajů
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role']; 
        $_SESSION['email'] = $user['email']; // Užitečné pro formuláře

        // Úspěch: Skočíme o složku výš do kořenu k indexu
        header("Location: ../index.php");
        exit();
    } else {
        // Chyba: Skočíme o složku výš zpět na login s chybou
        header("Location: ../login.php?error=invalid_credentials");
        exit();
    }
} else {
    // Pokud někdo přistoupí přímo, hodíme ho na index
    header("Location: ../index.php");
    exit();
}