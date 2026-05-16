<?php
require_once '../includes/db.php';
session_start();

// Ochrana: Jen Admin a Editor
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'editor')) {
    die("Nepovolený přístup.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_rebook'])) {
    $res_id = $_POST['res_id'];
    $new_flight_id = $_POST['new_flight_id'];

    // 1. Načtení staré rezervace
    $stmt = $pdo->prepare("SELECT price_paid FROM reservations WHERE id = ?");
    $stmt->execute([$res_id]);
    $old_res = $stmt->fetch();

    // 2. Načtení nového letu
    $stmtF = $pdo->prepare("SELECT f.*, a.name as airline_name FROM flights f JOIN airlines a ON f.airline_id = a.id WHERE f.id = ?");
    $stmtF->execute([$new_flight_id]);
    $nf = $stmtF->fetch();

    if ($nf && $old_res) {
        $stara_cena = $old_res['price_paid'];
        $nova_cena = $nf['price'];
        $rozdil = $nova_cena - $stara_cena;
        $diff_text = ($rozdil >= 0) ? "Doplatek: +" . $rozdil . " Kč" : "Přeplatek: " . abs($rozdil) . " Kč";

        $new_info = "REBOOKED: " . $nf['airline_name'] . " | " . $nf['destination_from'] . " -> " . $nf['destination_to'] . " (" . date('d.m. H:i', strtotime($nf['departure_time'])) . ")";

      
        // Update v DB - PŘIDÁME RESET STAVU NA 'Potvrzeno'
$update = $pdo->prepare("UPDATE reservations SET flight_info = ?, price_paid = ?, status = 'Potvrzeno' WHERE id = ?");
$update->execute([$new_info, $nova_cena, $res_id]);

        // Zápis do historie
        $log_details = "Původní cena: $stara_cena Kč -> Nová cena: $nova_cena Kč. ($diff_text). Let změněn na: $new_info";
        logChange($pdo, $res_id, $_SESSION['user_id'], "Rebooking", $log_details);
        
        header("Location: ../edit_reservation.php?res_id=$res_id&msg=updated");
        exit();
    }
}
header("Location: ../index.php");