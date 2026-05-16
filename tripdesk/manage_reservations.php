<?php
require_once 'includes/db.php';
session_start();

// 1. vyzadat prihlaseni
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// 2. LOGIKA VÝBĚRU DAT:
// Admin a Editor vidí všechno, běžný uživatel jen své rezervace
if ($role === 'admin' || $role === 'editor') {
    $query = "SELECT r.*, u.username, u.first_name, u.last_name,
              (SELECT COUNT(*) FROM reservation_history WHERE reservation_id = r.id) as history_count
              FROM reservations r 
              JOIN users u ON r.user_id = u.id 
              ORDER BY r.id DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
} else {
    // OPRAVENO: Přidán alias r.*, aby databáze přesně věděla, odkud brát sloupce
    $query = "SELECT r.*, 
              (SELECT COUNT(*) FROM reservation_history WHERE reservation_id = r.id) as history_count 
              FROM reservations r 
              WHERE r.user_id = ? 
              ORDER BY r.id DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id]);
}

$reservations = $stmt->fetchAll();

include 'includes/header.php'; 
?>

<div class="container" style="margin-top: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1>
            <?php 
                if ($role === 'admin') echo "📋 Administrace rezervací";
                elseif ($role === 'editor') echo "📑 Správa rezervací (Editor)";
                else echo "✈️ Moje rezervace";
            ?>
        </h1>
        <a href="index.php" class="btn" style="background: #3498db; text-decoration: none; color: white; padding: 10px 20px; border-radius: 5px;">+ Nová rezervace</a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #c3e6cb;">
            Akce proběhla úspěšně.
        </div>
    <?php endif; ?>

    <?php if (empty($reservations)): ?>
        <div style="background: white; text-align: center; padding: 60px; border-radius: 12px; border: 1px solid #eee;">
            <p style="color: #7f8c8d; font-size: 1.2rem;">Zatím zde nejsou žádné záznamy.</p>
        </div>
    <?php else: ?>
        <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eee;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #2c3e50; color: white; text-align: left;">
                        <th style="padding: 18px;">ID</th>
                        <?php if ($role === 'admin' || $role === 'editor'): ?>
                            <th style="padding: 18px;">Cestující</th>
                        <?php endif; ?>
                        <th style="padding: 18px;">Detaily letu</th>
                        <th style="padding: 18px;">Cena</th>
                        <th style="padding: 18px; text-align: center;">Akce</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $res): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 18px; font-weight: bold;">#<?= $res['id']; ?></td>
                            
                            <?php if ($role === 'admin' || $role === 'editor'): ?>
                                <td style="padding: 18px;">
                                    <strong><?= htmlspecialchars($res['username']); ?></strong><br>
                                    <small style="color: #7f8c8d;"><?= htmlspecialchars($res['first_name'] . " " . $res['last_name']); ?></small>
                                </td>
                            <?php endif; ?>

                            <td style="padding: 18px;">
                                <div style="margin-bottom: 5px; font-weight: 500; <?= ($res['status'] === 'Zrušeno') ? 'text-decoration: line-through; color: #a0aec0;' : ''; ?>">
                                    <?= htmlspecialchars($res['flight_info']); ?>
                                </div>
                                <div style="display: flex; gap: 10px;">
                                    <span style="font-size: 0.7rem; padding: 2px 8px; border-radius: 4px; 
                                                 background: <?= ($res['status'] === 'Zrušeno') ? '#fff5f5' : '#e8f5e9'; ?>; 
                                                 color: <?= ($res['status'] === 'Zrušeno') ? '#e53e3e' : '#2e7d32'; ?>; 
                                                 border: 1px solid <?= ($res['status'] === 'Zrušeno') ? '#feb2b2' : 'transparent'; ?>; 
                                                 font-weight: bold; text-transform: uppercase;">
                                        <?= htmlspecialchars($res['status']); ?>
                                    </span>

                                    <?php if ($res['history_count'] > 0): ?>
                                        <span style="font-size: 0.7rem; color: #718096; background: #edf2f7; padding: 2px 8px; border-radius: 4px;">
                                            📜 Log: <?= $res['history_count'] ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="padding: 18px; font-weight: bold;">
                                <?= number_format($res['price_paid'], 0, ',', ' '); ?> Kč
                            </td>

                            <td style="padding: 18px;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    
                                    <a href="invoice.php?res_id=<?= $res['id']; ?>" class="btn" style="padding: 6px 12px; background: #c0ff9c; color: #19be00; text-decoration: none; border-radius: 4px; font-size: 0.8rem;">
                                        Faktura
                                    </a>
                                    
                                    <a href="edit_reservation.php?res_id=<?= $res['id']; ?>" class="btn" style="padding: 6px 12px; background: #c7e9ff; color: #0b588b; text-decoration: none; border-radius: 4px; font-size: 0.8rem;">
                                        Spravovat
                                    </a>

                                    <?php if ($role === 'admin' || $role === 'editor'): ?>
                                        <a href="rebook.php?res_id=<?= $res['id']; ?>" class="btn" style="padding: 6px 12px; font-size: 0.8rem; background: #ffba7d; text-decoration: none; color: #b96600; border-radius: 4px;">
                                        Rebook
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($role === 'admin'): ?>
                                        <a href="admin/delete_reservation.php?id=<?= $res['id']; ?>" class="btn" style="padding: 6px 12px; font-size: 0.8rem; background: #f88f84; text-decoration: none; color: #d23726; border-radius: 6px;" onclick="return confirm('Opravdu smazat?')">
                                            Smazat
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<footer style="margin-top: 50px; text-align: center; padding: 20px; color: #bdc3c7; font-size: 0.85rem;">
    TripDesk Management System v2.0 | 2026
</footer>
</body>
</html>