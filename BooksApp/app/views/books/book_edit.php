<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upravit knihu | <?= htmlspecialchars($book['title']) ?></title>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --primary-light: #eef2ff;
            --secondary: #8b5cf6;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --bg: #f8fafc;
            --white: #ffffff;
            --border: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --success: #22c55e;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: var(--bg);
            background-image: radial-gradient(var(--border) 1px, transparent 1px);
            background-size: 20px 20px;
            color: var(--text-main);
            margin: 0;
            line-height: 1.5;
            padding-bottom: 3rem;
        }

        .main-wrapper {
            max-width: 800px;
            margin: 3rem auto;
            padding: 0 1.5rem;
            animation: slideUpFade 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* --- TLAČÍTKO ZPĚT --- */
        .back-link {
            display: inline-flex;
            align-items: center;
            padding: 0.6rem 1.2rem;
            background: var(--white);
            border-radius: 99px;
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            transition: all 0.3s ease;
            margin-bottom: 2rem;
        }
        .back-link:hover {
            transform: translateX(-5px);
            color: var(--primary);
            box-shadow: var(--shadow-md);
            border-color: var(--primary-light);
        }

        /* --- HLAVIČKA --- */
        .header-section {
            margin-bottom: 2.5rem;
            text-align: center;
        }
        .header-section h2 {
            font-size: 2.2rem;
            font-weight: 800;
            margin: 0 0 0.5rem 0;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.025em;
        }
        .header-section p {
            color: var(--text-muted);
            font-size: 1.1rem;
            margin: 0.2rem 0;
        }

        /* --- KARTA FORMULÁŘE --- */
        form {
            background: var(--white);
            padding: 3rem;
            border-radius: 1.5rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--white);
            position: relative;
            overflow: hidden;
        }
        
        form::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem 2rem;
        }

        .full-width { grid-column: span 2; }

        /* --- INPUTY A LABEL --- */
        label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        label span { color: #ef4444; }

        input, textarea, select {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid var(--border);
            border-radius: 0.75rem;
            background: var(--bg);
            color: var(--text-main);
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 4px var(--primary-light);
            transform: translateY(-2px);
        }

        input[readonly] {
            background: #f1f5f9;
            color: var(--text-muted);
            cursor: not-allowed;
        }

        /* --- UPLOAD SEKCE --- */
        .file-upload-box {
            border: 2px dashed var(--primary);
            background: var(--primary-light);
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            border-radius: 1rem;
            transition: all 0.3s ease;
        }
        .file-upload-box:hover {
            background: #e0e7ff;
            transform: scale(1.01);
        }

        /* --- TLAČÍTKO --- */
        button[type="submit"] {
            width: 100%;
            padding: 1.2rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: 1rem;
            font-size: 1.1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        button[type="submit"]:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.5);
        }

        @keyframes slideUpFade {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
            .full-width { grid-column: span 1; }
            form { padding: 1.5rem; }
        }
    </style>
</head>
<body>
    <?php require_once '../app/views/layout/header.php'; ?>

    <div class="main-wrapper">
        <a href="index.php" class="back-link">&larr; Zpět na seznam knih</a>

        <div class="header-section">
            <h2>Upravit knihu</h2>
            <p>ID záznamu: <strong>#<?= htmlspecialchars($book['id']) ?></strong></p>
            <p>Právě upravujete: <strong><?= htmlspecialchars($book['title']) ?></strong></p>
        </div>
        
        <form action="index.php?url=book/update/<?= htmlspecialchars($book['id']) ?>" method="post" enctype="multipart/form-data">
            <div class="form-grid">
                
                <div class="full-width">
                    <label for="title">Název knihy <span>*</span></label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($book['title']) ?>" required>
                </div>

                <div>
                    <label for="author">Autor <span>*</span></label>
                    <input type="text" id="author" name="author" value="<?= htmlspecialchars($book['author']) ?>" required>
                </div>

                <div>
                    <label for="isbn">ISBN <span>*</span></label>
                    <input type="text" id="isbn" name="isbn" value="<?= htmlspecialchars($book['isbn'] ?? '') ?>">
                </div>

                <div>
                    <label for="category">Kategorie <span>*</span></label>
                    <select id="category" name="category" required>
                        <option value="">-- Vyberte kategorii --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat['id']) ?>" <?= ($book['category'] == $cat['id']) ? 'selected' : '' ?>>
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
                            <option value="<?= htmlspecialchars($sub['id']) ?>" <?= ($book['subcategory'] == $sub['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sub['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="year">Rok vydání <span>*</span></label>
                    <input type="number" id="year" name="year" value="<?= htmlspecialchars($book['year']) ?>" required>
                </div>

                <div>
                    <label for="price">Cena (Kč)</label>
                    <input type="number" id="price" name="price" step="0.5" value="<?= htmlspecialchars($book['price'] ?? '') ?>">
                </div>

                <div class="full-width">
                    <label for="link">Odkaz (URL)</label>
                    <input type="url" id="link" name="link" value="<?= htmlspecialchars($book['link'] ?? '') ?>" placeholder="https://...">
                </div>

                <div class="full-width">
                    <label for="description">Popis knihy</label>
                    <textarea id="description" name="description" rows="5"><?= htmlspecialchars($book['description'] ?? '') ?></textarea>
                </div>    

                <div class="full-width">
                    <label>Nové obrázky (nepovinné)</label>
                    <div class="file-upload-box" onclick="document.getElementById('images').click()">
                        <span id="file-title" style="font-weight: 700; display: block;">Klikněte pro změnu obrázků</span>
                        <span id="file-info" style="font-size: 0.85rem; color: var(--text-muted);">
                            Původní obrázky zůstanou, dokud nenahrajete nové.
                        </span>
                        <input type="file" id="images" name="images[]" multiple accept="image/*" style="display: none;">
                    </div>
                </div>

                <div class="full-width">
                    <button type="submit">Uložit změny do databáze</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        const fileInput = document.getElementById('images');
        const fileTitle = document.getElementById('file-title');
        const fileInfo = document.getElementById('file-info');

        fileInput.addEventListener('change', function(event) {
            const files = event.target.files;
            if (files.length === 0) {
                fileTitle.textContent = 'Klikněte pro změnu obrázků';
                fileTitle.style.color = 'var(--primary)';
                fileInfo.textContent = 'Původní obrázky zůstanou, dokud nenahrajete nové.';
            } else {
                fileTitle.textContent = files.length === 1 ? 'Soubor připraven' : 'Soubory připraveny';
                fileTitle.style.color = 'var(--success)';
                fileInfo.textContent = 'Vybráno: ' + (files.length === 1 ? files[0].name : files.length + ' souborů');
            }
        });
    </script>
</body>
</html>