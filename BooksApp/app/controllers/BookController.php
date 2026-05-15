<?php

require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/Database.php'; 

class BookController {

    private $book;

    public function __construct() {
        // Spuštění session pro notifikace a autentizaci
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $database = new Database();
        $dbConn = $database->getConnection();
        $this->book = new Book($dbConn);
    }

    /**
     * POMOCNÁ METODA: Kontrola přihlášení
     */
   private function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        // Nastartuj session, pokud náhodou neběží
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        $this->addErrorMessage('Pro tuto akci se musíte nejdříve přihlásit.');
        
        // ZÁCHRANNÁ BRZDA: Vynutíme zápis session na disk/do paměti před redirectem
        session_write_close(); 
        
        header('Location: index.php?url=auth/login');
        exit();
    }
}

    // 1. Zobrazení seznamu knih
    public function index() {
        $books = $this->book->getAll(); 
        require_once __DIR__ . '/../views/books/books_list.php';
    }
    
// 2. FORMULÁŘ pro přidání
   public function create() {
    // 1. Kontrola přihlášení
    $this->checkAuth();

    // 2. Načtení potřebných modelů (všechny na jednom místě)
    require_once __DIR__ . '/../models/Database.php';
    require_once __DIR__ . '/../models/Category.php';
    require_once __DIR__ . '/../models/Subcategory.php';

    $database = new Database();
    $db = $database->getConnection();

    // 3. Příprava dat pro formulář
    // Kategorie
    $categoryModel = new Category($db);
    $categories = $categoryModel->getAllCategories();

    // Subkategorie
    $subcategoryModel = new Subcategory($db);
    $subcategories = $subcategoryModel->getAllSubcategories();

    // 4. Načtení šablony (View) - TENTO ŘÁDEK MUSÍ BÝT POSLEDNÍ
    // Šablona teď uvidí jak $categories, tak $subcategories
    require_once __DIR__ . '/../views/books/book_create.php';
}

    // 3. ZPRACOVÁNÍ přidání
    public function store() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];

            // Očištění textových dat
            $title       = htmlspecialchars($_POST['title'] ?? '');
            $author      = htmlspecialchars($_POST['author'] ?? '');
            $isbn        = htmlspecialchars($_POST['isbn'] ?? '');
            $category    = htmlspecialchars($_POST['category'] ?? '');
            $subcategory = htmlspecialchars($_POST['subcategory'] ?? '');
            $year        = (int)($_POST['year'] ?? 0);
            $price       = (float)($_POST['price'] ?? 0);
            $link        = htmlspecialchars($_POST['link'] ?? '');
            $description = htmlspecialchars($_POST['description'] ?? '');

            // Zpracování obrázků
            $uploadedImages = $this->processImageUploads();

            // Volání metody create v modelu (předáváme userId)
            $isSaved = $this->book->create(
                $title, $author, $category, $subcategory, 
                $year, $price, $isbn, $description, $link, $uploadedImages,
                $userId
            );

            if ($isSaved) {
                $this->addSuccessMessage('Kniha byla úspěšně uložena.');
                header('Location: index.php?url=book/index');
                exit;
            } else {
                $this->addErrorMessage('Nepodařilo se uložit knihu do databáze.');
            }
        } else {
            $this->addNoticeMessage('Pro přidání knihy je nutné odeslat formulář.');
        }
    }

    // 4. Zobrazení formuláře pro úpravu (s kontrolou vlastnictví)
    public function edit($id = null) {
        $this->checkAuth();

        if (!$id) {
            $this->addErrorMessage('Nebylo zadáno ID knihy k úpravě.');
            header('Location: index.php');
            exit;
        }

        $book = $this->book->getById($id);

        if (!$book) {
            $this->addErrorMessage('Požadovaná kniha nebyla nalezena.');
            header('Location: index.php');
            exit;
        }

        // 🛡️ Kontrola vlastnictví
                // 💡 ZMĚNA: Zjistíme, zda je přihlášený uživatel admin
        $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

        // 🛡️ ZMĚNA: Vyhodíme uživatele POKUD NENÍ autor A ZÁROVEŇ NENÍ admin
        if ($book['created_by'] !== $_SESSION['user_id'] && !$isAdmin) {
            $this->addErrorMessage('Nemáte oprávnění upravovat tuto knihu.');
            header('Location: index.php');
            exit;
        }

        require_once __DIR__ . '/../views/books/book_edit.php';
        //z
        require_once '../app/models/Database.php';
        require_once '../app/models/Category.php';

    }

    // 5. Zpracování úpravy (s kontrolou vlastnictví)
    public function update($id = null) {
        $this->checkAuth();

        if (!$id) {
            $this->addErrorMessage('Nebylo zadáno ID knihy k aktualizaci.');
            header('Location: index.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Nejdříve zjistíme, zda kniha patří uživateli
            $book = $this->book->getById($id);

                    // 💡 ZMĚNA: Zjistíme, zda je přihlášený uživatel admin
        $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

        // 🛡️ ZMĚNA: Vyhodíme uživatele POKUD NENÍ autor A ZÁROVEŇ NENÍ admin
        if ($book['created_by'] !== $_SESSION['user_id'] && !$isAdmin) {
            $this->addErrorMessage('Nemáte oprávnění upravovat tuto knihu.');
            header('Location: index.php');
            exit;
        }

            // Očištění dat
            $title       = htmlspecialchars($_POST['title'] ?? '');
            $author      = htmlspecialchars($_POST['author'] ?? '');
            $isbn        = htmlspecialchars($_POST['isbn'] ?? '');
            $category    = htmlspecialchars($_POST['category'] ?? '');
            $subcategory = htmlspecialchars($_POST['subcategory'] ?? '');
            $year        = (int)($_POST['year'] ?? 0);
            $price       = (float)($_POST['price'] ?? 0);
            $link        = htmlspecialchars($_POST['link'] ?? '');
            $description = htmlspecialchars($_POST['description'] ?? '');

            $uploadedImages = $this->processImageUploads();

            // Pokud nebyly nahrány nové obrázky, zachováme původní (volitelné, záleží na tvé implementaci update)
            $isUpdated = $this->book->update(
                $id, $title, $author, $category, $subcategory, 
                $year, $price, $isbn, $description, $link, $uploadedImages
            );

            if ($isUpdated) {
                $this->addSuccessMessage('Kniha byla úspěšně upravena.');
                header('Location: index.php');
                exit;
            } else {
                $this->addErrorMessage('Nastala chyba. Změny se nepodařilo uložit.');
            }
        }
    }

    // 6. Smazání knihy (s kontrolou vlastnictví)
    public function delete($id = null) {
        $this->checkAuth();

        if (!$id) {
            $this->addErrorMessage('Nebylo zadáno ID knihy ke smazání.');
            header('Location: index.php');
            exit;
        }

        $book = $this->book->getById($id);

        if (!$book) {
            $this->addErrorMessage('Kniha nebyla nalezena.');
            header('Location: index.php');
            exit;
        }

        // 🛡️ Kontrola vlastnictví
                // 💡 ZMĚNA: Zjistíme, zda je přihlášený uživatel admin
        $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

        // 🛡️ ZMĚNA: Vyhodíme uživatele POKUD NENÍ autor A ZÁROVEŇ NENÍ admin
        if ($book['created_by'] !== $_SESSION['user_id'] && !$isAdmin) {
            $this->addErrorMessage('Nemáte oprávnění upravovat tuto knihu.');
            header('Location: index.php');
            exit;
        }

        if ($this->book->delete($id)) {
            $this->addSuccessMessage('Kniha byla trvale smazána.');
        } else {
            $this->addErrorMessage('Nastala chyba. Knihu se nepodařilo smazat.');
        }

        header('Location: index.php');
        exit;
    }

    // --- Pomocné metody pro upload a notifikace ---

    protected function processImageUploads() {
        $uploadedFiles = [];
        $uploadDir = __DIR__ . '/../../public/uploads/'; 
    
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            $fileCount = count($_FILES['images']['name']);
            for ($i = 0; $i < $fileCount; $i++) {
                if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES['images']['tmp_name'][$i];
                    $originalName = basename($_FILES['images']['name'][$i]);
                    $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                    if (!in_array($fileExtension, $allowedExtensions)) continue; 

                    $newName = 'book_' . uniqid() . '_' . substr(md5(mt_rand()), 0, 4) . '.' . $fileExtension;
                    if (move_uploaded_file($tmpName, $uploadDir . $newName)) {
                        $uploadedFiles[] = $newName; 
                    }
                }
            }
        }
        return $uploadedFiles;
    }

    protected function addSuccessMessage($message) { $_SESSION['messages']['success'][] = $message; }
    protected function addNoticeMessage($message)  { $_SESSION['messages']['notice'][] = $message; }
    protected function addErrorMessage($message)   { $_SESSION['messages']['error'][] = $message; }
}