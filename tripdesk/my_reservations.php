<?php
require_once 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['user_id'];

// nacteni rezervace uživatele a propojeni s tabulkou airlines pro název
$stmt = $pdo->prepare("
    SELECT r.*, a.name as airline_name 
    FROM reservations r 
    JOIN airlines a ON 1=1 -- Zde by v reálu byla vazba na lety, pro zjednodušení teď takto
    WHERE r.user_id = ?
");
// v reservation uz ulozene info o letu
$stmt->execute([$user_id]);
$reservations = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <h1>Moje letenky</h1>
    
    <?php if (empty($reservations)): ?>
        <p>Nemáte žádné rezervace. <a href="index.php">Koupit letenku</a></p>
    <?php else: ?>
        <?php foreach ($reservations as $res): ?>
            <div class="airline-card">
                <h3>Let: <?= htmlspecialchars($res['flight_info']) ?></h3>
                <p>Zaplacená cena: <strong><?= $res['price_paid'] ?> Kč</strong></p>
                <p>Stav: <?= $res['status'] ?></p>

                <?php if ($_SESSION['role'] === 'editor' || $_SESSION['role'] === 'admin'): ?>
                    <a href="rebook.php?res_id=<?= $res['id'] ?>" class="btn">Změnit let (Rebooking)</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
