<?php
require_once 'includes/db.php';
session_start();

$airline_id = $_GET['id'] ?? null;

if (!$airline_id) {
    header("Location: index.php");
    exit();
}

// 1. Načtení informací o aerolince
$stmt = $pdo->prepare("SELECT * FROM airlines WHERE id = ?");
$stmt->execute([$airline_id]);
$airline = $stmt->fetch();

if (!$airline) {
    die("Aerolinka nenalezena v databázi.");
}

// 2. Načtení recenzí (airline_comments)
$stmtComm = $pdo->prepare("
    SELECT c.*, u.username 
    FROM airline_comments c 
    JOIN users u ON c.user_id = u.id 
    WHERE c.airline_id = ? 
    ORDER BY c.created_at DESC
");
$stmtComm->execute([$airline_id]);
$comments = $stmtComm->fetchAll();

// 3. Načtení aktuálních letů této společnosti
$stmtFlights = $pdo->prepare("SELECT * FROM flights WHERE airline_id = ? AND status != 'Zrušeno' ORDER BY departure_time ASC");
$stmtFlights->execute([$airline_id]);
$airlineFlights = $stmtFlights->fetchAll();

include 'includes/header.php';
?>

<div class="container" style="margin-top: 30px;">
    <a href="index.php" style="text-decoration: none; color: var(--secondary); font-weight: 600;">← Zpět na hlavní vyhledávač</a>

    <?php 
        // Logika pro zobrazení loga
        $logo_name = $airline['logo']; 
        $logo_path = 'assets/img/' . $logo_name;

        if (empty($logo_name) || !file_exists($logo_path)) {
            $logo_display = 'assets/img/logo.svg'; 
        } else {
            $logo_display = $logo_path;
        }
    ?>
    <div style="display: flex; align-items: center; gap: 25px; margin: 30px 0; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-top: 5px solid var(--primary);">
        <img src="<?= htmlspecialchars($logo_display) ?>" 
             alt="<?= htmlspecialchars($airline['name']) ?> Logo"
             style="width: 100px; height: 100px; object-fit: contain; background: #fff; padding: 5px; border-radius: 10px; border: 1px solid #f0f0f0;">
        <div>
            <h1 style="margin: 0; color: var(--primary);"><?= htmlspecialchars($airline['name']) ?></h1>
            <p style="color: #718096; margin-top: 5px;">Letecká společnost • <?= count($airlineFlights) ?> aktivních spojů</p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px;">
        
        <section>
            <h3 style="border-bottom: 2px solid var(--secondary); padding-bottom: 10px; margin-bottom: 25px; color: var(--primary);">💬 Recenze uživatelů</h3>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <form action="actions/add_comment.php" method="POST" style="margin-bottom: 40px; background: #f7fafc; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <input type="hidden" name="airline_id" value="<?= $airline_id ?>">
                    
                    <label style="display: block; margin-bottom: 10px; font-weight: bold; color: var(--primary);">Vaše zkušenost:</label>
                    <textarea name="content" placeholder="Jak se vám s <?= htmlspecialchars($airline['name']) ?> letělo?" required 
                              style="width: 100%; height: 100px; padding: 12px; border: 1px solid #cbd5e0; border-radius: 8px; font-family: inherit; resize: vertical;"></textarea>
                    
                    <button type="submit" class="btn btn-secondary" style="margin-top: 15px; width: 100%; padding: 12px; font-weight: bold;">🚀 Odeslat recenzi</button>
                </form>
            <?php else: ?>
                <div style="background: #fffaf0; padding: 20px; border-radius: 10px; border: 1px solid #fbd38d; margin-bottom: 30px; color: #7b341e; text-align: center;">
                    Pro psaní recenzí se prosím nejprve <a href="login.php" style="font-weight: bold; color: var(--primary);">přihlaste</a>.
                </div>
            <?php endif; ?>

            <?php if (empty($comments)): ?>
                <p style="color: #a0aec0; font-style: italic; text-align: center; padding: 40px; background: white; border-radius: 12px; border: 1px dashed #e2e8f0;">Zatím zde nejsou žádné recenze.</p>
            <?php else: ?>
                <?php foreach ($comments as $c): ?>
                    <div style="background: white; border: 1px solid #edf2f7; padding: 20px; border-radius: 12px; margin-bottom: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <strong style="color: var(--secondary);">@<?= htmlspecialchars($c['username']) ?></strong>
                            <small style="color: #a0aec0;"><?= date('d. m. Y H:i', strtotime($c['created_at'])) ?></small>
                        </div>
                        <p style="margin: 0; line-height: 1.6; color: #2d3748;"><?= nl2br(htmlspecialchars($c['comment_text'])) ?></p>
                        
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <div style="margin-top: 15px; text-align: right;">
                                <a href="admin/delete_comment.php?id=<?= $c['id'] ?>&airline_id=<?= $airline_id ?>" 
                                   style="color: #e53e3e; font-size: 0.8rem; text-decoration: none; border: 1px solid #e53e3e; padding: 4px 10px; border-radius: 6px;" 
                                   onclick="return confirm('Opravdu smazat?')">🗑️ Smazat</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <aside>
            <h3 style="border-bottom: 2px solid var(--success); padding-bottom: 10px; margin-bottom: 25px; color: var(--primary);">✈️ Aktuální lety</h3>
            <?php if (empty($airlineFlights)): ?>
                <p style="color: #a0aec0; background: white; padding: 20px; border-radius: 12px; text-align: center; border: 1px solid #eee;">Žádné aktivní lety.</p>
            <?php else: ?>
                <?php foreach ($airlineFlights as $f): ?>
                    <div class="card" style="margin-bottom: 15px; border-left: 4px solid var(--success); background: white;">
                        <div style="font-weight: bold; font-size: 1.1rem; color: var(--primary);">
                            <?= htmlspecialchars($f['destination_from']) ?> ➔ <?= htmlspecialchars($f['destination_to']) ?>
                        </div>
                        <div style="color: #718096; font-size: 0.85rem; margin: 8px 0 15px 0;">
                            📅 <?= date('d. m. Y', strtotime($f['departure_time'])) ?> | 🕒 <?= date('H:i', strtotime($f['departure_time'])) ?>
                        </div>
                        <a href="booking_form.php?id=<?= $f['id'] ?>" class="btn btn-success" style="display: block; text-align: center; text-decoration: none; font-weight: bold;">
                            <?= number_format($f['price'], 0, ',', ' ') ?> Kč
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </aside>

    </div>
</div>

<footer style="text-align: center; margin-top: 60px; padding: 40px; border-top: 1px solid #edf2f7; color: #a0aec0;">
    &copy; 2026 TripDesk Letištní Portál
</footer>
</body>
</html>