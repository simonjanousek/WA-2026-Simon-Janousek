<?php

class Subcategory {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Získá úplně všechny subkategorie (např. pro admin seznam)
     */
    public function getAllSubcategories() {
        $stmt = $this->db->prepare("SELECT * FROM subcategories ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Získá subkategorie patřící pod konkrétní hlavní kategorii
     * @param int $categoryId ID hlavní kategorie
     */
    public function getByCategoryId($categoryId) {
        $stmt = $this->db->prepare("SELECT * FROM subcategories WHERE category_id = :cat_id ORDER BY name ASC");
        $stmt->bindValue(':cat_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}