<?php
class Auth extends Controller 
{
    public function __construct()
    {
        // Load necessary models
        $this->userModel = $this->model('UserModel');
    }

    public function login()
    {
        // Handle login form submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $data = [
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'captcha' => trim($_POST['captcha'])
            ];

            // Validate captcha
            if (!$this->validateCaptcha($data['captcha'])) {
                $data['captcha_error'] = 'Captcha tidak valid';
                $this->view('auth/login', $data);
                return;
            }

            // Login user
            $loggedInUser = $this->userModel->login($data['username'], $data['password']);

            if ($loggedInUser) {
                // Create session
                $this->createUserSession($loggedInUser);
                
                // Update last login
                $this->userModel->updateLastLogin($loggedInUser['id_user']);
                
                // Reset login attempts
                $this->userModel->resetLoginAttempts($data['username']);
                
                // Redirect based on role
                if ($loggedInUser['role'] == 'admin') {
                    header('location: ' . BASE_URL . '/admin/dashboard');
                } else {
                    header('location: ' . BASE_URL . '/mahasiswa/dashboard');
                }
            } else {
                // Increment login attempts
                $this->userModel->incrementLoginAttempts($data['username']);
                
                $data['login_error'] = 'Username atau password salah';
                $this->view('auth/login', $data);
            }
        } else {
            // Load view
            $this->view('auth/login');
        }
    }

    public function register()
    {
        // Handle registration form submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $data = [
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'captcha' => trim($_POST['captcha'])
            ];

            // Validate captcha
            if (!$this->validateCaptcha($data['captcha'])) {
                $data['captcha_error'] = 'Captcha tidak valid';
                $this->view('auth/register', $data);
                return;
            }

            // Validate password match
            if ($data['password'] != $data['confirm_password']) {
                $data['password_error'] = 'Password tidak cocok';
                $this->view('auth/register', $data);
                return;
            }

            // Check if username already exists
            if ($this->userModel->findUserByUsername($data['username'])) {
                $data['username_error'] = 'Username sudah digunakan';
                $this->view('auth/register', $data);
                return;
            }

            // Check if email already exists
            if ($this->userModel->findUserByEmail($data['email'])) {
                $data['email_error'] = 'Email sudah digunakan';
                $this->view('auth/register', $data);
                return;
            }

            // Hash password
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

            // Register user
            if ($this->userModel->register($data)) {
                // Auto login after registration
                $loggedInUser = $this->userModel->login($data['username'], $_POST['password']);
                
                if ($loggedInUser) {
                    // Create session
                    $this->createUserSession($loggedInUser);
                    
                    // Create student record with draft status
                    $mahasiswaModel = $this->model('MahasiswaModel');
                    $mahasiswaModel->createDraftRecord($loggedInUser['id_user']);
                    
                    // Redirect to dashboard
                    header('location: ' . BASE_URL . '/mahasiswa/dashboard');
                }
            } else {
                $data['register_error'] = 'Gagal mendaftar, silakan coba lagi';
                $this->view('auth/register', $data);
            }
        } else {
            // Load view
            $this->view('auth/register');
        }
    }

    public function logout()
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_role']);
        
        session_destroy();
        
        header('location: ' . BASE_URL . '/auth/login');
    }

    private function validateCaptcha($userCaptcha)
    {
        // This would typically validate against stored captcha in database
        // For now, we'll implement a basic validation
        if (!isset($_SESSION['captcha']) || $userCaptcha !== $_SESSION['captcha']) {
            return false;
        }
        return true;
    }

    private function createUserSession($user)
    {
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['user_name'] = $user['username'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
    }
}