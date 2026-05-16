<?php
require_once '../includes/db.php';
session_start();

// 1. přístup mají jen Admin a Editor
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'editor')) {
    die("Nepovolený přístup. Pro tuto akci musíte být personál.");
}

$res_id = $_GET['res_id'] ?? null;
$msg = "";
$error = "";

// 2. Zpracování nahrání pasu (Check-in)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['passport'])) {
    $res_id = $_POST['res_id'];
    
    if ($_FILES['passport']['error'] === 0) {
        $target_dir = "../assets/passports/";
        
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $extension = pathinfo($_FILES["passport"]["name"], PATHINFO_EXTENSION);
        $file_name = "passport_" . $res_id . "_" . time() . "." . $extension;
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["passport"]["tmp_name"], $target_file)) {
            $db_path = "assets/passports/" . $file_name;
            
            try {
                // START TRANSAKCE
                $pdo->beginTransaction();

                // AKTUALIZACE: Status se mění na 'Checked-in'
                $stmt = $pdo->prepare("UPDATE reservations SET passport_photo = ?, status = 'Checked-in' WHERE id = ?");
                $stmt->execute([$db_path, $res_id]);
                
                // --- NOVÉ: ZÁPIS DO HISTORIE (AUDIT LOG) ---
                $details = "Úspěšné odbavení. Byl nahrán sken pasu a stav změněn na Checked-in.";
                logChange($pdo, $res_id, $_SESSION['user_id'], "Check-in", $details);
                // -------------------------------------------

                $pdo->commit();
                $msg = "Odbavení proběhlo úspěšně! Status změněn na: Checked-in.";
            } catch (Exception $e) {
                // Pokud se něco nepovede, vrátíme změny zpět
                $pdo->rollBack();
                $error = "Chyba databáze: " . $e->getMessage();
            }
            
        } else {
            $error = "Chyba při přesunu souboru do složky.";
        }
    } else {
        $error = "Chyba při nahrávání souboru. Zkuste to znovu.";
    }
}

// 3. Načtení dat o rezervaci pro zobrazení ve formuláři
$res = null;
if ($res_id) {
    $stmt = $pdo->prepare("SELECT r.*, u.username, u.first_name, u.last_name FROM reservations r JOIN users u ON r.user_id = u.id WHERE r.id = ?");
    $stmt->execute([$res_id]);
    $res = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Odbavení - TripDesk Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .checkin-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 500px; margin: 40px auto; }
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; text-align: center; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info-row { margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div style="margin-top: 20px;">
            <a href="../manage_reservations.php" style="text-decoration: none; color: #3498db; font-weight: bold;">← Zpět na seznam rezervací</a>
        </div>

        <div class="checkin-card">
            <h2 style="margin-top: 0; color: #2c3e50; text-align: center;">🛂 Odbavení (Check-in)</h2>

            <?php if ($msg): ?>
                <div class="alert alert-success"><?= $msg ?></div>
                <div style="text-align:center;">
                    <a href="../manage_reservations.php" class="btn" style="background: #2c3e50;">Hotovo</a>
                </div>
            <?php elseif ($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>

            <?php if ($res && !$msg): ?>
                <div class="info-row">
                    <strong>Rezervace:</strong> #<?= $res['id'] ?>
                </div>
                <div class="info-row">
                    <strong>Cestující:</strong> <?= htmlspecialchars($res['first_name'] . " " . $res['last_name']) ?> (<?= htmlspecialchars($res['username']) ?>)
                </div>
                <div class="info-row" style="margin-bottom: 25px;">
                    <strong>Aktuální stav:</strong> 
                    <span style="color: <?= ($res['status'] == 'Checked-in' ? '#27ae60' : '#e67e22') ?>; font-weight: bold;">
                        <?= htmlspecialchars($res['status']) ?>
                    </span>
                </div>

                <?php if ($res['status'] !== 'Checked-in'): ?>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="res_id" value="<?= $res['id'] ?>">
                        <label style="display:block; margin-bottom: 10px; font-weight:bold;">Nahrát sken cestovního pasu:</label>
                        <input type="file" name="passport" accept="image/*" required style="margin-bottom: 20px; width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        
                        <button type="submit" class="btn" style="width: 100%; background: #27ae60; padding: 15px; font-size: 1rem; border: none; color: white; border-radius: 8px; cursor: pointer;">
                            Dokončit Check-in
                        </button>
                    </form>
                <?php else: ?>
                    <div style="text-align:center; background: #f0fdf4; padding: 20px; border-radius: 10px;">
                        <p style="color: #27ae60; font-weight:bold;">✅ Tento cestující již byl odbaven.</p>
                        <?php if ($res['passport_photo']): ?>
                            <div style="margin-top:10px;">
                                <a href="../<?= $res['passport_photo'] ?>" target="_blank" style="font-size: 0.9rem; color: #3498db;">Zobrazit nahraný pas</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            <?php elseif (!$msg): ?>
                <p style="text-align: center; color: #e74c3c;">Chyba: Rezervace nebyla nalezena.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>