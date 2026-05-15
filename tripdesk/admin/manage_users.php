<?php
require_once '../includes/db.php';
session_start();

// 1. Ochrana: Jen admin sem může
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Nepovolený přístup.");
}

$msg = "";

// 2. Zpracování změny role
if (isset($_POST['update_role'])) {
    $u_id = $_POST['user_id'];
    $new_role = $_POST['role'];
    
    // Pojistka: Admin si nemůže sám sobě sebrat práva admina (aby se nezablokoval)
    if ($u_id == $_SESSION['user_id'] && $new_role !== 'admin') {
        $msg = "Chyba: Nemůžete si sami změnit roli z Admina na nižší.";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$new_role, $u_id]);
        $msg = "Role uživatele byla úspěšně změněna.";
    }
}

// 3. Načtení zprávy z URL (po smazání)
if (isset($_GET['msg']) && $_GET['msg'] === 'user_deleted') {
    $msg = "Uživatel byl trvale smazán ze systému.";
}

// 4. Načtení všech uživatelů
$users = $pdo->query("SELECT id, username, email, first_name, last_name, role FROM users ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Správa uživatelů - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; }
        .badge-admin { background: #e74c3c; color: white; }
        .badge-editor { background: #f39c12; color: white; }
        .badge-user { background: #95a5a6; color: white; }
        .btn-delete { color: #e74c3c; text-decoration: none; font-weight: bold; margin-left: 10px; }
        .btn-delete:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container" style="max-width: 1000px; margin-top: 40px;">
        <header style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <a href="../index.php" style="text-decoration: none; color: #666;">← Zpět na hlavní web</a>
                <h1 style="margin: 10px 0 0 0;">👥 Správa uživatelů</h1>
            </div>
        </header>

        <?php if (!empty($msg)): ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <table style="width: 100%; border-collapse: collapse; background: white; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden;">
            <thead>
                <tr style="background: #34495e; color: white; text-align: left;">
                    <th style="padding: 15px;">Uživatel / Jméno</th>
                    <th style="padding: 15px;">Kontakt</th>
                    <th style="padding: 15px;">Role</th>
                    <th style="padding: 15px;">Akce (Změna role / Smazat)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 15px;">
                            <strong><?= htmlspecialchars($u['username']) ?></strong>
                            <div style="font-size: 0.85rem; color: #7f8c8d;">
                                <?= htmlspecialchars($u['first_name'] . " " . $u['last_name']) ?>
                            </div>
                        </td>
                        <td style="padding: 15px; font-size: 0.9rem;">
                            <?= htmlspecialchars($u['email']) ?>
                        </td>
                        <td style="padding: 15px;">
                            <span class="badge badge-<?= $u['role'] ?>">
                                <?= $u['role'] ?>
                            </span>
                        </td>
                        <td style="padding: 15px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <form method="POST" style="display: flex; gap: 5px; align-items: center;">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <select name="role" style="padding: 6px; border-radius: 4px; border: 1px solid #ddd;">
                                        <option value="user" <?= $u['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                        <option value="editor" <?= $u['role'] === 'editor' ? 'selected' : '' ?>>Editor</option>
                                        <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                    <button type="submit" name="update_role" class="btn" style="padding: 6px 12px; font-size: 0.75rem; background: #3498db;">
                                        Uložit
                                    </button>
                                </form>

                                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                    <a href="delete_user.php?id=<?= $u['id'] ?>" 
                                       class="btn-delete"
                                       onclick="return confirm('⚠️ OPRAVDU SMAZAT? \nSmažete uživatele <?= htmlspecialchars($u['username']) ?> a všechny jeho rezervace. Tuto akci nelze vrátit!')">
                                       🗑️ Smazat
                                    </a>
                                <?php else: ?>
                                    <span style="color: #bdc3c7; font-size: 0.8rem; font-style: italic;">(To jste vy)</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>