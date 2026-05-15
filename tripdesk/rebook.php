<?php
require_once 'includes/db.php';
session_start();

// 1. Ochrana: Jen Admin a Editor
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'editor')) {
    die("Nepovolený přístup.");
}

$res_id = $_GET['res_id'] ?? null;
if (!$res_id) die("Rezervace nenalezena.");

// 2. Načtení detailů staré rezervace (potřebujeme pro zobrazení "Aktuální let")
$stmt = $pdo->prepare("SELECT r.*, u.username FROM reservations r JOIN users u ON r.user_id = u.id WHERE r.id = ?");
$stmt->execute([$res_id]);
$old_res = $stmt->fetch();

// 3. Zachycení filtrů z vyhledávání
$search_to = $_GET['search_to'] ?? '';

// 4. Načtení dostupných letů pro tabulku
// 4. Načtení dostupných letů pro tabulku (POUZE AKTIVNÍ A BUDOUCÍ)
$sql = "SELECT f.*, a.name as airline_name 
        FROM flights f 
        JOIN airlines a ON f.airline_id = a.id 
        WHERE f.status = 'Aktivní' 
        AND f.departure_time > NOW()"; // Ukáže jen lety, které ještě neodletěly

if (!empty($search_to)) {
    // Používáme připravený parametr pro bezpečnost (quote už máš, tak to zachováme)
    $sql .= " AND (f.destination_to LIKE " . $pdo->quote("%$search_to%") . " OR f.destination_from LIKE " . $pdo->quote("%$search_to%") . ")";
}

$sql .= " ORDER BY f.departure_time ASC";
$available_flights = $pdo->query($sql)->fetchAll();

// NAČTENÍ HEADERU (už s tvou novou vlaštovkou a designem)
include 'includes/header.php';
?>

<div class="container" style="margin-top: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2 style="color: var(--primary); margin: 0;">🔄 Rebooking</h2>
            <p style="color: #718096; margin: 5px 0 0 0;">Změna rezervace pro uživatele: <strong><?= htmlspecialchars($old_res['username']) ?></strong></p>
        </div>
        <a href="edit_reservation.php?res_id=<?= $res_id ?>" class="btn" style="background: #edf2f7; color: #4a5568;">Zpět na detail</a>
    </div>

    <div style="display: flex; gap: 20px; margin-bottom: 30px;">
        <div class="card" style="flex: 2; border-left: 5px solid var(--secondary); background: white;">
            <small style="color: #a0aec0; font-weight: bold;">AKTUÁLNÍ LET:</small>
            <div style="font-size: 1.1rem; margin-top: 5px; font-weight: 600; color: var(--primary);">
                <?= htmlspecialchars($old_res['flight_info']) ?>
            </div>
        </div>
        <div class="card" style="flex: 1; border-left: 5px solid var(--success); background: white; text-align: center;">
            <small style="color: #a0aec0; font-weight: bold;">ZAPLACENÝ KREDIT:</small>
            <div style="font-size: 1.4rem; font-weight: 800; color: var(--success); margin-top: 5px;">
                <?= number_format($old_res['price_paid'], 0, ',', ' ') ?> Kč
            </div>
        </div>
    </div>

    <section class="card" style="margin-bottom: 30px;">
        <form method="GET" style="display: flex; gap: 15px; align-items: flex-end;">
            <input type="hidden" name="res_id" value="<?= $res_id ?>">
            <div style="flex: 1;">
                <label style="font-weight: bold; color: var(--primary); display: block; margin-bottom: 8px;">Kam nebo odkud chce cestující letět?</label>
                <input type="text" name="search_to" value="<?= htmlspecialchars($search_to) ?>" placeholder="Např. Paříž, Londýn..." style="width: 100%;">
            </div>
            <button type="submit" class="btn btn-secondary" style="height: 45px;">🔍 Najít náhradní lety</button>
        </form>
    </section>

    <div class="card" style="padding: 0; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: var(--primary); color: white;">
                    <th style="padding: 15px; text-align: left;">Dopravce</th>
                    <th style="padding: 15px; text-align: left;">Trasa</th>
                    <th style="padding: 15px; text-align: left;">Odlet</th>
                    <th style="padding: 15px; text-align: left;">Nová cena</th>
                    <th style="padding: 15px; text-align: left;">Rozdíl</th>
                    <th style="padding: 15px; text-align: center;">Akce</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($available_flights as $f): 
                    $diff = $f['price'] - $old_res['price_paid'];
                    $diff_color = ($diff > 0) ? "#e53e3e" : "#38a169";
                ?>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 15px;"><strong><?= htmlspecialchars($f['airline_name']) ?></strong></td>
                        <td style="padding: 15px;"><?= htmlspecialchars($f['destination_from']) ?> ➔ <?= htmlspecialchars($f['destination_to']) ?></td>
                        <td style="padding: 15px;"><?= date('d. m. H:i', strtotime($f['departure_time'])) ?></td>
                        <td style="padding: 15px; font-weight: bold;"><?= number_format($f['price'], 0, ',', ' ') ?> Kč</td>
                        <td style="padding: 15px; color: <?= $diff_color ?>; font-weight: bold;">
                            <?= ($diff >= 0 ? "+" : "") . number_format($diff, 0, ',', ' ') ?> Kč
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            <form method="POST" action="actions/save_rebook.php">
                                <input type="hidden" name="res_id" value="<?= $res_id ?>">
                                <input type="hidden" name="new_flight_id" value="<?= $f['id'] ?>">
                                <button type="submit" name="confirm_rebook" class="btn btn-success" 
                                        style="font-size: 0.85rem;"
                                        onclick="return confirm('Provést změnu na tento let? Rozdíl: <?= $diff ?> Kč.')">
                                    Vybrat
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (empty($available_flights)): ?>
            <div style="padding: 40px; text-align: center; color: #a0aec0;">Žádné lety neodpovídají hledání.</div>
        <?php endif; ?>
    </div>
</div>