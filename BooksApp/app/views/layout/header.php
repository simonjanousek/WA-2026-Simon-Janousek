<header class="main-header">
    <div class="header-content">
        <a href="index.php" class="logo">
            <span class="logo-icon">📚</span>
            <h1>Knihovna</h1>
        </a>

        <nav class="main-nav">
            <ul class="nav-links">
                <li>
                    <a href="index.php?url=book/index" class="nav-link">Seznam knih</a>
                </li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <li>
                        <a href="index.php?url=book/create" class="btn-add">
                            <span class="plus">+</span> Přidat knihu
                        </a>
                    </li>
                    <li class="user-info">
                        <span class="user-greeting">Ahoj, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong></span>
                    </li>
                    <li>
                        <a href="index.php?url=auth/logout" class="btn-logout">Odhlásit</a>
                    </li>
                <?php else: ?>
                    <li>
                        <a href="index.php?url=auth/login" class="nav-link">Přihlásit</a>
                    </li>
                    <li>
                        <a href="index.php?url=auth/register" class="btn-register">Registrace</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<style>
    /* --- SJEDNOCENÝ STYL HEADERU --- */
    .main-header {
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        padding: 0.75rem 0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        font-family: 'Inter', system-ui, sans-serif;
    }

    .header-content {
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo {
        display: flex;
        align-items: center;
        text-decoration: none;
        gap: 0.75rem;
    }

    .logo-icon { font-size: 1.5rem; }

    .logo h1 {
        font-size: 1.25rem;
        font-weight: 800;
        margin: 0;
        background: linear-gradient(135deg, #4f46e5, #8b5cf6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        letter-spacing: -0.025em;
    }

    .nav-links {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .nav-link {
        text-decoration: none;
        color: #64748b;
        font-weight: 600;
        font-size: 0.9rem;
        transition: color 0.2s;
    }

    .nav-link:hover { color: #4f46e5; }

    /* --- TLAČÍTKA V NAVIGACI --- */
    .btn-add {
        background: #4f46e5;
        color: #ffffff;
        text-decoration: none;
        padding: 0.5rem 1rem;
        border-radius: 0.75rem;
        font-weight: 700;
        font-size: 0.85rem;
        transition: all 0.3s;
        box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
    }

    .btn-add:hover {
        background: #4338ca;
        transform: translateY(-1px);
        box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
    }

    .btn-register {
        background: #f1f5f9;
        color: #1e293b;
        text-decoration: none;
        padding: 0.5rem 1rem;
        border-radius: 0.75rem;
        font-weight: 700;
        font-size: 0.85rem;
        transition: all 0.2s;
    }

    .btn-register:hover { background: #e2e8f0; }

    .btn-logout {
        text-decoration: none;
        color: #ef4444;
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .btn-logout:hover { color: #b91c1c; }

    .user-info {
        color: #64748b;
        font-size: 0.9rem;
        border-left: 1px solid #e2e8f0;
        padding-left: 1.5rem;
    }

    .user-greeting strong { color: #1e293b; }

    /* --- ALERTY (Tvé původní, mírně učesané) --- */
    .alerts-container {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 2000;
        width: 90%;
        max-width: 450px;
    }

    .alert {
        display: flex;
        align-items: center;
        padding: 1rem;
        margin-bottom: 0.75rem;
        border-radius: 1rem;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        font-family: inherit;
        animation: alertSlideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1px solid rgba(0,0,0,0.05);
        backdrop-filter: blur(8px);
    }

    .alert-success { background: rgba(220, 252, 231, 0.95); color: #166534; border-left: 5px solid #22c55e; }
    .alert-error   { background: rgba(254, 242, 242, 0.95); color: #991b1b; border-left: 5px solid #ef4444; }
    .alert-notice  { background: rgba(254, 252, 232, 0.95); color: #854d0e; border-left: 5px solid #eab308; }
    
    .alert-text { flex-grow: 1; font-size: 0.9rem; font-weight: 600; }
    .alert-close { cursor: pointer; opacity: 0.5; font-size: 1.5rem; line-height: 1; padding-left: 1rem; }

    @keyframes alertSlideIn {
        from { opacity: 0; transform: translate(-50%, -20px); }
        to { opacity: 1; transform: translate(-50%, 0); }
    }

    /* Responzivita */
    @media (max-width: 768px) {
        .header-content { flex-direction: column; gap: 1rem; }
        .nav-links { gap: 1rem; flex-wrap: wrap; justify-content: center; }
        .user-info { border: none; padding: 0; }
    }
</style>

<?php if (isset($_SESSION['messages'])): ?>
    <div class="alerts-container">
        <?php foreach ($_SESSION['messages'] as $type => $msgs): ?>
            <?php foreach ($msgs as $msg): ?>
                <div class="alert alert-<?= $type ?>">
                    <span class="alert-text">
                        <?= ($type === 'error' ? '⚠️ ' : '✅ ') . htmlspecialchars($msg) ?>
                    </span>
                    <span class="alert-close" onclick="this.parentElement.remove()">&times;</span>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
        <?php unset($_SESSION['messages']); ?>
    </div>
<?php endif; ?>