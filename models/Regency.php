<?php
// models/Regency.php

require_once 'Database.php';

class Regency {
    private $conn;
    private $table_name = "regencies";

    public $id;
    public $province_id;
    public $name;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all regencies
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get regency by ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get regencies by province ID
    public function getByProvinceId($province_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE province_id = ? ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $province_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>