<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Přidat knihu</title>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg: #f8fafc;
            --white: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --input-bg: #ffffff;
            --primary-light: rgba(79, 70, 229, 0.1);
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            margin: 0;
            min-height: 100vh;
            padding: 2rem 0;
        }

        .container {
            background: var(--white);
            width: 100%;
            max-width: 650px;
            margin: 0 auto;
            padding: 2.5rem;
            border-radius: 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border);
        }

        h2 {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0 0 0.5rem 0;
            letter-spacing: -0.025em;
        }

        .subtitle {
            color: var(--text-muted);
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .full-width {
            grid-column: span 2;
        }

        label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-main);
        }

        label span {
            color: #ef4444;
        }

        input, textarea, select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            font-family: inherit;
            font-size: 0.95rem;
            transition: all 0.2s;
            box-sizing: border-box;
            background: var(--input-bg);
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-light);
        }

        .file-upload {
            border: 2px dashed var(--border);
            padding: 1.5rem;
            border-radius: 1rem;
            text-align: center;
            background: #fafafa;
            cursor: pointer;
            transition: border-color 0.2s;
        }

        .file-upload:hover {
            border-color: var(--primary);
        }

        .file-upload-label {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .file-upload-label b {
            color: var(--primary);
            display: block;
            margin-bottom: 0.25rem;
        }

        button {
            width: 100%;
            padding: 1rem;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 1.5rem;
        }

        button:hover {
            background-color: var(--primary-hover);
        }

        @media (max-width: 640px) {
            .form-grid { grid-template-columns: 1fr; }
            .full-width { grid-column: span 1; }
            .container { width: 90%; padding: 1.5rem; }
        }
    </style>
</head>
<body>
    <?php require_once '../app/views/layout/header.php'; ?>

    <div class="container">
        <h2>Přidat novou knihu</h2>
        <p class="subtitle">Vyplňte údaje a uložte knihu do databáze</p>

        <form action="index.php?url=book/store" method="post" enctype="multipart/form-data">
            
            <div class="form-grid">
                <div class="full-width">
                    <label for="title">Název knihy <span>*</span></label>
                    <input type="text" id="title" name="title" placeholder="např. Velký Gatsby" required>
                </div>

                <div>
                    <label for="author">Autor <span>*</span></label>
                    <input type="text" id="author" name="author" placeholder="Jméno a příjmení" required>
                </div>

                <div>
                    <label for="isbn">ISBN <span>*</span></label>
                    <input type="text" id="isbn" name="isbn" placeholder="978-..." required>
                </div>

                <div>
                    <label for="category">Kategorie <span>*</span></label>
                    <select id="category" name="category" required>
                        <option value="">-- Vyberte kategorii --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat['id']) ?>">
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="subcategory">Podkategorie <span>*</span></label>
                    <select id="subcategory" name="subcategory" required>
                        <option value="">-- Vyberte podkategorii --</option>
                        <?php foreach ($subcategories as $sub): ?>
                            <option value="<?= htmlspecialchars($sub['id']) ?>">
                                <?= htmlspecialchars($sub['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="year">Rok vydání <span>*</span></label>
                    <input type="number" id="year" name="year" placeholder="2024" required>
                </div>

                <div>
                    <label for="price">Cena (Kč)</label>
                    <input type="number" id="price" name="price" step="0.5" placeholder="0.00">
                </div>

                <div class="full-width">
                    <label for="link">Odkaz na knihu</label>
                    <input type="url" id="link" name="link" placeholder="https://...">
                </div>

                <div class="full-width">
                    <label for="description">Stručný popis</label>
                    <textarea id="description" name="description" rows="4" placeholder="O čem kniha je..."></textarea>
                </div>

                <div class="full-width">
                    <label>Obrázky knihy</label>
                    <div class="file-upload" onclick="document.getElementById('images').click()">
                        <span class="file-upload-label">
                            <b id="file-title">Klikni pro výběr souborů</b>
                            <span id="file-info">JPG, PNG nebo WebP (můžeš i více najednou)</span>
                        </span>
                        <input type="file" id="images" name="images[]" multiple accept="image/*" style="display: none;">
                    </div>
                </div>
            </div>

            <button type="submit">Uložit do databáze</button>
        </form>
    </div>

    <script>
        const fileInput = document.getElementById('images');
        const fileTitle = document.getElementById('file-title');
        const fileInfo = document.getElementById('file-info');

        if (fileInput) {
            fileInput.addEventListener('change', function(event) {
                const files = event.target.files;
                
                if (files.length === 0) {
                    fileTitle.textContent = 'Klikni pro výběr souborů';
                    fileTitle.style.color = 'var(--primary)';
                    fileInfo.textContent = 'JPG, PNG nebo WebP (můžeš i více najednou)';
                } else if (files.length === 1) {
                    fileTitle.textContent = 'Soubor připraven';
                    fileTitle.style.color = '#22c55e'; // Zelená
                    fileInfo.textContent = 'Vybráno: ' + files[0].name;
                } else {
                    fileTitle.textContent = 'Soubory připraveny';
                    fileTitle.style.color = '#22c55e'; // Zelená
                    fileInfo.textContent = 'Vybráno celkem: ' + files.length + ' souborů';
                }
            });
        }
    </script>
</body>
</html>