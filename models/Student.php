<?php
// models/Student.php

require_once 'Database.php';

class Student {
    private $conn;
    private $table_name = "students";

    public $id;
    public $full_name;
    public $email;
    public $password;
    public $phone;
    public $gender;
    public $birth_date;
    public $address;
    public $province_id;
    public $regency_id;
    public $prodi_id;
    public $academic_year_id;
    public $status;
    public $is_deleted;
    public $created_at;
    public $updated_at;
    public $last_login_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Register new student
    public function register() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET 
                  full_name = :full_name, 
                  email = :email, 
                  password = :password, 
                  phone = :phone, 
                  gender = :gender, 
                  birth_date = :birth_date, 
                  address = :address, 
                  province_id = :province_id, 
                  regency_id = :regency_id, 
                  prodi_id = :prodi_id, 
                  academic_year_id = :academic_year_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->gender = htmlspecialchars(strip_tags($this->gender));
        $this->address = htmlspecialchars(strip_tags($this->address));

        // Bind values
        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":gender", $this->gender);
        $stmt->bindParam(":birth_date", $this->birth_date);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":province_id", $this->province_id);
        $stmt->bindParam(":regency_id", $this->regency_id);
        $stmt->bindParam(":prodi_id", $this->prodi_id);
        $stmt->bindParam(":academic_year_id", $this->academic_year_id);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Login student
    public function login($email, $password) {
        $query = "SELECT id, full_name, email, password, status FROM " . $this->table_name . " 
                  WHERE email = ? AND is_deleted = 0 LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($password, $row['password'])) {
            $this->id = $row['id'];
            $this->full_name = $row['full_name'];
            $this->email = $row['email'];
            $this->status = $row['status'];
            
            // Update last login
            $this->updateLastLogin($this->id);
            
            return true;
        }
        return false;
    }

    // Update last login
    private function updateLastLogin($id) {
        $query = "UPDATE " . $this->table_name . " SET last_login_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    // Get student by ID
    public function getById($id) {
        $query = "SELECT s.*, p.nama_prodi, ay.year_name, prov.name as province_name, reg.name as regency_name
                  FROM " . $this->table_name . " s
                  LEFT JOIN prodi p ON s.prodi_id = p.id
                  LEFT JOIN academic_years ay ON s.academic_year_id = ay.id
                  LEFT JOIN provinces prov ON s.province_id = prov.id
                  LEFT JOIN regencies reg ON s.regency_id = reg.id
                  WHERE s.id = ? AND s.is_deleted = 0 LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get all students (with filters)
    public function getAll($search = '', $status = '', $limit = 10, $offset = 0) {
        $query = "SELECT s.*, p.nama_prodi, ay.year_name, prov.name as province_name, reg.name as regency_name
                  FROM " . $this->table_name . " s
                  LEFT JOIN prodi p ON s.prodi_id = p.id
                  LEFT JOIN academic_years ay ON s.academic_year_id = ay.id
                  LEFT JOIN provinces prov ON s.province_id = prov.id
                  LEFT JOIN regencies reg ON s.regency_id = reg.id
                  WHERE s.is_deleted = 0";
        
        $params = [];
        
        if (!empty($search)) {
            $query .= " AND (s.full_name LIKE ? OR s.email LIKE ? OR s.phone LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($status)) {
            $query .= " AND s.status = ?";
            $params[] = $status;
        }
        
        $query .= " ORDER BY s.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get count of all students (with filters)
    public function getCount($search = '', $status = '') {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " s WHERE s.is_deleted = 0";
        
        $params = [];
        
        if (!empty($search)) {
            $query .= " AND (s.full_name LIKE ? OR s.email LIKE ? OR s.phone LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($status)) {
            $query .= " AND s.status = ?";
            $params[] = $status;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Get pending students
    public function getPending($academic_year_id) {
        $query = "SELECT s.*, p.nama_prodi, ay.year_name, prov.name as province_name, reg.name as regency_name
                  FROM " . $this->table_name . " s
                  LEFT JOIN prodi p ON s.prodi_id = p.id
                  LEFT JOIN academic_years ay ON s.academic_year_id = ay.id
                  LEFT JOIN provinces prov ON s.province_id = prov.id
                  LEFT JOIN regencies reg ON s.regency_id = reg.id
                  WHERE s.status = 'pending' AND s.academic_year_id = ? AND s.is_deleted = 0
                  ORDER BY s.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $academic_year_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get active students (for current academic year)
    public function getActive($academic_year_id) {
        $query = "SELECT s.*, p.nama_prodi, ay.year_name, prov.name as province_name, reg.name as regency_name
                  FROM " . $this->table_name . " s
                  LEFT JOIN prodi p ON s.prodi_id = p.id
                  LEFT JOIN academic_years ay ON s.academic_year_id = ay.id
                  LEFT JOIN provinces prov ON s.province_id = prov.id
                  LEFT JOIN regencies reg ON s.regency_id = reg.id
                  WHERE s.academic_year_id = ? AND s.is_deleted = 0
                  ORDER BY s.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $academic_year_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update student status
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    // Update student data (only if status is pending)
    public function update($id, $status) {
        if ($status !== 'pending') {
            return false; // Only allow updates if status is still pending
        }
        
        $query = "UPDATE " . $this->table_name . " 
                  SET 
                  full_name = :full_name, 
                  phone = :phone, 
                  gender = :gender, 
                  birth_date = :birth_date, 
                  address = :address, 
                  province_id = :province_id, 
                  regency_id = :regency_id, 
                  prodi_id = :prodi_id, 
                  academic_year_id = :academic_year_id,
                  updated_at = NOW()
                  WHERE id = :id AND status = 'pending'";
        
        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->gender = htmlspecialchars(strip_tags($this->gender));
        $this->address = htmlspecialchars(strip_tags($this->address));

        // Bind values
        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":gender", $this->gender);
        $stmt->bindParam(":birth_date", $this->birth_date);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":province_id", $this->province_id);
        $stmt->bindParam(":regency_id", $this->regency_id);
        $stmt->bindParam(":prodi_id", $this->prodi_id);
        $stmt->bindParam(":academic_year_id", $this->academic_year_id);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }

    // Change student password
    public function changePassword($id, $newPassword) {
        $query = "UPDATE " . $this->table_name . " SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt->bindParam(':password', $newPassword);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    // Soft delete student
    public function delete($id) {
        $query = "UPDATE " . $this->table_name . " SET is_deleted = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
}
?>