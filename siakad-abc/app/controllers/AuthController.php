<?php
/**
 * Auth Controller
 * Menangani autentikasi dan authorization
 */

class AuthController extends Controller {
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
    }
    
    /**
     * Show login page
     */
    public function login() {
        // If already logged in, redirect to dashboard
        if ($this->isLoggedIn()) {
            $this->redirect($this->config['base_url'] . '/dashboard');
        }
        
        $flash = $this->getFlash();
        $this->view('auth/login', [
            'title' => 'Login - SIAKAD Universitas ABC',
            'flash' => $flash
        ]);
    }
    
    /**
     * Process login
     */
    public function authenticate() {
        // Check rate limiting
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        if (!checkRateLimit('login_' . $ipAddress, 5, 300)) {
            $this->setFlash('error', 'Terlalu banyak percobaan login. Silakan coba lagi nanti.');
            $this->redirect($this->config['base_url'] . '/auth/login');
        }
        
        // Verify CSRF token
        if (!$this->verifyCsrfToken($this->post('csrf_token'))) {
            $this->setFlash('error', 'Token keamanan tidak valid. Silakan coba lagi.');
            $this->redirect($this->config['base_url'] . '/auth/login');
        }
        
        $username = $this->sanitize($this->post('username'));
        $password = $this->post('password');
        
        if (empty($username) || empty($password)) {
            $this->setFlash('error', 'Username dan password harus diisi');
            $this->redirect($this->config['base_url'] . '/auth/login');
        }
        
        $result = $this->userModel->authenticate($username, $password);
        
        if ($result['success']) {
            // Set session
            $_SESSION['user_id'] = $result['user']['username'];
            $_SESSION['role'] = $result['user']['role'];
            $_SESSION['nama'] = $result['user']['nama'];
            $_SESSION['modul_kode'] = $result['user']['modul_kode'];
            $_SESSION['logged_in'] = true;
            
            // Log audit
            logAudit(
                $username,
                'LOGIN',
                'auth',
                'users',
                0,
                null,
                ['ip_address' => $ipAddress]
            );
            
            $this->setFlash('success', 'Selamat datang, ' . $result['user']['nama'] . '!');
            $this->redirect($this->config['base_url'] . '/dashboard');
        } else {
            $this->setFlash('error', $result['message']);
            $this->redirect($this->config['base_url'] . '/auth/login');
        }
    }
    
    /**
     * Logout
     */
    public function logout() {
        if ($this->isLoggedIn()) {
            // Log audit before destroying session
            logAudit(
                $_SESSION['user_id'],
                'LOGOUT',
                'auth',
                'users',
                0,
                null,
                null
            );
        }
        
        // Destroy session
        session_unset();
        session_destroy();
        
        // Start new session for flash message
        session_start();
        $this->setFlash('success', 'Anda telah logout');
        $this->redirect($this->config['base_url'] . '/auth/login');
    }
}
