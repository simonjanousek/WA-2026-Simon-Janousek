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
    public function create($data) {
        // SQL dotaz se všemi poli z tvého formuláře
        $sql = "INSERT INTO " . $this->table . " 
                (title, author, category, isbn, subcategory, year, price, link, description) 
                VALUES 
                (:title, :author, :category, :isbn, :subcategory, :year, :price, :link, :description)";
        
        try {
            $stmt = $this->db->prepare($sql);

            // Bindování parametrů z pole $data, které přijde z Controlleru
            $stmt->bindParam(':title', $data['title']);
            $stmt->bindParam(':author', $data['author']);
            $stmt->bindParam(':category', $data['category']);
            $stmt->bindParam(':isbn', $data['isbn']);
            $stmt->bindParam(':subcategory', $data['subcategory']);
            $stmt->bindParam(':year', $data['year']);
            $stmt->bindParam(':price', $data['price']);
            $stmt->bindParam(':link', $data['link']);
            $stmt->bindParam(':description', $data['description']);

            if ($stmt->execute()) {
                return true;
            }
            return false;
            
        } catch (PDOException $e) {
            // Pro vývoj vypíšeme chybu, v ostrém provozu raději logovat
            error_log("Chyba DB: " . $e->getMessage());
            return false;
        }
    }
    public function getAll() {
    $sql = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  
}
}