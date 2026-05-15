<?php

class Book {
    private $db;
    private $table = "books"; // Název tvé tabulky v DB

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    /**
     * Metoda pro vytvoření nového záznamu knihy
     */
    public function create(
        string $title,
        string $author,
        string $category,
        string $subcategory,
        int $year,
        float $price,
        string $isbn,
        string $description,
        string $link,
        array $images,
        int $userId // !!! ZMĚNA: NOVÝ PARAMETR PRO ID UŽIVATELE
    ): bool {
        // !!! ZMĚNA: Přidali jsme created_by do INSERT i VALUES
        $sql = "INSERT INTO " . $this->table . " 
                (title, author, category, subcategory, year, price, isbn, description, link, images, created_by)
                VALUES 
                (:title, :author, :category, :subcategory, :year, :price, :isbn, :description, :link, :images, :created_by)";
        
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':title' => $title,
            ':author' => $author,
            ':category' => $category,
            ':subcategory' => $subcategory ?: null,
            ':year' => $year,
            ':price' => $price,
            ':isbn' => $isbn,
            ':description' => $description,
            ':link' => $link,
            ':images' => json_encode($images),
            ':created_by' => $userId // !!! ZMĚNA: Předání ID do databáze
        ]);
    }

    // Získání všech knih
        // Získání všech knih z databáze (Nyní včetně názvu kategorie)
    public function getAll() {
        
        // 💡 ZMĚNA: Místo "SELECT *" použijeme přesnější dotaz s JOINem
        $sql = "SELECT books.*, categories.name AS category_name 
                FROM books 
                LEFT JOIN categories ON books.category = categories.id 
                ORDER BY books.id DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Získání jedné konkrétní knihy podle jejího ID
    public function getById($id) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        // Používá se fetch() místo fetchAll(), protože očekáváme maximálně jeden výsledek.
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Aktualizace existující knihy
    public function update(
        $id, $title, $author, $category, $subcategory, 
        $year, $price, $isbn, $description, $link, $images = []
    ) {
        $sql = "UPDATE " . $this->table . " 
                SET title = :title, 
                    author = :author, 
                    category = :category, 
                    subcategory = :subcategory, 
                    year = :year, 
                    price = :price, 
                    isbn = :isbn, 
                    description = :description, 
                    link = :link, 
                    images = :images
                WHERE id = :id";
                
        $stmt = $this->db->prepare($sql);

        // Parametrů je stejné množství jako u create, navíc je pouze :id
        return $stmt->execute([
            ':id' => $id,
            ':title' => $title,
            ':author' => $author,
            ':category' => $category,
            ':subcategory' => $subcategory ?: null,
            ':year' => $year,
            ':price' => $price,
            ':isbn' => $isbn,
            ':description' => $description,
            ':link' => $link,
            ':images' => json_encode($images)
        ]);
    }

    // Trvalé smazání knihy z databáze
    public function delete($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        // Vrací true při úspěchu, false při chybě
        return $stmt->execute([':id' => $id]);
    }
}