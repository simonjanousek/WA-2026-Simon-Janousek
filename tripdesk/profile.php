<?php
require_once 'includes/db.php';
session_start();

// bewz prihlaseni na login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_msg = "";

// 1. Načtení aktuálních dat uživatele z db
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// 2. Zpracování formuláře (Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_email = $_POST['email'];
    $new_password = $_POST['password'];

    // kdyz nove heslo tak update obojiho jinak jen email
    if (!empty($new_password)) {
        $stmt = $pdo->prepare("UPDATE users SET email = ?, password = ? WHERE id = ?");
        $stmt->execute([$new_email, $new_password, $user_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmt->execute([$new_email, $user_id]);
    }
    $success_msg = "Údaje byly úspěšně aktualizovány!";
    // nacteni dat pro zobrazeni
    header("Location: profile.php?success=1");
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="airline-card" style="max-width: 500px; margin: 30px auto;">
        <h2>Můj profil (TripDesk)</h2>
        
        <?php if (isset($_GET['success'])) echo "<p style='color:green;'>Údaje byly uloženy!</p>"; ?>

        <form method="POST">
            <div style="margin-bottom: 15px;">
                <label>Uživatelské jméno (nelze změnit):</label><br>
                <input type="text" value="<?= htmlspecialchars($user['username']) ?>" readonly style="background: #eee; width: 100%;">
            </div>

            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label>Jméno:</label><br>
                    <input type="text" value="<?= htmlspecialchars($user['first_name'] ?? 'Nezadáno') ?>" readonly style="background: #eee; width: 100%;">
                </div>
                <div style="flex: 1;">
                    <label>Příjmení:</label><br>
                    <input type="text" value="<?= htmlspecialchars($user['last_name'] ?? 'Nezadáno') ?>" readonly style="background: #eee; width: 100%;">
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <label>E-mail (můžete změnit):</label><br>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required style="width: 100%;">
            </div>

            <div style="margin-bottom: 15px;">
                <label>Nové heslo (nechte prázdné, pokud nechcete měnit):</label><br>
                <input type="password" name="password" placeholder="********" style="width: 100%;">
            </div>

            <button type="submit" class="btn">Uložit změny</button>
        </form>
    </div>
</div>

<?php //uzavreni footerem ?>