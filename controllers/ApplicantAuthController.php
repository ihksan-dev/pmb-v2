<?php
// controllers/ApplicantAuthController.php

require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/Student.php';

class ApplicantAuthController {
    
    public function showLogin() {
        // Check if user is already logged in
        session_start();
        if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'student') {
            header('Location: /applicant/dashboard');
            exit();
        }
        
        // Generate CSRF token
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        include_once __DIR__ . '/../views/applicant/login.php';
    }
    
    public function login() {
        session_start();
        
        // Verify CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = "Token CSRF tidak valid.";
            header('Location: /applicant/login');
            exit();
        }
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = "Email dan kata sandi wajib diisi.";
            header('Location: /applicant/login');
            exit();
        }
        
        $database = new Database();
        $db = $database->getConnection();
        
        $student = new Student($db);
        
        if ($student->login($email, $password)) {
            // Clear CSRF token after successful login
            unset($_SESSION['csrf_token']);
            
            // Set session variables
            $_SESSION['student_id'] = $student->id;
            $_SESSION['student_email'] = $student->email;
            $_SESSION['student_name'] = $student->full_name;
            $_SESSION['user_type'] = 'student';
            
            header('Location: /applicant/dashboard');
            exit();
        } else {
            $_SESSION['error'] = "Email atau kata sandi salah.";
            header('Location: /applicant/login');
            exit();
        }
    }
    
    public function logout() {
        session_start();
        session_destroy();
        header('Location: /');
        exit();
    }
}
?>