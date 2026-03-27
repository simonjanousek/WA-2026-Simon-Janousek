<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikace Knihovna</title>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #eef2ff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --bg: #f8fafc;
            --white: #ffffff;
            --border: #e2e8f0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            margin: 0;
            line-height: 1.5;
        }

        header {
            background: var(--white);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        header h1 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 1.5rem;
            margin: 0;
        }

        nav a {
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        nav a:hover {
            color: var(--primary);
        }

        main {
            max-width: 1000px;
            margin: 3rem auto;
            padding: 0 1.5rem;
        }

        h2 {
            font-size: 1.875rem;
            font-weight: 700;
            letter-spacing: -0.025em;
            margin-bottom: 0.5rem;
        }

        main p {
            color: var(--text-muted);
            margin-bottom: 2rem;
        }

        /* Moderní pojetí tabulky */
        .table-container {
            background: var(--white);
            border-radius: 1rem;
            border: 1px solid var(--border);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background: var(--bg);
            padding: 1rem 1.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 1.25rem 1.5rem;
            font-size: 0.95rem;
            border-bottom: 1px solid var(--border);
        }

        tr:last-child td {
            border-bottom: none;
        }

        tbody tr:hover {
            background-color: var(--primary-light);
            transition: background 0.2s;
        }

        /* Badge pro cenu */
        .price-badge {
            background: var(--primary-light);
            color: var(--primary);
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 0.875rem;
            display: inline-block;
        }

        footer {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <header>
        <h1>Aplikace knihovna</h1>
        <nav>
            <ul>
                <li><a href="index.php?url=book/index">seznam knih (domů)</a></li>
                <li><a href="index.php?url=book/create">přidat novou knihu</a></li>
            </ul>
        </nav>
    </header>

<main>
    <h2>Dostupné knihy</h2>
    <p>Zde se objeví seznam knih načtených z databáze</p>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Název</th>
                    <th>Autor</th>
                    <th>ISBN</th>
                    <th>Cena</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td style="font-weight: 500;"><?= htmlspecialchars($book['title']) ?></td>
                        <td style="color: var(--text-muted);"><?= htmlspecialchars($book['author']) ?></td>
                        <td style="font-family: monospace; font-size: 0.85rem;"><?= htmlspecialchars($book['isbn']) ?></td>
                        <td>
                            <span class="price-badge">
                                <?= str_replace('.', ',', htmlspecialchars($book['price'])) ?> Kč
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<footer>
    <p>&copy; WA 2026 - výukový projekt</p>
</footer>
</body>
</html>