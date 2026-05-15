<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Dynamická detekce cesty - opravuje cesty pro soubory v admin/ i v kořenu
$base_path = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../' : '';
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TripDesk - Rezervační systém</title>
    
    <link rel="stylesheet" href="<?= $base_path ?>assets/css/style.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Moderní barvy a reset pro navigaci */
        :root {
            --primary: #1A365D;    /* Námořnická modř */
            --secondary: #38B2AC;  /* Tyrkysová */
            --success: #48BB78;    /* Zelená */
            --bg-light: #F7FAFC;
        }

        body { font-family: 'Inter', sans-serif; margin: 0; background-color: var(--bg-light); }

        nav {
            background: white;
            padding: 0.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: transform 0.2s;
        }

        .logo-link:hover { transform: scale(1.02); }
        .logo-img { height: 50px; width: auto; }

        nav ul {
            list-style: none;
            display: flex;
            align-items: center;
            gap: 20px;
            margin: 0;
            padding: 0;
        }

        nav ul li a {
            text-decoration: none;
            color: var(--primary);
            font-weight: 600;
            font-size: 0.95rem;
            transition: color 0.2s;
        }

        nav ul li a:hover { color: var(--secondary); }

        /* Styly pro tlačítka v menu */
        .btn-register {
            background: var(--success);
            color: white !important;
            padding: 8px 18px;
            border-radius: 8px;
            transition: opacity 0.2s !important;
        }

        .btn-register:hover { opacity: 0.9; }

        .btn-admin {
            color: var(--secondary) !important;
            border: 1px solid var(--secondary);
            padding: 5px 10px;
            border-radius: 6px;
        }

        .btn-admin:hover {
            background: var(--secondary);
            color: white !important;
        }

        .logout-link {
            color: #e53e3e !important;
            font-size: 0.85rem;
            opacity: 0.8;
        }

        .logout-link:hover { opacity: 1; text-decoration: underline; }

        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
    </style>
</head>
<body>

<nav>
    <a href="<?= $base_path ?>index.php" class="logo-link">
        <img src="<?= $base_path ?>assets/img/logo.svg" alt="TripDesk Logo" class="logo-img">
    </a>

    <ul>
        <li><a href="<?= $base_path ?>index.php">Domů</a></li>
        
        <?php if(isset($_SESSION['user_id'])): ?>
            <li><a href="<?= $base_path ?>profile.php">Můj Profil</a></li>
            
            <li><a href="<?= $base_path ?>manage_reservations.php">
                <?php echo ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'editor') ? 'Správa rezervací' : 'Moje rezervace'; ?>
            </a></li>

            <?php if($_SESSION['role'] === 'admin'): ?>
                <li><a href="<?= $base_path ?>admin/manage_users.php" class="btn-admin">Uživatelé</a></li>
                <li><a href="<?= $base_path ?>admin/add_flight.php" class="btn-admin">+ Přidat let</a></li>
            <?php endif; ?>
            
            <li><a href="<?= $base_path ?>logout.php" class="logout-link">Odhlásit (<?= htmlspecialchars($_SESSION['username']); ?>)</a></li>
        <?php else: ?>
            <li><a href="<?= $base_path ?>login.php">Přihlásit</a></li>
            <li><a href="<?= $base_path ?>register.php" class="btn-register">Registrace</a></li>
        <?php endif; ?>
    </ul>
</nav>

<div class="container">