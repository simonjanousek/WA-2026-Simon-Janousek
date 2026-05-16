<?php 
session_start();
include 'includes/header.php'; 

// Zpracování chybových hlášek z URL
$error_msg = null;
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'weak_password') $error_msg = "Heslo nesplňuje požadavky (velké písmeno, číslo a znak).";
    if ($_GET['error'] === 'exists') $error_msg = "Uživatelské jméno nebo e-mail již existuje.";
    if ($_GET['error'] === 'db_error') $error_msg = "Chyba na straně databáze. Zkuste to později.";
}
?>

<div class="container" style="margin-top: 50px;">
    <form method="POST" action="actions/process_register.php" class="card" style="max-width: 450px; margin: auto; padding: 30px;">
        <h2 style="text-align: center; color: var(--primary); margin-bottom: 25px;">Registrace uživatele</h2>
        
        <?php if ($error_msg): ?>
            <div style="background: #fff5f5; color: #c53030; padding: 15px; border-radius: 8px; border: 1px solid #feb2b2; margin-bottom: 20px; font-size: 0.9rem;">
                ⚠️ <?= $error_msg ?>
            </div>
        <?php endif; ?>

        <div style="margin-bottom: 15px;">
            <label style="font-weight: 600;">Uživatelské jméno:</label>
            <input type="text" name="username" placeholder="Uživatelské jméno" required style="width:100%; padding: 12px; border-radius: 6px; border: 1px solid #ddd;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="font-weight: 600;">E-mail:</label>
            <input type="email" name="email" placeholder="vas@email.cz" required style="width:100%; padding: 12px; border-radius: 6px; border: 1px solid #ddd;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="font-weight: 600;">Heslo:</label>
            <input type="password" name="password" placeholder="••••••••" required style="width:100%; padding: 12px; border-radius: 6px; border: 1px solid #ddd;">
            <small style="color: #718096; display: block; margin-top: 5px; line-height: 1.4;">
                Musí mít aspoň 8 znaků, obsahovat velké písmeno, číslo a **speciální znak (@, #, $)**.
            </small>
        </div>

        <div style="display: flex; gap: 10px; margin-bottom: 20px;">
            <div style="flex: 1;">
                <label style="font-weight: 600;">Jméno:</label>
                <input type="text" name="first_name" placeholder="Jan" style="width:100%; padding: 12px; border-radius: 6px; border: 1px solid #ddd;">
            </div>
            <div style="flex: 1;">
                <label style="font-weight: 600;">Příjmení:</label>
                <input type="text" name="last_name" placeholder="Novák" style="width:100%; padding: 12px; border-radius: 6px; border: 1px solid #ddd;">
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-weight: bold; font-size: 1rem;">
            Zaregistrovat se
        </button>

        <p style="text-align: center; margin-top: 20px; font-size: 0.9rem;">
            Už máte účet? <a href="login.php" style="color: var(--secondary); font-weight: bold;">Přihlaste se</a>
        </p>
    </form>
</div>