<?php
require_once __DIR__ . '/../includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $res_id = $_POST['res_id'];
    $current_admin_id = $_SESSION['user_id']; // ID toho, kdo teď klikl na "Uložit"
    $admin_role = $_SESSION['role'];
    
    $new_email = trim($_POST['contact_email']);
    $new_phone = trim($_POST['contact_phone']);

    // 1. Načtení rezervace
    $stmt = $pdo->prepare("SELECT flight_info, user_id FROM reservations WHERE id = ?");
    $stmt->execute([$res_id]);
    $res = $stmt->fetch();

    if ($res) {
        $target_user_id = $res['user_id']; // ID cestujícího, kterému letenka patří
        
        // Vyčištění a aktualizace textu letenky
        $base_route = explode(" | ", $res['flight_info'])[0];
        $updated_info = $base_route . " | Tel: $new_phone, Email: $new_email";
        
        $updateRes = $pdo->prepare("UPDATE reservations SET flight_info = ? WHERE id = ?");
        $updateRes->execute([$updated_info, $res_id]);

        $logDetails = "Změna kontaktů na: $new_email, $new_phone";

        // 2. Pokud je to ADMIN, může měnit i jméno v profilu uživatele
        if ($admin_role === 'admin' && isset($_POST['first_name']) && isset($_POST['last_name'])) {
            $fname = trim($_POST['first_name']);
            $lname = trim($_POST['last_name']);

            $updateUser = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ? WHERE id = ?");
            $updateUser->execute([$fname, $lname, $target_user_id]);
            
            $logDetails .= " | Změněno jméno cestujícího na: $fname $lname";
        }

        // 3. ZÁPIS DO HISTORIE - Tady se propisuje, KDO to udělal
        $logStmt = $pdo->prepare("
            INSERT INTO reservation_history (reservation_id, user_id, action_type, details, created_at) 
            VALUES (?, ?, 'Aktualizace údajů', ?, NOW())
        ");
        // Posíláme $current_admin_id, aby bylo vidět, že jsi to změnil ty
        $logStmt->execute([$res_id, $current_admin_id, $logDetails]);

        header("Location: ../manage_reservations.php?msg=updated");
        exit();
    }
}

header("Location: ../index.php");
exit();