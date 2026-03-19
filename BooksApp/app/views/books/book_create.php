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

    <form action="../../controllers/BookController.php" method="post" enctype="multipart/form-data">
        
        <div>
            <label for="title">Název knihy <span>*</span></label>
            <input type="text" id="title" name="title" required>
        </div>

        <div>
            <label for="author">Autor <span>*</span></label>
            <input type="text" id="author" name="author" placeholder="Jmeno a prijmeni" required>
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
            <input type="number" id="price" name="price" step="0,5">
        </div>

        <div>
            <label for="link">Odkaz <span></span></label>
            <input type="number" id="link" name="link">
        </div>

        
        <div>
            <label for="description">Popis <span></span></label>
            <input type="text" id="description" name="description" rows="5">
        </div>







        <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Obrázky (můžete nahrát více)</label>
                        <label class="flex flex-col items-center justify-center w-full p-6 border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer hover:border-indigo-500 hover:bg-indigo-50 transition">
                            <span class="text-gray-600 font-medium">Klikni pro výběr souborů</span>
                            <span class="text-sm text-gray-400 mt-1">JPG / PNG / WebP – více souborů najednou</span>
                            <input type="file" id="images" name="images[]" multiple accept="image/*" class="hidden">
                        </label>
                    </div>



    

        


        <button type="submit">Uložit do DB</button>

    </form>
</div>

</body>
</html>