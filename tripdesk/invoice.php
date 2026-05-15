<?php
require_once 'includes/db.php';
session_start();

$res_id = $_GET['id'] ?? null;
if (!$res_id) die("Rezervace nenalezena.");

// Načtení dat rezervace a uživatele
$stmt = $pdo->prepare("
    SELECT r.*, u.username, u.email, u.first_name, u.last_name 
    FROM reservations r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.id = ?
");
$stmt->execute([$res_id]);
$res = $stmt->fetch();

if (!$res) die("Doklad neexistuje.");

// Detekce, zda jde o rebooking (podle textu ve flight_info)
$is_rebooked = strpos($res['flight_info'], 'REBOOKED') !== false;
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Faktura_<?= $res['id'] ?></title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; color: #333; line-height: 1.6; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, .15); }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #3498db; padding-bottom: 20px; }
        .company-info { text-align: right; }
        .details { margin-top: 30px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 40px; }
        th { background: #f8f9fa; text-align: left; padding: 10px; border-bottom: 2px solid #eee; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        .total { text-align: right; margin-top: 30px; font-size: 1.2rem; font-weight: bold; }
        .stamp { margin-top: 50px; text-align: right; font-style: italic; color: #999; }
        
        /* Tlačítka schováme při tisku */
        @media print {
            .no-print { display: none; }
            .invoice-box { border: none; box-shadow: none; }
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align: center; margin-bottom: 20px;">
    <button onclick="window.print();" style="padding: 10px 20px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer;">
        🖨️ Stáhnout / Tisknout PDF
    </button>
    <button onclick="window.close();" style="padding: 10px 20px; background: #95a5a6; color: white; border: none; border-radius: 5px; cursor: pointer;">
        Zavřít
    </button>
</div>

<div class="invoice-box">
    <div class="header">
        <div>
            <h1 style="color: #3498db; margin: 0;">TripDesk ✈️</h1>
            <p>Variabilní symbol: <?= $res['id'] . date('Y') ?></p>
        </div>
        <div class="company-info">
            <strong>TripDesk s.r.o.</strong><br>
            Letištní 123, Praha 6<br>
            IČ: 12345678<br>
            DIČ: CZ12345678
        </div>
    </div>

    <div class="details">
        <div>
            <strong>Odběratel:</strong><br>
            <?= htmlspecialchars($res['first_name'] . ' ' . $res['last_name']) ?><br>
            <?= htmlspecialchars($res['email']) ?><br>
            Username: <?= htmlspecialchars($res['username']) ?>
        </div>
        <div style="text-align: right;">
            <strong>Datum vystavení:</strong> <?= date('d. m. Y') ?><br>
            <strong>Status:</strong> <?= htmlspecialchars($res['status']) ?>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Popis položky</th>
                <th style="text-align: right;">Cena</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Letenka:</strong> <?= htmlspecialchars($res['flight_info']) ?><br>
                    <small style="color: #666;">Typ dokladu: <?= $is_rebooked ? 'Opravný daňový doklad (Rebooking)' : 'Řádná faktura' ?></small>
                </td>
                <td style="text-align: right;"><?= number_format($res['price_paid'], 0, ',', ' ') ?> Kč</td>
            </tr>
        </tbody>
    </table>

    <div class="total">
        CELKEM K ÚHRADĚ: <?= number_format($res['price_paid'], 0, ',', ' ') ?> Kč
    </div>

    <div class="stamp">
        Elektronicky vystaveno systémem TripDesk.<br>
        Děkujeme za váš nákup!
    </div>
</div>

</body>
</html>