<?php
// prima cesta - zacatek v korenu
require_once 'includes/db.php';
session_start();

// prihlaseny uzivatel na hlavni stranku
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error_msg = null;
if (isset($_GET['error']) && $_GET['error'] === 'invalid_credentials') {
    $error_msg = "Špatné uživatelské jméno nebo heslo!";
}

include 'includes/header.php'; 
?>

<div class="container" style="margin-top: 60px;">
    <form method="POST" action="actions/process_login.php" class="card" style="max-width: 400px; margin: auto; padding: 35px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
        
        <h2 style="text-align: center; color: var(--primary); margin-bottom: 30px;">Přihlášení do TripDesk</h2>

        <?php if ($error_msg): ?>
            <div style="background: #fff5f5; color: #c53030; padding: 12px; border-radius: 8px; border: 1px solid #feb2b2; margin-bottom: 20px; text-align: center; font-size: 0.9rem;">
                ⚠️ <?= $error_msg ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'registrace_ok'): ?>
            <div style="background: #f0fff4; color: #2f855a; padding: 12px; border-radius: 8px; border: 1px solid #c6f6d5; margin-bottom: 20px; text-align: center; font-size: 0.9rem;">
                ✅ Registrace byla úspěšná. Můžete se přihlásit!
            </div>
        <?php endif; ?>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Uživatelské jméno</label>
            <input type="text" name="username" placeholder="Zadejte jméno" required 
                   style="width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 8px;">
        </div>

        <div style="margin-bottom: 25px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Heslo</label>
            <input type="password" name="password" placeholder="Zadejte heslo" required 
                   style="width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 8px;">
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; font-weight: bold; border-radius: 8px; font-size: 1rem;">
            Vstoupit do systému
        </button>

        <p style="text-align: center; margin-top: 25px; color: #718096; font-size: 0.9rem;">
            Ještě nemáte účet? <a href="register.php" style="color: var(--secondary); font-weight: bold; text-decoration: none;">Zaregistrujte se</a>
        </p>
    </form>
</div>

<footer style="margin-top: 50px; text-align: center; color: #a0aec0; font-size: 0.8rem;">
    TripDesk Secure Login System &copy; 2026
</footer>

</body>
</html>