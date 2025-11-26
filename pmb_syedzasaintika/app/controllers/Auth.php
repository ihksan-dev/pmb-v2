<?php
require_once '../core/Controller.php';
require_once '../app/middleware/Captcha.php';

class Auth extends Controller 
{
    private $captcha;

    public function __construct()
    {
        $this->captcha = new Captcha();
    }

    public function login()
    {
        $data['title'] = 'Login - PMB Universitas Syedza Saintika';
        $data['captcha_session'] = $this->captcha->generateCaptcha();
        
        $this->view('templates/header', $data);
        $this->view('auth/login', $data);
        $this->view('templates/footer', $data);
    }

    public function register()
    {
        $data['title'] = 'Registrasi - PMB Universitas Syedza Saintika';
        $data['captcha_session'] = $this->captcha->generateCaptcha();
        
        $this->view('templates/header', $data);
        $this->view('auth/register', $data);
        $this->view('templates/footer', $data);
    }

    public function generateCaptcha()
    {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'session_id' => $this->captcha->generateCaptcha()]);
    }

    public function captchaImage($session_id)
    {
        $this->captcha->createCaptchaImage($session_id);
    }

    public function doLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = 'Token CSRF tidak valid';
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        // Validate captcha
        $captcha_result = $this->captcha->validateCaptcha($_POST['captcha_session_id'], $_POST['captcha_code']);
        if (!$captcha_result['valid']) {
            $_SESSION['error'] = 'Kode captcha salah';
            $this->captcha->refreshCaptcha($_POST['captcha_session_id']);
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $username = trim($_POST['username']);
        $password = $_POST['password'];

        // Load user model and validate credentials
        $userModel = $this->model('UserModel');
        $user = $userModel->getUserByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            // Mark captcha as used
            $this->captcha->markCaptchaUsed($_POST['captcha_session_id']);
            
            // Set session
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect based on role
            if ($user['role'] === 'admin') {
                header('Location: ' . BASE_URL . 'admin/dashboard');
            } else {
                header('Location: ' . BASE_URL . 'mahasiswa/dashboard');
            }
            exit;
        } else {
            $_SESSION['error'] = 'Username atau password salah';
            $this->captcha->refreshCaptcha($_POST['captcha_session_id']);
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
    }

    public function doRegister()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'auth/register');
            exit;
        }

        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = 'Token CSRF tidak valid';
            header('Location: ' . BASE_URL . 'auth/register');
            exit;
        }

        // Validate captcha
        $captcha_result = $this->captcha->validateCaptcha($_POST['captcha_session_id'], $_POST['captcha_code']);
        if (!$captcha_result['valid']) {
            $_SESSION['error'] = 'Kode captcha salah';
            $this->captcha->refreshCaptcha($_POST['captcha_session_id']);
            header('Location: ' . BASE_URL . 'auth/register');
            exit;
        }

        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Validate input
        if ($password !== $confirm_password) {
            $_SESSION['error'] = 'Password dan konfirmasi password tidak cocok';
            $this->captcha->refreshCaptcha($_POST['captcha_session_id']);
            header('Location: ' . BASE_URL . 'auth/register');
            exit;
        }

        if (strlen($password) < 8) {
            $_SESSION['error'] = 'Password minimal 8 karakter';
            $this->captcha->refreshCaptcha($_POST['captcha_session_id']);
            header('Location: ' . BASE_URL . 'auth/register');
            exit;
        }

        // Load user model and register user
        $userModel = $this->model('UserModel');
        
        if ($userModel->getUserByUsername($username)) {
            $_SESSION['error'] = 'Username sudah digunakan';
            $this->captcha->refreshCaptcha($_POST['captcha_session_id']);
            header('Location: ' . BASE_URL . 'auth/register');
            exit;
        }

        if ($userModel->getUserByEmail($email)) {
            $_SESSION['error'] = 'Email sudah digunakan';
            $this->captcha->refreshCaptcha($_POST['captcha_session_id']);
            header('Location: ' . BASE_URL . 'auth/register');
            exit;
        }

        // Hash password and register user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $result = $userModel->registerUser($username, $email, $hashed_password);

        if ($result) {
            // Mark captcha as used
            $this->captcha->markCaptchaUsed($_POST['captcha_session_id']);
            
            $_SESSION['success'] = 'Registrasi berhasil, silakan login';
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        } else {
            $_SESSION['error'] = 'Gagal melakukan registrasi';
            $this->captcha->refreshCaptcha($_POST['captcha_session_id']);
            header('Location: ' . BASE_URL . 'auth/register');
            exit;
        }
    }

    public function logout()
    {
        session_start();
        session_destroy();
        header('Location: ' . BASE_URL . 'home/index');
        exit;
    }
}