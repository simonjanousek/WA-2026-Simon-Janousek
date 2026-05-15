<?php
require_once 'includes/db.php';
session_start();

// 1. Ochrana přístupu - musí být přihlášen
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

$res_id = $_GET['res_id'] ?? null;
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'user';

if (!$res_id) {
    die("Rezervace nebyla specifikována.");
}

// 2. Načtení dat rezervace + jména cestujícího přes JOIN
$stmt = $pdo->prepare("
    SELECT r.*, u.first_name, u.last_name, u.username as passenger_username
    FROM reservations r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.id = ?
");
$stmt->execute([$res_id]);
$res = $stmt->fetch();

// 3. Kontrola oprávnění (Vlastník, Admin nebo Editor)
if (!$res || ($res['user_id'] != $user_id && $role !== 'admin' && $role !== 'editor')) {
    die("K této rezervaci nemáte přístup.");
}

// 4. Načtení historie (Timeline) - JOIN na uživatele, který změnu PROVEDL
$stmtH = $pdo->prepare("
    SELECT h.*, u.username as editor_name, u.role as editor_role, u.first_name as efname, u.last_name as elname
    FROM reservation_history h 
    JOIN users u ON h.user_id = u.id 
    WHERE h.reservation_id = ? 
    ORDER BY h.created_at DESC
");
$stmtH->execute([$res_id]);
$history = $stmtH->fetchAll();

include 'includes/header.php'; 
?>

<div class="container" style="margin-top: 30px;">
    <div style="margin-bottom: 25px;">
        <a href="manage_reservations.php" style="text-decoration: none; color: var(--secondary); font-weight: 600;">
            ← Zpět na seznam rezervací
        </a>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h2 style="margin: 0; color: var(--primary);">Správa rezervace #<?= htmlspecialchars($res['id']) ?></h2>
    
    <?php 
        // Definice barvy pozadí podle stavu
        $badgeColor = 'var(--success)'; // Výchozí zelená
        if ($res['status'] === 'Zrušeno') {
            $badgeColor = '#e74c3c'; // Červená pro zrušení
        } elseif ($res['status'] === 'Zpožděno') {
            $badgeColor = '#f39c12'; // Oranžová
        }
    ?>
    
    <span style="background: <?= $badgeColor ?>; color: white; padding: 5px 15px; border-radius: 20px; font-size: 0.9rem; font-weight: bold; text-transform: uppercase;">
        Stav: <?= htmlspecialchars($res['status']) ?>
    </span>
</div>
    
    <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 40px; align-items: start;">
        
        <div class="card" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eee;">
            <h3 style="margin-top: 0; margin-bottom: 20px; color: var(--primary);">📝 Údaje rezervace</h3>
            
            <form action="actions/update_reservation.php" method="POST">
                <input type="hidden" name="res_id" value="<?= htmlspecialchars($res['id']) ?>">
                <input type="hidden" name="target_user_id" value="<?= $res['user_id'] ?>">

                <?php if ($role === 'admin'): ?>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; font-weight: bold; margin-bottom: 8px;">Jméno cestujícího:</label>
                            <input type="text" name="first_name" value="<?= htmlspecialchars($res['first_name']) ?>" required 
                                   style="width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 8px;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: bold; margin-bottom: 8px;">Příjmení cestujícího:</label>
                            <input type="text" name="last_name" value="<?= htmlspecialchars($res['last_name']) ?>" required 
                                   style="width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 8px;">
                        </div>
                    </div>
                <?php else: ?>
                    <div style="margin-bottom: 20px; padding: 10px; background: #fdf2f2; border-radius: 8px; border: 1px solid #fbd3d3;">
                        <strong>Cestující:</strong> <?= htmlspecialchars($res['first_name'] . " " . $res['last_name']) ?> 
                        <small style="color: #718096;">(@<?= htmlspecialchars($res['passenger_username']) ?>)</small>
                    </div>
                <?php endif; ?>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px;">Info o letu:</label>
                    <div style="background: #f8f9fa; padding: 12px; border-radius: 8px; border: 1px solid #e9ecef; color: #4a5568; font-size: 0.95rem;">
                        <?= htmlspecialchars($res['flight_info']) ?>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px;">Kontaktní E-mail:</label>
                    <input type="email" name="contact_email" value="<?= htmlspecialchars($res['contact_email'] ?? '') ?>" required 
                           style="width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 8px;">
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px;">Kontaktní Telefon:</label>
                    <input type="text" name="contact_phone" placeholder="+420..." required 
                           style="width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 8px;">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-weight: bold; font-size: 1rem; border-radius: 8px;">
                    💾 Uložit veškeré změny
                </button>
            </form>

            <?php if ($role === 'admin' || $role === 'editor'): ?>
                <div style="margin-top: 30px; padding-top: 25px; border-top: 2px dashed #edf2f7;">
                    <h4 style="color: #718096; margin-top: 0; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px;">🛠 Manažerské nástroje</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <a href="rebook.php?res_id=<?= $res['id'] ?>" class="btn" style="background: #edf2f7; color: #2d3748; text-decoration: none; text-align: center; padding: 10px; font-size: 0.85rem; border: 1px solid #cbd5e0; border-radius: 8px; font-weight: 600;">
                            🔄 Rebooking
                        </a>
                        <a href="admin/checkin.php?res_id=<?= $res['id'] ?>" class="btn" style="background: var(--secondary); color: white; text-decoration: none; text-align: center; padding: 10px; font-size: 0.85rem; border-radius: 8px; font-weight: 600;">
                            ✅ Check-in
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div style="background: #fff; padding: 30px; border-radius: 12px; border: 1px solid #eee; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <h3 style="margin-top: 0; margin-bottom: 30px; color: var(--primary);">🕒 Historie změn (Change Log)</h3>
            
            <div style="border-left: 3px solid var(--secondary); padding-left: 30px; margin-left: 10px;">
                <?php if (empty($history)): ?>
                    <p style="color: #a0aec0; font-style: italic; text-align: center;">Zatím nebyly provedeny žádné změny.</p>
                <?php else: ?>
                    <?php foreach ($history as $event): ?>
                        <div style="margin-bottom: 30px; position: relative;">
                            <div style="position: absolute; left: -37px; top: 4px; width: 12px; height: 12px; background: var(--secondary); border-radius: 50%; border: 3px solid white; box-shadow: 0 0 0 2px var(--secondary);"></div>
                            
                            <div style="font-size: 0.75rem; color: #a0aec0; font-weight: bold; margin-bottom: 5px;">
                                <?= date('d. m. Y | H:i', strtotime($event['created_at'])) ?>
                            </div>
                            
                            <div style="font-weight: 700; color: var(--primary); font-size: 0.95rem;">
                                <?= htmlspecialchars($event['action_type']) ?>
                            </div>
                            
                            <div style="font-size: 0.85rem; color: #4a5568; margin-top: 6px; line-height: 1.5; background: #f7fafc; padding: 10px; border-radius: 6px; border: 1px solid #edf2f7;">
                                <?= htmlspecialchars($event['details']) ?>
                            </div>

                            <div style="font-size: 0.75rem; color: #718096; margin-top: 8px;">
                                Provedl: <strong style="color: var(--secondary);"><?= htmlspecialchars($event['efname'] . " " . $event['elname']) ?></strong> 
                                <span style="font-size: 0.65rem; background: #e2e8f0; padding: 1px 6px; border-radius: 4px; margin-left: 5px; font-weight: bold;">
                                    <?= strtoupper($event['editor_role']) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<footer style="margin-top: 60px; text-align: center; padding: 30px; border-top: 1px solid #edf2f7; color: #a0aec0; font-size: 0.85rem;">
    TripDesk Management & Audit System | &copy; 2026
</footer>
</body>
</html>