<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Přidat knihu</title>
</head>
<body>

<div>
    <h2>Přidat novou knihu</h2>
    <p>Vyplňte údaje a uložte knihu do databáze</p>

    <form action="index.php?url=book/store" method="post" enctype="multipart/form-data">
         
        <div>
            <label for="title">Název knihy <span>*</span></label>
            <input type="text" id="title" name="title" required>
        </div>

        <div>
            <label for="author">Autor <span>*</span></label>
            <input type="text" id="author" name="author" placeholder="Jméno a příjmení" required>
        </div>

        <div>
            <label for="category">Kategorie <span>*</span></label>
            <input type="text" id="category" name="category" required>
        </div>

        <div>
            <label for="isbn">ISBN <span>*</span></label>
            <input type="text" id="isbn" name="isbn" required>
        </div>

        <div>
            <label for="subcategory">Podkategorie <span>*</span></label>
            <input type="text" id="subcategory" name="subcategory" required>
        </div>

        <div>
            <label for="year">Rok <span>*</span></label>
            <input type="number" id="year" name="year" required>
        </div>

        <div>
            <label for="price">Cena <span></span></label>
            <input type="number" id="price" name="price" step="0.5">
        </div>

        <div>
            <label for="link">Odkaz <span></span></label>
            <input type="text" id="link" name="link">
        </div>

        <div>
            <label for="description">Popis <span></span></label>
            <textarea id="description" name="description" rows="5"></textarea>
        </div>

        <div>
            <label style="display: block; margin-bottom: 10px;">Obrázky (můžete nahrát více)</label>
            <div style="border: 2px dashed #ccc; padding: 20px; text-align: center;">
                <span class="text-gray-600 font-medium">Klikni pro výběr souborů</span><br>
                <span class="text-sm text-gray-400 mt-1">JPG / PNG / WebP</span>
                <input type="file" id="images" name="images[]" multiple accept="image/*">
            </div>
        </div>

        <div style="margin-top: 20px;">
            <button type="submit">Uložit do DB</button>
        </div>

    </form>
</div>

</body>
</html>