<?php
// 1. Připojení k DB - skáčeme ze složky actions/ o úroveň výš
require_once '../includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $flight_id = $_POST['flight_id'];
    
    // Načtení dat z formuláře
    $fname = $_POST['passenger_first_name'];
    $lname = $_POST['passenger_last_name'];
    $email = $_POST['contact_email'];
    $phone = $_POST['contact_phone'];

    // Načtení ceny a info o letu
    $stmt = $pdo->prepare("SELECT f.*, a.name as airline_name FROM flights f JOIN airlines a ON f.airline_id = a.id WHERE f.id = ?");
    $stmt->execute([$flight_id]);
    $flight = $stmt->fetch();

    if ($flight) {
        // Spojíme vše do textového řetězce, který tvoje DB očekává ve sloupci 'flight_info'
        $flight_info = $flight['destination_from'] . " -> " . $flight['destination_to'] . " (Cestující: $fname $lname, Tel: $phone)";
        $price = $flight['price'];

        // OPRAVENO: Odstraněn 'flight_id' ze seznamu sloupců, aby to odpovídalo tvojí DB
        $ins = $pdo->prepare("INSERT INTO reservations (user_id, flight_info, price_paid, status) VALUES (?, ?, ?, 'Potvrzeno')");
        $ins->execute([$user_id, $flight_info, $price]);

        // Návrat o úroveň výš na seznam rezervací
        header("Location: ../manage_reservations.php?msg=booked");
        exit();
    } else {
        die("Chyba: Let nebyl nalezen.");
    }
} else {
    header("Location: ../index.php");
    exit();
}