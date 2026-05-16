<?php
// admin/cancel_flight.php
require_once __DIR__ . '/../includes/db.php';
session_start();

// Ochrana přístupu
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Nepovolený přístup.");
}

$flight_id = $_GET['id'] ?? null;

if ($flight_id) {
    try {
        $pdo->beginTransaction();

        // 1. Zrušíme let v tabulce flights
        $stmt_f = $pdo->prepare("UPDATE flights SET status = 'Zrušeno' WHERE id = ?");
        $stmt_f->execute([$flight_id]);

        // 2. Získani deatilu pro oznaceni techto rezervaci
        $stmt_info = $pdo->prepare("SELECT destination_from, destination_to FROM flights WHERE id = ?");
        $stmt_info->execute([$flight_id]);
        $flight = $stmt_info->fetch();

        if ($flight) {
            // 3. update vsech se stejnym flight_info
            $sql_r = "UPDATE reservations 
                      SET status = 'Zrušeno' 
                      WHERE flight_info LIKE :from AND flight_info LIKE :to";
            
            $stmt_r = $pdo->prepare($sql_r);
            $stmt_r->execute([
                ':from' => "%" . $flight['destination_from'] . "%",
                ':to'   => "%" . $flight['destination_to'] . "%"
            ]);
        }

        // Potvrzeni zmen v db
        $pdo->commit();
        header("Location: ../index.php?msg=cancelled");
        exit();

    } catch (PDOException $e) {
        // kdyz chyva tak zpet
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        die("Kritická chyba při rušení letu: " . $e->getMessage());
    }
} else {
    // Pokud chybí ID, vracíme na úvod
    header("Location: ../index.php");
    exit();
}