<?php
require_once '../includes/db.php';
session_start();

// Ochrana: Jen admin sem může
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Nepovolený přístup.");
}

// Načtení seznamu aerolinek (včetně loga pro náhled)
$airlines = $pdo->query("SELECT id, name, logo FROM airlines ORDER BY name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $airline_id = $_POST['airline_id'];
    $from = $_POST['destination_from'];
    $to = $_POST['destination_to'];
    $departure = $_POST['departure_time'];
    $price = $_POST['price'];

    try {
        $stmt = $pdo->prepare("INSERT INTO flights (airline_id, destination_from, destination_to, departure_time, price) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$airline_id, $from, $to, $departure, $price]);
        $success = "Let byl úspěšně přidán do systému!";
    } catch (PDOException $e) {
        $error = "Chyba při ukládání: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Přidat let - Administrace</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: bold; margin-bottom: 8px; color: #34495e; }
        input, select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        .admin-nav { margin-bottom: 30px; display: flex; gap: 15px; align-items: center; }
        .success-msg { background: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container" style="max-width: 600px; margin-top: 50px;">
        <div class="admin-nav">
            <a href="../index.php" style="text-decoration: none;">← Hlavní web</a>
            <span style="color: #ccc;">|</span>
            <a href="manage_airlines.php" style="text-decoration: none; color: var(--primary);">Správa aerolinek & Log</a>
        </div>

        <h1>➕ Přidat nový let</h1>

        <?php if (isset($success)) echo "<div class='success-msg'>$success</div>"; ?>
        <?php if (isset($error)) echo "<div style='color:red; margin-bottom:20px;'>$error</div>"; ?>

        <form method="POST" class="airline-card" style="background: white; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            
            <div class="form-group">
                <label>Letecká společnost:</label>
                <select name="airline_id" required>
                    <option value="">-- Vyberte dopravce --</option>
                    <?php foreach ($airlines as $a): ?>
                        <option value="<?= $a['id'] ?>">
                            <?= htmlspecialchars($a['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small><a href="manage_airlines.php" style="color: #666; font-size: 0.8rem;">+ Přidat novou aerolinku / nahrát logo</a></small>
            </div>

            <div style="display: flex; gap: 20px;">
                <div class="form-group" style="flex: 1;">
                    <label>Odkud (Město):</label>
                    <input type="text" name="destination_from" placeholder="např. Praha" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Kam (Město):</label>
                    <input type="text" name="destination_to" placeholder="např. Lisabon" required>
                </div>
            </div>

            <div class="form-group">
                <label>Datum a čas odletu:</label>
                <input type="datetime-local" name="departure_time" required>
            </div>

            <div class="form-group">
                <label>Cena letenky (Kč):</label>
                <input type="number" name="price" step="1" min="1" placeholder="např. 2500" required>
            </div>

            <button type="submit" class="btn" style="width: 100%; background: var(--primary); padding: 15px; font-size: 1rem;">
                🚀 Uložit let do nabídky
            </button>
        </form>
    </div>
</body>
</html>