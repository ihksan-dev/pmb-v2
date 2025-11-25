<?php
// controllers/RegistrationController.php

require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Prodi.php';
require_once __DIR__ . '/../models/AcademicYear.php';
require_once __DIR__ . '/../models/Province.php';
require_once __DIR__ . '/../models/Regency.php';

class RegistrationController {
    
    public function showForm() {
        // Check if user is already logged in
        session_start();
        if (isset($_SESSION['user_type'])) {
            if ($_SESSION['user_type'] === 'student') {
                header('Location: /applicant/dashboard');
                exit();
            } elseif ($_SESSION['user_type'] === 'admin') {
                header('Location: /admin/dashboard');
                exit();
            }
        }
        
        // Generate CSRF token
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        $database = new Database();
        $db = $database->getConnection();
        
        $prodi = new Prodi($db);
        $prodis = $prodi->getAll();
        
        $academicYear = new AcademicYear($db);
        $academic_years = $academicYear->getAll();
        
        $province = new Province($db);
        $provinces = $province->getAll();
        
        // If province_id is provided in the session, get related regencies
        $regencies = [];
        if (!empty($_POST['province_id'])) {
            $regency = new Regency($db);
            $regencies = $regency->getByProvinceId($_POST['province_id']);
        }
        
        include_once __DIR__ . '/../views/public/register.php';
    }
    
    public function process() {
        session_start();
        
        // Verify CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = "Token CSRF tidak valid.";
            header('Location: /register');
            exit();
        }
        
        // Validate input
        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $gender = $_POST['gender'] ?? '';
        $birth_date = $_POST['birth_date'] ?? '';
        $address = trim($_POST['address'] ?? '');
        $province_id = (int)($_POST['province_id'] ?? 0);
        $regency_id = (int)($_POST['regency_id'] ?? 0);
        $prodi_id = (int)($_POST['prodi_id'] ?? 0);
        $academic_year_id = (int)($_POST['academic_year_id'] ?? 0);
        $password = $_POST['password'] ?? '';
        
        // Basic validation
        if (empty($full_name) || empty($email) || empty($phone) || empty($gender) || 
            empty($birth_date) || empty($address) || empty($province_id) || 
            empty($regency_id) || empty($prodi_id) || empty($academic_year_id) || empty($password)) {
            $_SESSION['error'] = "Semua field wajib diisi.";
            $this->showForm();
            return;
        }
        
        // Email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Format email tidak valid.";
            $this->showForm();
            return;
        }
        
        // Phone validation
        if (!preg_match('/^[0-9+\-\s()]+$/', $phone)) {
            $_SESSION['error'] = "Format nomor telepon tidak valid.";
            $this->showForm();
            return;
        }
        
        // Password validation
        if (strlen($password) < 6) {
            $_SESSION['error'] = "Kata sandi minimal 6 karakter.";
            $this->showForm();
            return;
        }
        
        $database = new Database();
        $db = $database->getConnection();
        
        // Check if email already exists
        $student = new Student($db);
        $query = "SELECT id FROM students WHERE email = ? AND is_deleted = 0";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = "Email sudah terdaftar.";
            $this->showForm();
            return;
        }
        
        // Set student properties
        $student->full_name = $full_name;
        $student->email = $email;
        $student->password = $password;
        $student->phone = $phone;
        $student->gender = $gender;
        $student->birth_date = $birth_date;
        $student->address = $address;
        $student->province_id = $province_id;
        $student->regency_id = $regency_id;
        $student->prodi_id = $prodi_id;
        $student->academic_year_id = $academic_year_id;
        
        // Register student
        $result = $student->register();
        
        if ($result) {
            // Clear CSRF token after successful registration
            unset($_SESSION['csrf_token']);
            
            // Set success message
            $_SESSION['success'] = "Pendaftaran berhasil. Silakan login untuk melanjutkan.";
            
            // Start session for the newly registered student
            $_SESSION['student_id'] = $result;
            $_SESSION['student_email'] = $email;
            $_SESSION['student_name'] = $full_name;
            $_SESSION['user_type'] = 'student';
            
            // Redirect to applicant dashboard
            header('Location: /applicant/dashboard');
            exit();
        } else {
            $_SESSION['error'] = "Gagal melakukan pendaftaran. Silakan coba lagi.";
            $this->showForm();
            return;
        }
    }
}
?>