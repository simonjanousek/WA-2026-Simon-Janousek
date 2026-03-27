<?php

// __DIR__ je cesta k aktuální složce (app/controllers)
// /../ nás posune o úroveň výš do složky app/
// a odtud jdeme do models/

require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/Database.php'; 



class BookController {

    private $book;

    public function __construct() {
        // 1. Vytvoříme připojení k databázi
        $database = new Database();
        $dbConn = $database->getConnection();

        // 2. Předáme připojení modelu Book
        $this->book = new Book($dbConn);
    }

    // Zobrazení seznamu knih (voláno přes index.php?url=book/index)
 // Zobrazení úvodní stránky (seznamu)
    public function index() {
        // 1. Vytáhnout data z modelu
        $books = $this->book->getAll(); 

        // 2. Načíst pohled (view)
        require_once __DIR__ . '/../views/books/books_list.php';
    }
    
    // Zobrazení formuláře pro přidání (voláno přes index.php?url=book/create)
    public function create() {
        require_once __DIR__ . '/../views/books/book_create.php';
    }
    // ZPRACOVÁNÍ FORMULÁŘE (voláno přes index.php?url=book/store)
    public function store() {
        // Kontrola, zda byla data odeslána metodou POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            // Posbíráme data z $_POST (názvy musí sedět na "name" v HTML)
            $data = [
                'title'       => $_POST['title'] ?? '',
                'author'      => $_POST['author'] ?? '',
                'category'    => $_POST['category'] ?? '',
                'isbn'        => $_POST['isbn'] ?? '',
                'subcategory' => $_POST['subcategory'] ?? '',
                'year'        => $_POST['year'] ?? 0,
                'price'       => $_POST['price'] ?? 0,
                'link'        => $_POST['link'] ?? '',
                'description' => $_POST['description'] ?? ''
            ];

            // Zavoláme metodu create v modelu Book.php
            if ($this->book->create($data)) {
                // Pokud se uložení povedlo, přesměrujeme uživatele na seznam knih
                header('Location: index.php?url=book/index');
                exit();
            } else {
                // Pokud nastala chyba
                echo "Chyba: Knihu se nepodařilo uložit do databáze.";
            }
        }
    }
}
