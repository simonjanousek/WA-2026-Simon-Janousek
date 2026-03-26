<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Přidat knihu</title>
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
    <h2>dostupne knihy</h2>
    <p>zde se objevi seznam knih nactenych z databaze</p>
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
                <td><?= htmlspecialchars($book['title']) ?></td>
                <td><?= htmlspecialchars($book['author']) ?></td>
                <td><?= htmlspecialchars($book['isbn']) ?></td>
                <td><?= htmlspecialchars($book['price']) ?> Kč</td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</main>

<footer>
    <p>&copy; WA 2026 - vyukovy projekt</p>
</footer>
</body>
</html>