<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Přidat knihu</title>
</head>
<body>

<div>
    <h2>Přidat novou knihu</h2>
    <p>Vyplňte údaje a uložte knihu do databáze</p>

    <form action="">
        
        <div>
            <label for="title">Název knihy <span>*</span></label>
            <input type="text" id="title" name="title" required>
        </div>

        <div>
            <label for="author">Autor <span>*</span></label>
            <input type="text" id="author" name="author" required>
        </div>

        <div>
            <label for="category">Kategorie <span>*</span></label>
            <input type="text" id="category" name="category" required>
        </div>

        <div>
            <label for="subcategory">Podkategorie <span>*</span></label>
            <input type="text" id="subcategory" name="subcategory" required>
        </div>

        <div>
            <label for="year">Rok <span>*</span></label>
            <input type="number" id="year" name="year" required>
        </div>

        <button type="submit">Uložit do DB</button>

    </form>
</div>

</body>
</html>