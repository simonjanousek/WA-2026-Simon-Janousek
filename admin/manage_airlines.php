<?php
require_once '../includes/db.php';
session_start();

// Ochrana: Jen admin sem může
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Nepovolený přístup.");
}

$success = null;
$error = null;

// --- ZPRACOVÁNÍ FORMULÁŘE (PŘIDÁNÍ AEROLINKY) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_airline'])) {
    $name = trim($_POST['airline_name']);
    
    if (!empty($name)) {
        // Výchozí logo, pokud se nic nenahraje (ujisti se, že tento soubor existuje v assets/img/)
        $logo_db_path = "logo.svg"; 

        // Kontrola, zda byl nahrán soubor
        if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === 0) {
            $target_dir = "../assets/img/";
            
            // Vytvoření složky, pokud neexistuje
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $file_info = pathinfo($_FILES["logo_file"]["name"]);
            $extension = strtolower($file_info['extension']);
            
            // Povolené formáty
            $allowed = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
            
            if (in_array($extension, $allowed)) {
                // Vytvoříme čistý název souboru bez diakritiky a mezer
                $safe_name = time() . "_" . preg_replace('/[^a-z0-9]/', '', strtolower($name)) . "." . $extension;
                $target_file = $target_dir . $safe_name;

                if (move_uploaded_file($_FILES["logo_file"]["tmp_name"], $target_file)) {
                    // Do db jen nazev souboru cesty reseny v kodu
                    $logo_db_path = $safe_name;
                }
            } else {
                $error = "Nepovolený formát obrázku (povolené: JPG, PNG, SVG, WEBP).";
            }
        }

        if (!$error) {
            $stmt = $pdo->prepare("INSERT INTO airlines (name, logo) VALUES (?, ?)");
            $stmt->execute([$name, $logo_db_path]);
            $success = "Aerolinka '$name' byla úspěšně vytvořena.";
        }
    } else {
        $error = "Název aerolinky nesmí být prázdný.";
    }
}

// --- SMAZÁNÍ AEROLINKY ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM airlines WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: manage_airlines.php?msg=deleted");
    exit();
}

$airlines = $pdo->query("SELECT * FROM airlines ORDER BY name ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Správa aerolinek - TripDesk</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container" style="max-width: 900px; margin-top: 50px;">
        <header style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
            <h1>🏢 Správa aerolinek</h1>
            <a href="add_flight.php" class="btn" style="background: #34495e; text-decoration:none;">← Zpět</a>
        </header>

        <?php if ($success): ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <section style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eee;">
            <h3 style="margin-top:0;">➕ Přidat novou společnost</h3>
            <form method="POST" enctype="multipart/form-data">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label style="display:block; margin-bottom:8px; font-weight:bold;">Název společnosti:</label>
                        <input type="text" name="airline_name" placeholder="např. Lufthansa" required style="width:100%; padding:12px; border: 1px solid #ddd; border-radius: 8px;">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:8px; font-weight:bold;">Logo (obrázek):</label>
                        <input type="file" name="logo_file" accept="image/*" style="width:100%; padding:8px;">
                    </div>
                </div>
                <button type="submit" name="add_airline" class="btn" style="background: #27ae60; width: 100%; font-weight: bold; padding: 12px;">
                    🚀 Uložit společnost
                </button>
            </form>
        </section>

        <h3 style="margin-top: 40px;">Existující společnosti</h3>
        <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eee;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #2c3e50; color: white; text-align: left;">
                        <th style="padding: 15px;">Logo</th>
                        <th style="padding: 15px;">Název</th>
                        <th style="padding: 15px;">Název souboru</th>
                        <th style="padding: 15px; text-align: center;">Akce</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($airlines as $a): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px;">
    <?php 
        // Pokud cesta v DB začíná na "assets", přidáme jen "../" pro výstup ze složky admin
        //oprava
        $path = $a['logo']; 
        $final_src = (strpos($path, 'assets/') === 0) ? "../" . $path : "../assets/img/" . $path;
    ?>
    <img src="<?= htmlspecialchars($final_src) ?>" style="width: 40px; height: 40px; object-fit: contain;">
</td>
                            <td style="padding: 15px; font-weight: bold; color: #2c3e50;"><?= htmlspecialchars($a['name']) ?></td>
                            <td style="padding: 15px; font-size: 0.8rem; color: #95a5a6;"><?= htmlspecialchars($a['logo']) ?></td>
                            <td style="padding: 15px; text-align: center;">
                                <a href="?delete=<?= $a['id'] ?>" onclick="return confirm('Opravdu smazat?')" style="color: #e74c3c; text-decoration: none; font-weight: bold; border: 1px solid #e74c3c; padding: 5px 10px; border-radius: 6px;">Smazat</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>