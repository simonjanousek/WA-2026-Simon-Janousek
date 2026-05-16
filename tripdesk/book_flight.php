<?php
require_once 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Pro nákup se musíš přihlásit!");
}

$flight_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

if ($flight_id) {
    // 1. Zjistíme info o letu z tabulky flights
    $stmt = $pdo->prepare("SELECT * FROM flights WHERE id = ?");
    $stmt->execute([$flight_id]);
    $flight = $stmt->fetch();

    if ($flight) {
        // 2. Vložíme rezervaci
        $info = $flight['destination_from'] . " -> " . $flight['destination_to'];
        $price = $flight['price'];

        $ins = $pdo->prepare("INSERT INTO reservations (user_id, flight_info, price_paid, status) VALUES (?, ?, ?, 'confirmed')");
        $ins->execute([$user_id, $info, $price]);

        header("Location: manage_reservations.php?msg=booked");
        exit();
    }
}