<?php
// controllers/ApplicantController.php

require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Prodi.php';
require_once __DIR__ . '/../models/AcademicYear.php';
require_once __DIR__ . '/../models/Province.php';
require_once __DIR__ . '/../models/Regency.php';

class ApplicantController {
    
    private function checkStudentAuth() {
        session_start();
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
            header('Location: /applicant/login');
            exit();
        }
    }
    
    public function dashboard() {
        $this->checkStudentAuth();
        
        $database = new Database();
        $db = $database->getConnection();
        
        $student = new Student($db);
        $studentData = $student->getById($_SESSION['student_id']);
        
        if (!$studentData) {
            header('Location: /applicant/login');
            exit();
        }
        
        include_once __DIR__ . '/../views/applicant/dashboard.php';
    }
    
    public function status() {
        $this->checkStudentAuth();
        
        $database = new Database();
        $db = $database->getConnection();
        
        $student = new Student($db);
        $studentData = $student->getById($_SESSION['student_id']);
        
        if (!$studentData) {
            header('Location: /applicant/login');
            exit();
        }
        
        include_once __DIR__ . '/../views/applicant/status.php';
    }
    
    public function print() {
        $this->checkStudentAuth();
        
        $database = new Database();
        $db = $database->getConnection();
        
        $student = new Student($db);
        $studentData = $student->getById($_SESSION['student_id']);
        
        if (!$studentData) {
            header('Location: /applicant/login');
            exit();
        }
        
        include_once __DIR__ . '/../views/applicant/print.php';
    }
    
    public function account() {
        $this->checkStudentAuth();
        
        $database = new Database();
        $db = $database->getConnection();
        
        $student = new Student($db);
        $studentData = $student->getById($_SESSION['student_id']);
        
        if (!$studentData) {
            header('Location: /applicant/login');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify CSRF token
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $_SESSION['error'] = "Token CSRF tidak valid.";
                include_once __DIR__ . '/../views/applicant/account.php';
                return;
            }
            
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_new_password = $_POST['confirm_new_password'] ?? '';
            
            // Validate input
            if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
                $_SESSION['error'] = "Semua field wajib diisi.";
                include_once __DIR__ . '/../views/applicant/account.php';
                return;
            }
            
            if ($new_password !== $confirm_new_password) {
                $_SESSION['error'] = "Kata sandi baru dan konfirmasi tidak cocok.";
                include_once __DIR__ . '/../views/applicant/account.php';
                return;
            }
            
            if (strlen($new_password) < 6) {
                $_SESSION['error'] = "Kata sandi baru minimal 6 karakter.";
                include_once __DIR__ . '/../views/applicant/account.php';
                return;
            }
            
            // Verify current password
            if (!password_verify($current_password, $studentData['password'])) {
                $_SESSION['error'] = "Kata sandi saat ini salah.";
                include_once __DIR__ . '/../views/applicant/account.php';
                return;
            }
            
            // Update password
            if ($student->changePassword($_SESSION['student_id'], $new_password)) {
                // Clear CSRF token after successful update
                unset($_SESSION['csrf_token']);
                
                $_SESSION['success'] = "Kata sandi berhasil diubah.";
            } else {
                $_SESSION['error'] = "Gagal mengubah kata sandi.";
            }
        }
        
        include_once __DIR__ . '/../views/applicant/account.php';
    }
    
    public function edit() {
        $this->checkStudentAuth();
        
        $database = new Database();
        $db = $database->getConnection();
        
        $student = new Student($db);
        $studentData = $student->getById($_SESSION['student_id']);
        
        if (!$studentData) {
            header('Location: /applicant/login');
            exit();
        }
        
        // Only allow editing if status is pending
        if ($studentData['status'] !== 'pending') {
            $_SESSION['error'] = "Data hanya dapat diubah jika status masih menunggu verifikasi.";
            header('Location: /applicant/status');
            exit();
        }
        
        // Get related data
        $prodi = new Prodi($db);
        $prodis = $prodi->getAll();
        
        $academicYear = new AcademicYear($db);
        $academic_years = $academicYear->getAll();
        
        $province = new Province($db);
        $provinces = $province->getAll();
        
        $regency = new Regency($db);
        $regencies = $regency->getByProvinceId($studentData['province_id']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify CSRF token
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $_SESSION['error'] = "Token CSRF tidak valid.";
                include_once __DIR__ . '/../views/applicant/edit.php';
                return;
            }
            
            // Validate input
            $full_name = trim($_POST['full_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $gender = $_POST['gender'] ?? '';
            $birth_date = $_POST['birth_date'] ?? '';
            $address = trim($_POST['address'] ?? '');
            $province_id = (int)($_POST['province_id'] ?? 0);
            $regency_id = (int)($_POST['regency_id'] ?? 0);
            $prodi_id = (int)($_POST['prodi_id'] ?? 0);
            $academic_year_id = (int)($_POST['academic_year_id'] ?? 0);
            
            if (empty($full_name) || empty($phone) || empty($gender) || 
                empty($birth_date) || empty($address) || empty($province_id) || 
                empty($regency_id) || empty($prodi_id) || empty($academic_year_id)) {
                $_SESSION['error'] = "Semua field wajib diisi.";
                include_once __DIR__ . '/../views/applicant/edit.php';
                return;
            }
            
            // Phone validation
            if (!preg_match('/^[0-9+\-\s()]+$/', $phone)) {
                $_SESSION['error'] = "Format nomor telepon tidak valid.";
                include_once __DIR__ . '/../views/applicant/edit.php';
                return;
            }
            
            // Set student properties
            $student->full_name = $full_name;
            $student->phone = $phone;
            $student->gender = $gender;
            $student->birth_date = $birth_date;
            $student->address = $address;
            $student->province_id = $province_id;
            $student->regency_id = $regency_id;
            $student->prodi_id = $prodi_id;
            $student->academic_year_id = $academic_year_id;
            
            // Update student
            if ($student->update($_SESSION['student_id'], $studentData['status'])) {
                // Clear CSRF token after successful update
                unset($_SESSION['csrf_token']);
                
                // Update session data
                $_SESSION['student_name'] = $full_name;
                
                $_SESSION['success'] = "Data berhasil diperbarui.";
                header('Location: /applicant/status');
                exit();
            } else {
                $_SESSION['error'] = "Gagal memperbarui data.";
                include_once __DIR__ . '/../views/applicant/edit.php';
                return;
            }
        }
        
        // For GET request, show the edit form
        include_once __DIR__ . '/../views/applicant/edit.php';
    }
}
?>