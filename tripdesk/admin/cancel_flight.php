<?php
// admin/cancel_flight.php
require_once __DIR__ . '/../includes/db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Nepovolený přístup.");
}

$flight_id = $_GET['id'] ?? null;

if ($flight_id) {
    try {
        $pdo->beginTransaction();

        // 1. Změníme stav LETU (tohle funguje určitě)
        $stmt_f = $pdo->prepare("UPDATE flights SET status = 'Zrušeno' WHERE id = ?");
        $stmt_f->execute([$flight_id]);

        // 2. Zjistíme detaily letu
        $stmt_info = $pdo->prepare("SELECT destination_from, destination_to FROM flights WHERE id = ?");
        $stmt_info->execute([$flight_id]);
        $flight = $stmt_info->fetch();

        if ($flight) {
            // Zkusíme najít sloupce v tabulce reservations, abychom věděli, co updatovat
            $q = $pdo->query("DESCRIBE reservations");
            $columns = $q->fetchAll(PDO::FETCH_COLUMN);

            // Určíme, jak se jmenuje sloupec pro stav (status vs stav)
            $statusColumn = in_array('status', $columns) ? 'status' : (in_array('stav', $columns) ? 'stav' : null);
            
            // Určíme, jak se jmenuje sloupec pro info o letu
            $infoColumn = in_array('flight_info', $columns) ? 'flight_info' : (in_array('info', $columns) ? 'info' : null);

            if ($statusColumn && $infoColumn) {
                // Sestavíme vyhledávací řetězec - velmi volný, aby se trefil
                $from = $flight['destination_from'];
                $to = $flight['destination_to'];
                
                // Update všech rezervací, kde se vyskytuje Odkud i Kam
                $sql_r = "UPDATE reservations SET $statusColumn = 'Zrušeno' 
                          WHERE $infoColumn LIKE :from AND $infoColumn LIKE :to";
                
                $stmt_r = $pdo->prepare($sql_r);
                $stmt_r->execute([
                    ':from' => "%$from%",
                    ':to' => "%$to%"
                ]);
            }
        }

        $pdo->commit();
        header("Location: ../index.php?msg=cancelled");
        exit();

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        die("Kritická chyba: " . $e->getMessage());
    }
} else {
    header("Location: ../index.php");
    exit();
}