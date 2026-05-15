<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seznam knih | Knihovna</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --primary-light: #eef2ff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --bg: #f8fafc;
            --white: #ffffff;
            --border: #e2e8f0;
            
            /* Tvoje barvy pro texty */
            --emerald-600: #059669; /* Úprava na tmavší zelenou pro čitelnost na bílé */
            --slate-500: #64748b;
            --slate-700: #334155;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            margin: 0;
            line-height: 1.5;
        }

        main {
            max-width: 1200px;
            margin: 2.5rem auto;
            padding: 0 1.5rem;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header-section { margin-bottom: 2rem; }
        h2 { font-size: 2rem; font-weight: 800; color: var(--text-main); margin: 0; letter-spacing: -0.025em; }
        .description { color: var(--text-muted); margin: 0.5rem 0 0 0; font-size: 1.1rem; }

        /* --- Tabulka --- */
        .table-container {
            background: var(--white);
            border-radius: 1.25rem;
            border: 1px solid var(--border);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        table { width: 100%; border-collapse: collapse; text-align: left; }

        th {
            background: #fcfcfd;
            padding: 1.25rem 1.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        /* Pomocné třídy pro tvůj styl */
        .text-slate-300 { color: var(--slate-700); } /* Na bílém pozadí musí být slate tmavší */
        .text-emerald-400 { color: var(--emerald-600); } 
        .text-slate-400 { color: var(--text-muted); }
        .font-medium { font-weight: 500; }
        .font-mono { font-family: ui-monospace, SFMono-Regular, monospace; }
        .px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
        .py-4 { padding-top: 1rem; padding-bottom: 1rem; }

        tbody tr:hover { background-color: #fcfdfe; }

        /* --- Obrázky --- */
        .img-cell { width: 80px; }
        .book-thumbnail {
            width: 70px;
            height: auto;
            border-radius: 6px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border);
            display: block;
        }

        .no-photo {
            width: 70px;
            aspect-ratio: 2/3;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            color: #94a3b8;
            border-radius: 6px;
            border: 1px dashed #cbd5e1;
            text-align: center;
            font-weight: 600;
        }

        .book-title { font-weight: 700; color: var(--text-main); font-size: 1.05rem; }

        /* --- Akce --- */
        .price-badge {
            background: var(--primary-light);
            color: var(--primary);
            padding: 0.4rem 0.8rem;
            border-radius: 2rem;
            font-weight: 700;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .actions-flex { display: flex; gap: 0.5rem; justify-content: flex-end; }
        .btn-action {
            text-decoration: none;
            font-size: 0.75rem;
            padding: 0.5rem 0.9rem;
            border-radius: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            transition: all 0.2s;
        }
        .btn-detail { background: #f1f5f9; color: #475569; }
        .btn-edit { background: #fef3c7; color: #92400e; }
        .btn-delete { background: #fee2e2; color: #b91c1c; }

        footer { text-align: center; padding: 4rem 2rem; color: var(--text-muted); font-size: 0.9rem; }
    </style>
</head>
<body>

<?php require_once '../app/views/layout/header.php'; ?>

<main>
    <div class="header-section">
        <h2>Dostupné knihy</h2>
        <p class="description">Kompletní přehled titulů s dynamickým náhledem obálek.</p>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th class="img-cell">Obálka</th>
                    <th class="px-6 py-4">Název knihy</th>
                    <th class="px-6 py-4">Autor</th>
                    <th class="px-6 py-4">Kategorie</th>
                    <th class="px-6 py-4">Rok</th>
                    <th class="px-6 py-4">Cena</th>
                    <th style="text-align: right;">Akce</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                <tr>
                    <td class="img-cell">
                        <?php 
                            $images = json_decode($book['images'], true);
                            if (!empty($images) && isset($images[0])): 
                                $imgName = htmlspecialchars($images[0]);
                        ?>
                                <img src="public/uploads/<?= $imgName ?>" class="book-thumbnail" onerror="this.src='uploads/<?= $imgName ?>'">
                            <?php else: ?>
                                <div class="no-photo">NENÍ<br>FOTO</div>
                            <?php endif; 
                        ?>
                    </td>

                    <td class="px-6 py-4">
                        <span class="book-title"><?= htmlspecialchars($book['title']) ?></span>
                    </td>

                    <td class="px-6 py-4 text-slate-300">
                        <?= htmlspecialchars($book['author']) ?>
                    </td>

                    <td class="px-6 py-4 text-emerald-400 font-medium">
                        <?= htmlspecialchars($book['category_name'] ?? 'Nezařazeno') ?>
                    </td>

                    <td class="px-6 py-4 text-slate-400 font-mono">
                        <?= htmlspecialchars($book['year']) ?>
                    </td>

                    <td class="px-6 py-4">
                        <span class="price-badge">
                            <?= number_format($book['price'], 2, ',', ' ') ?> Kč
                        </span>
                    </td>

                    <td class="px-6 py-4">
                        <div class="actions-flex">
                            <a href="index.php?url=book/show/<?= $book['id'] ?>" class="btn-action btn-detail">Detail</a>
                            
                            <?php 
                            $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
                            $isOwner = isset($_SESSION['user_id']) && isset($book['created_by']) && $_SESSION['user_id'] == $book['created_by'];

                            if ($isOwner || $isAdmin): 
                            ?>
                                <a href="index.php?url=book/edit/<?= $book['id'] ?>" class="btn-action btn-edit">Upravit</a>
                                <a href="index.php?url=book/delete/<?= $book['id'] ?>" 
                                   onclick="return confirm('Opravdu chcete tuto knihu smazat?')" 
                                   class="btn-action btn-delete">Smazat</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<footer>
    <p>&copy; 2026 Knihovna - Simon Janoušek</p>
</footer>

</body>
</html>