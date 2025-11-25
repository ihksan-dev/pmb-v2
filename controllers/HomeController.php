<?php
// controllers/HomeController.php

require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/AcademicYear.php';
require_once __DIR__ . '/../models/Prodi.php';

class HomeController {
    
    public function index() {
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
        
        // Get active academic year to show on home page
        $database = new Database();
        $db = $database->getConnection();
        
        $academicYear = new AcademicYear($db);
        $currentYear = $academicYear->getActive();
        
        $prodi = new Prodi($db);
        $prodis = $prodi->getAll();
        
        // Include the view
        include_once __DIR__ . '/../views/public/home.php';
    }
}
?>