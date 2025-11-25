<?php
// controllers/AuthController.php

require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/Admin.php';

class AuthController {
    
    public function showLogin() {
        // Check if user is already logged in
        session_start();
        if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') {
            header('Location: /admin/dashboard');
            exit();
        }
        
        // Generate CSRF token
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        include_once __DIR__ . '/../views/admin/login.php';
    }
    
    public function login() {
        session_start();
        
        // Verify CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = "Token CSRF tidak valid.";
            header('Location: /admin/login');
            exit();
        }
        
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $_SESSION['error'] = "Username dan kata sandi wajib diisi.";
            header('Location: /admin/login');
            exit();
        }
        
        $database = new Database();
        $db = $database->getConnection();
        
        $admin = new Admin($db);
        
        if ($admin->login($username, $password)) {
            // Clear CSRF token after successful login
            unset($_SESSION['csrf_token']);
            
            // Set session variables
            $_SESSION['admin_id'] = $admin->id;
            $_SESSION['admin_username'] = $admin->username;
            $_SESSION['user_type'] = 'admin';
            
            header('Location: /admin/dashboard');
            exit();
        } else {
            $_SESSION['error'] = "Username atau kata sandi salah.";
            header('Location: /admin/login');
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