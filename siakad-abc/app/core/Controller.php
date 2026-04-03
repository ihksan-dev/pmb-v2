<?php
/**
 * Base Controller Class
 * Semua controller extends dari kelas ini
 */

class Controller {
    protected $config;
    
    public function __construct() {
        $this->config = require __DIR__ . '/../config/app.php';
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Load view with data
     */
    protected function view($viewName, $data = []) {
        extract($data);
        
        $viewFile = __DIR__ . '/../views/' . str_replace('.', '/', $viewName) . '.php';
        
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            throw new Exception("View {$viewName} not found");
        }
    }
    
    /**
     * Redirect to URL
     */
    protected function redirect($url) {
        header("Location: {$url}");
        exit();
    }
    
    /**
     * Return JSON response
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
    
    /**
     * Check if user is logged in
     */
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']) && isset($_SESSION['role']);
    }
    
    /**
     * Get current logged in user
     */
    protected function currentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'role' => $_SESSION['role'],
            'nama' => $_SESSION['nama'] ?? ''
        ];
    }
    
    /**
     * Check if user has role access
     */
    protected function hasRole($allowedRoles) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        $currentRole = $_SESSION['role'];
        
        if (!is_array($allowedRoles)) {
            $allowedRoles = [$allowedRoles];
        }
        
        return in_array($currentRole, $allowedRoles);
    }
    
    /**
     * Require authentication
     */
    protected function requireAuth() {
        if (!$this->isLoggedIn()) {
            $this->redirect($this->config['base_url'] . '/auth/login');
        }
    }
    
    /**
     * Require specific role
     */
    protected function requireRole($roles) {
        $this->requireAuth();
        
        if (!$this->hasRole($roles)) {
            http_response_code(403);
            die("Access denied. You don't have permission to access this resource.");
        }
    }
    
    /**
     * Get POST data
     */
    protected function post($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }
    
    /**
     * Get GET data
     */
    protected function get($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }
    
    /**
     * Sanitize input
     */
    protected function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Generate CSRF token
     */
    protected function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF token
     */
    protected function verifyCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Flash message
     */
    protected function setFlash($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }
    
    /**
     * Get and clear flash message
     */
    protected function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}
