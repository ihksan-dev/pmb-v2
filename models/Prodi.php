<?php
// models/Prodi.php

require_once 'Database.php';

class Prodi {
    private $conn;
    private $table_name = "prodi";

    public $id;
    public $nama_prodi;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all program studi
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY nama_prodi ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get prodi by ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>