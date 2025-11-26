<?php
require_once '../core/Controller.php';
require_once '../app/middleware/Auth.php';

class Mahasiswa extends Controller 
{
    public function __construct()
    {
        // Check if user is logged in and has mahasiswa role
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
    }

    public function dashboard()
    {
        $data['title'] = 'Dashboard Mahasiswa - PMB Universitas Syedza Saintika';
        
        // Load user data
        $mahasiswaModel = $this->model('MahasiswaModel');
        $data['user'] = $mahasiswaModel->getMahasiswaByUserId($_SESSION['user_id']);
        
        $this->view('templates/header', $data);
        $this->view('templates/navbar_mahasiswa', $data);
        $this->view('mahasiswa/dashboard', $data);
        $this->view('templates/footer', $data);
    }

    public function daftar()
    {
        $data['title'] = 'Form Pendaftaran - PMB Universitas Syedza Saintika';
        
        // Load required models
        $prodiModel = $this->model('ProdiModel');
        $provinsiModel = $this->model('ProvinsiModel');
        $kabupatenModel = $this->model('KabupatenModel');
        
        $data['prodi_list'] = $prodiModel->getAllProdi();
        $data['provinsi_list'] = $provinsiModel->getAllProvinsi();
        
        $this->view('templates/header', $data);
        $this->view('templates/navbar_mahasiswa', $data);
        $this->view('mahasiswa/daftar', $data);
        $this->view('templates/footer', $data);
    }

    public function saveDraft()
    {
        // AJAX endpoint to save form as draft
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        // Validate and save draft data
        $mahasiswaModel = $this->model('MahasiswaModel');
        $result = $mahasiswaModel->saveDraft($_SESSION['user_id'], $_POST);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Draft berhasil disimpan']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan draft']);
        }
    }

    public function submit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'mahasiswa/daftar');
            exit;
        }

        // Load required helpers
        require_once '../app/helpers/FileUpload.php';
        require_once '../app/helpers/RegistrationNumber.php';
        
        // Handle file uploads
        $uploadHelper = new FileUpload();
        $photo_result = $uploadHelper->uploadFile($_FILES['foto'], 'foto');
        $ijazah_result = $uploadHelper->uploadFile($_FILES['ijazah'], 'dokumen');
        
        if (!$photo_result['success'] || !$ijazah_result['success']) {
            $_SESSION['error'] = 'Gagal mengupload file';
            header('Location: ' . BASE_URL . 'mahasiswa/daftar');
            exit;
        }

        // Generate registration number
        $regNumberHelper = new RegistrationNumber();
        $registration_number = $regNumberHelper->generateNumber();

        // Save data to database
        $mahasiswaModel = $this->model('MahasiswaModel');
        $result = $mahasiswaModel->submitForm($_SESSION['user_id'], $_POST, $photo_result['filename'], $ijazah_result['filename'], $registration_number);

        if ($result) {
            $_SESSION['success'] = 'Formulir pendaftaran berhasil dikirim';
            header('Location: ' . BASE_URL . 'mahasiswa/status');
        } else {
            $_SESSION['error'] = 'Gagal mengirim formulir pendaftaran';
            header('Location: ' . BASE_URL . 'mahasiswa/daftar');
        }
    }

    public function status()
    {
        $data['title'] = 'Status Pendaftaran - PMB Universitas Syedza Saintika';
        
        $mahasiswaModel = $this->model('MahasiswaModel');
        $data['pendaftaran'] = $mahasiswaModel->getPendaftaranByUserId($_SESSION['user_id']);
        
        $this->view('templates/header', $data);
        $this->view('templates/navbar_mahasiswa', $data);
        $this->view('mahasiswa/status', $data);
        $this->view('templates/footer', $data);
    }

    public function cetak()
    {
        $data['title'] = 'Cetak Formulir - PMB Universitas Syedza Saintika';
        
        $mahasiswaModel = $this->model('MahasiswaModel');
        $data['pendaftaran'] = $mahasiswaModel->getPendaftaranByUserId($_SESSION['user_id']);
        
        // Only allow if status is verified
        if ($data['pendaftaran']['status'] !== 'verified') {
            header('Location: ' . BASE_URL . 'mahasiswa/status');
            exit;
        }
        
        $this->view('templates/header', $data);
        $this->view('templates/navbar_mahasiswa', $data);
        $this->view('mahasiswa/cetak', $data);
        $this->view('templates/footer', $data);
    }
}