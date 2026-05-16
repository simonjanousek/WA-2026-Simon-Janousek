<?php
// 1. Připojení k DB ze složky actions/ o úroveň výš
require_once '../includes/db.php';
session_start();

// Kontrola, zda je uživatel přihlášen a zda poslal data formulářem
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $flight_id = $_POST['flight_id'];
    
    // Načtení dat z formuláře
    $fname = $_POST['passenger_first_name'];
    $lname = $_POST['passenger_last_name'];
    $email = $_POST['contact_email'];
    $phone = $_POST['contact_phone'];

    // Načtení ceny a info o letu (propojujeme s aerolinkou)
    $stmt = $pdo->prepare("SELECT f.*, a.name as airline_name FROM flights f JOIN airlines a ON f.airline_id = a.id WHERE f.id = ?");
    $stmt->execute([$flight_id]);
    $flight = $stmt->fetch();

    if ($flight) {
        // SESTAVENÍ INFO TEXTU (včetně emailu a názvu aerolinky)
        $flight_info = $flight['airline_name'] . ": " . $flight['destination_from'] . " -> " . $flight['destination_to'] . 
                       " (Cestující: $fname $lname, Email: $email, Tel: $phone)";
        
        $price = $flight['price'];

        // Vložení do tabulky reservations
        $ins = $pdo->prepare("INSERT INTO reservations (user_id, flight_info, price_paid, status) VALUES (?, ?, ?, 'Potvrzeno')");
        $ins->execute([$user_id, $flight_info, $price]);

        // Úspěch -> přesměrování
        header("Location: ../manage_reservations.php?msg=booked");
        exit();
    } else {
        // Let neexistuje
        die("Chyba: Let nebyl nalezen.");
    }

} else {
    // Pokud někdo přistoupí přímo na skript bez POSTu, vraceni na index
    header("Location: ../index.php");
    exit();
}