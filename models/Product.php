<?php

class Product {
    private $db;

    public function __construct() {
        require_once __DIR__ . '/../core/Database.php';
        $this->db = new Database();
    }

    // Dipakai Annas untuk nampilin katalog di Home
    public function getAllProducts() {
        $stmt = $this->db->prepare("SELECT * FROM products ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // SANGAT KRITIKAL: Ini yang ditunggu Randi untuk menghitung harga di Cart!
    public function getProductById($id) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
