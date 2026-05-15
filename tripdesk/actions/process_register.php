<?php
require_once __DIR__ . '/../includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username']);
    $pass = $_POST['password'];
    $email = trim($_POST['email']);
    $fname = trim($_POST['first_name']);
    $lname = trim($_POST['last_name']);

    // 1. KONTROLA: Malé, Velké, Číslo, Speciální znak, min. 8 znaků
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';

    if (!preg_match($pattern, $pass)) {
        header("Location: ../register.php?error=weak_password");
        exit();
    }

    // 2. KONTROLA: Zda uživatel nebo email už neexistuje
    $check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check->execute([$user, $email]);
    if ($check->fetch()) {
        header("Location: ../register.php?error=exists");
        exit();
    }

    // 3. Hashování a uložení
    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, first_name, last_name, role) VALUES (?, ?, ?, ?, ?, 'user')");
        $stmt->execute([$user, $hashed_pass, $email, $fname, $lname]);

        header("Location: ../login.php?msg=registrace_ok");
        exit();
    } catch (PDOException $e) {
        header("Location: ../register.php?error=db_error");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}