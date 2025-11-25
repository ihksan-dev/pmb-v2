<?php
// models/AcademicYear.php

require_once 'Database.php';

class AcademicYear {
    private $conn;
    private $table_name = "academic_years";

    public $id;
    public $year_name;
    public $is_active;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all academic years
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY year_name DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get academic year by ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get active academic year
    public function getActive() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE is_active = 1 LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Set academic year as active (with transaction to ensure only one is active)
    public function setActive($id) {
        try {
            $this->conn->beginTransaction();
            
            // First, set all years to inactive
            $query = "UPDATE " . $this->table_name . " SET is_active = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            // Then set the selected year as active
            $query = "UPDATE " . $this->table_name . " SET is_active = 1 WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $result = $stmt->execute();
            
            if ($result) {
                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollback();
                return false;
            }
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    // Create new academic year
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET 
                  year_name = :year_name, 
                  is_active = :is_active";
        
        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->year_name = htmlspecialchars(strip_tags($this->year_name));
        
        // Set is_active to 0 by default (unless it's the first entry)
        if ($this->is_active === null) {
            $this->is_active = 0;
        }

        // Bind values
        $stmt->bindParam(":year_name", $this->year_name);
        $stmt->bindParam(":is_active", $this->is_active);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Update academic year
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET 
                  year_name = :year_name 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->year_name = htmlspecialchars(strip_tags($this->year_name));

        // Bind values
        $stmt->bindParam(":year_name", $this->year_name);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    // Delete academic year
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
}
?>