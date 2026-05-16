<?php
require_once 'includes/db.php';
session_start();

// Kontrola přihlášení
if (!isset($_SESSION['user_id'])) {
    die("Pro zobrazení faktury se musíte přihlásit.");
}

$res_id = $_GET['res_id'] ?? null;
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

// Bezpečnostní pojistka: Ochrana dat před cizími uživateli
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'editor' && $res['user_id'] != $_SESSION['user_id']) {
    die("K tomuto dokladu nemáte přístup.");
}

// --- DYNAMICKÁ DETEKCE REBOOKINGU Z LOGŮ V DATABÁZI ---
$is_rebooked = false;
$stara_cena = null;
$doplatek = 0;

// Podíváme se, zda k této rezervaci existuje záznam o Rebookingu v logách
// POZOR: Pokud se tvoje tabulka logů jmenuje 'reservation_history', přepiš slovo 'logs' na 'reservation_history'
$stmt_log = $pdo->prepare("
    SELECT details FROM reservation_history 
    WHERE reservation_id = ? 
    ORDER BY id DESC LIMIT 1
");
$stmt_log->execute([$res_id]);
$log = $stmt_log->fetch();

if ($log) {
    $is_rebooked = true;
    // Pomocí regulárního výrazu vytáhneme z logu původní cenu (hledá číslo za textem "Původní cena:")
    if (preg_match('/Původní cena:\s*([0-9\s]+)/u', $log['details'], $matches)) {
        $stara_cena = (int)str_replace(' ', '', $matches[1]);
        $doplatek = $res['price_paid'] - $stara_cena;
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Faktura_<?= $res['id'] ?></title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; color: #333; line-height: 1.6; background-color: #f4f6f9; padding: 20px; }
        .invoice-box { max-width: 800px; margin: auto; padding: 40px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); background: #fff; border-radius: 8px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #3498db; padding-bottom: 20px; }
        .company-info { text-align: right; font-size: 0.9rem; color: #4a5568; }
        .details { margin-top: 30px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 20px; }
        .customer-info { font-size: 0.95rem; }
        .meta-info { text-align: right; font-size: 0.95rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        th { background: #f8f9fa; text-align: left; padding: 12px; border-bottom: 2px solid #e2e8f0; color: #4a5568; font-weight: 600; }
        td { padding: 12px; border-bottom: 1px solid #e2e8f0; }
        .total { text-align: right; margin-top: 40px; font-size: 1.4rem; font-weight: bold; color: #2c3e50; }
        .stamp { margin-top: 60px; text-align: right; font-style: italic; color: #a0aec0; font-size: 0.85rem; }
        
        .btn-container { text-align: center; margin-bottom: 25px; }
        .btn-print { padding: 10px 20px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; margin-right: 10px; }

        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none; }
            .invoice-box { border: none; box-shadow: none; padding: 0; max-width: 100%; }
        }
    </style>
</head>
<body>

<div class="no-print btn-container">
    <button onclick="window.print();" class="btn-print">🖨️ Stáhnout / Tisknout PDF</button>
</div>

<div class="invoice-box">
    <div class="header">
        <div>
            <img src="./assets/img/logo.svg" alt="TripDesk Logo" style="height: 55px; margin-bottom: 10px; display: block;">
            <p style="margin: 0; font-weight: bold; color: #4a5568;">Variabilní symbol: <?= $res['id'] . date('Y') ?></p>
        </div>
        <div class="company-info">
            <strong style="font-size: 1.1rem; color: #2c3e50;">TripDesk s.r.o.</strong><br>
            Letištní 123, Praha 6<br>
            IČ: 12345678<br>
            DIČ: CZ12345678
        </div>
    </div>

    <div class="details">
        <div class="customer-info">
            <strong style="color: #4a5568;">Odběratel:</strong><br>
            <span style="font-size: 1.1rem; font-weight: bold; color: #2c3e50;"><?= htmlspecialchars($res['first_name'] . ' ' . $res['last_name']) ?></span><br>
            <?= htmlspecialchars($res['email']) ?><br>
            <small style="color: #718096;">Uživatelské jméno: <?= htmlspecialchars($res['username']) ?></small>
        </div>
        <div class="meta-info">
            <strong>Datum vystavení:</strong> <?= date('d. m. Y') ?><br>
            <strong>Stav rezervace:</strong> 
            <span style="color: <?= ($res['status'] === 'Zrušeno') ? '#e53e3e' : '#27ae60'; ?>; font-weight: bold;">
                <?= htmlspecialchars($res['status']) ?>
            </span>
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
            <?php if ($is_rebooked && $stara_cena !== null): ?>
                <tr>
                    <td>
                        <strong>Původní letenka (Zápočet / Storno):</strong><br>
                        <small style="color: #718096;">Částka odečtená z původního zrušeného letu</small>
                    </td>
                    <td style="text-align: right; color: #e53e3e; font-weight: bold;">-<?= number_format($stara_cena, 0, ',', ' ') ?> Kč</td>
                </tr>
                <tr>
                    <td>
                        <strong>Nová letenka (Změna rezervace):</strong> <?= htmlspecialchars($res['flight_info']) ?><br>
                        <small style="color: #718096;">Typ dokladu: Opravný daňový doklad</small>
                    </td>
                    <td style="text-align: right; font-weight: bold; color: #2c3e50;"><?= number_format($res['price_paid'], 0, ',', ' ') ?> Kč</td>
                </tr>
                <tr style="background: #fffaf0; font-weight: bold;">
                    <td style="color: #b96600; padding: 15px 12px;">
                        ➔ ROZDÍL K ÚHRADĚ:
                    </td>
                    <td style="text-align: right; color: #b96600; padding: 15px 12px; font-size: 1.1rem;">
                        <?= ($doplatek >= 0 ? "+" : "") . number_format($doplatek, 0, ',', ' ') ?> Kč
                    </td>
                </tr>
            <?php else: ?>
                <tr>
                    <td>
                        <strong style="color: #2c3e50;">Letenka:</strong> <?= htmlspecialchars($res['flight_info']) ?><br>
                        <small style="color: #718096;">Typ dokladu: Řádná faktura</small>
                    </td>
                    <td style="text-align: right; font-weight: bold; color: #2c3e50;"><?= number_format($res['price_paid'], 0, ',', ' ') ?> Kč</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="total">
        <?php if ($is_rebooked && $stara_cena !== null): ?>
            CELKEM K DOPLACENÍ: <?= number_format($doplatek, 0, ',', ' ') ?> Kč
        <?php else: ?>
            CELKEM K ÚHRADĚ: <?= number_format($res['price_paid'], 0, ',', ' ') ?> Kč
        <?php endif; ?>
    </div>

    <div class="stamp">
        Elektronicky vystaveno systémem TripDesk.<br>
        Děkujeme za váš nákup!
    </div>
</div>

</body>
</html>