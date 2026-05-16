<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. filtry
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$date = $_GET['date'] ?? '';

// --- LOGOVÁNÍ VYHLEDÁVÁNÍ (Pro budoucí analýzu adminem) ---
if (!empty($from) || !empty($to)) {
    // pro mozne zapisy do  search_logs a analyzu poptavky
}

// 2. SQL Dotaz - sjednoceni s logy a aerolinkami
$sql = "SELECT f.*, a.id as airline_id, a.name as airline_name, a.logo 
        FROM flights f 
        LEFT JOIN airlines a ON f.airline_id = a.id 
        WHERE 1=1";
$params = [];

if (!empty($from)) {
    $sql .= " AND f.destination_from LIKE ?";
    $params[] = "%$from%";
}
if (!empty($to)) {
    $sql .= " AND f.destination_to LIKE ?";
    $params[] = "%$to%";
}
if (!empty($date)) {
    $sql .= " AND DATE(f.departure_time) = ?";
    $params[] = $date;
}

$sql .= " ORDER BY f.departure_time ASC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $flights = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Chyba databáze: " . $e->getMessage());
}

include 'includes/header.php'; 
?>


<div class="container">
    <header style="text-align: center; margin: 40px 0;">
        <h1 style="font-size: 2.5rem; margin-bottom: 10px;">Vyhledávač letů ✈️</h1>
        <p style="color: #718096; font-size: 1.1rem;">Pro zahájení vyhledávání vyplňte alespoň jedno pole</p>
    </header>

    <section class="card" style="margin-bottom: 40px; border-top: 4px solid var(--secondary);">
        <form method="GET" action="index.php" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 200px;">
                <label style="font-weight: bold; color: var(--primary);">Odkud</label>
                <input type="text" name="from" value="<?= htmlspecialchars($from) ?>" placeholder="Město nebo letiště...">
            </div>
            <div style="flex: 1; min-width: 200px;">
                <label style="font-weight: bold; color: var(--primary);">Kam</label>
                <input type="text" name="to" value="<?= htmlspecialchars($to) ?>" placeholder="Cílová destinace...">
            </div>
            <div style="flex: 1; min-width: 150px;">
                <label style="font-weight: bold; color: var(--primary);">📅 Kdy</label>
                <input type="date" name="date" value="<?= htmlspecialchars($date) ?>">
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-secondary" style="height: 45px; padding: 0 30px;">Vyhledat lety</button>
                <?php if ($from || $to || $date): ?>
                    <a href="index.php" class="btn" style="background: #edf2f7; color: #4a5568; height: 45px; display: flex; align-items: center;">Zrušit</a>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <main>
        <?php if (empty($flights)): ?>
            <div class="card" style="text-align: center; padding: 60px;">
                <span style="font-size: 3rem;">🌍</span>
                <h3 style="margin-top: 20px;">V tento den nejsou žádné dostupné lety</h3>
                <p>Zkuste změnit destinaci nebo datum.</p>
                <a href="index.php" style="color: var(--secondary); font-weight: bold;">Zobrazit všechny dostupné lety ➔</a>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px;">
                <?php foreach ($flights as $flight): 
                    $isCancelled = (isset($flight['status']) && $flight['status'] === 'Zrušeno');
                    
                    // CHYTRÉ OŠETŘENÍ LOGA
                    $logoFile = basename($flight['logo'] ?? 'logo.svg');
                    $logoPath = "assets/img/" . $logoFile;
                    if (!file_exists($logoPath) || empty($logoFile)) {
                        $logoPath = "assets/img/logo.svg"; // Fallback na naši vlaštovku
                    }
                ?>
                    <div class="airline-card" style="<?= $isCancelled ? 'opacity: 0.7; border-top: 5px solid #e53e3e;' : 'border-top: 5px solid var(--secondary);' ?>">
                        
                        <?php if ($isCancelled): ?>
                            <div style="background: #fff5f5; color: #e53e3e; padding: 8px; text-align: center; font-weight: bold; border-radius: 6px; margin-bottom: 15px; font-size: 0.8rem; border: 1px solid #feb2b2;">
                                🛑 TENTO LET BYL ZRUŠEN
                            </div>
                        <?php endif; ?>

                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <img src="<?= $logoPath ?>" alt="Airline Logo" class="airline-logo-img" style="width: 100%; max-width: 60px; height: auto; object-fit: contain;">
                                <div>
                                    <a href="airline_details.php?id=<?= $flight['airline_id'] ?>" style="text-decoration: none;">
                                        <strong style="color: var(--primary); font-size: 1rem;"><?= htmlspecialchars($flight['airline_name'] ?? 'Neznámý let') ?></strong>
                                    </a>
                                    <div style="font-size: 0.75rem; color: #a0aec0;">ID: #<?= $flight['id'] ?></div>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 1.3rem; font-weight: 800; color: var(--primary);">
                                    <?= number_format($flight['price'], 0, ',', ' ') ?> Kč
                                </div>
                                <small style="color: #718096;">včetně poplatků</small>
                            </div>
                        </div>

                        <div style="margin: 25px 0; display: flex; align-items: center; justify-content: space-between;">
                            <div style="text-align: left;">
                                <div style="font-size: 1.2rem; font-weight: bold;"><?= htmlspecialchars($flight['destination_from']) ?></div>
                                <div style="color: var(--secondary); font-weight: bold;"><?= date('H:i', strtotime($flight['departure_time'])) ?></div>
                            </div>
                            
                            <div style="flex: 1; text-align: center; position: relative; margin: 0 15px;">
                                <div style="height: 2px; background: #e2e8f0; width: 100%;"></div>
                                <div style="position: absolute; top: -10px; left: 50%; transform: translateX(-50%); background: white; padding: 0 5px;">✈️</div>
                            </div>

                            <div style="text-align: right;">
                                <div style="font-size: 1.2rem; font-weight: bold;"><?= htmlspecialchars($flight['destination_to']) ?></div>
                                
                            </div>
                        </div>

                        <div style="background: #f7fafc; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; display: flex; justify-content: space-between;">
                            <span>📅 <?= date('d. m. Y', strtotime($flight['departure_time'])) ?></span>
                            <span style="color: var(--success); font-weight: bold;">● Přímý let</span>
                        </div>

                        <?php if (!$isCancelled): ?>
                            <a href="<?= isset($_SESSION['user_id']) ? 'booking_form.php?id='.$flight['id'] : 'login.php' ?>" 
                               class="btn <?= isset($_SESSION['user_id']) ? 'btn-success' : '' ?>" 
                               style="width: 100%; display: block; padding: 12px;">
                                <?= isset($_SESSION['user_id']) ? 'Rezervovat let' : '🔑 Pro nákup se přihlaste' ?>
                            </a>
                        <?php else: ?>
                            <a href="index.php?from=<?= urlencode($flight['destination_from']) ?>&to=<?= urlencode($flight['destination_to']) ?>" 
                               class="btn" style="width: 100%; background: var(--primary); display: block;">
                               🔍 Najít náhradní spoj
                            </a>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <div style="display: flex; gap: 5px; margin-top: 15px; padding-top: 15px; border-top: 1px dashed #e2e8f0;">
                                <a href="admin/cancel_flight.php?id=<?= $flight['id']; ?>" class="btn" style="background: #4a5568; font-size: 0.7rem; flex: 1; padding: 5px;">Zrušit let</a>
                                <a href="admin/delete_flight.php?id=<?= $flight['id']; ?>" class="btn" style="background: #e53e3e; font-size: 0.7rem; flex: 1; padding: 5px;" onclick="return confirm('Smazat?')">Smazat</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<footer style="text-align: center; margin-top: 80px; padding: 40px; background: #fff; border-top: 1px solid #e2e8f0; color: #a0aec0;">
    <img src="assets/img/logo.svg" style="height: 30px; opacity: 0.5; margin-bottom: 10px;"><br>
    &copy; 2026 TripDesk Letištní Systém | Školní projekt WA<br>
    <small>Všechny časy jsou uvedeny v místním čase.</small>
</footer>

</body>
</html>