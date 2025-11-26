<?php
require_once '../core/Controller.php';
require_once '../app/middleware/Auth.php';

class Admin extends Controller 
{
    public function __construct()
    {
        // Check if user is logged in and has admin role
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
    }

    public function dashboard()
    {
        $data['title'] = 'Dashboard Admin - PMB Universitas Syedza Saintika';
        
        // Load statistics
        $mahasiswaModel = $this->model('MahasiswaModel');
        $data['stats'] = $mahasiswaModel->getDashboardStats();
        
        $this->view('templates/header', $data);
        $this->view('templates/navbar_admin', $data);
        $this->view('admin/dashboard', $data);
        $this->view('templates/footer', $data);
    }

    // Pendaftar methods
    public function pendaftar()
    {
        $data['title'] = 'Data Pendaftar - PMB Universitas Syedza Saintika';
        
        $mahasiswaModel = $this->model('MahasiswaModel');
        $data['pendaftar_list'] = $mahasiswaModel->getAllPendaftar($_GET);
        
        $this->view('templates/header', $data);
        $this->view('templates/navbar_admin', $data);
        $this->view('admin/pendaftar/index', $data);
        $this->view('templates/footer', $data);
    }

    public function pendaftarDetail($id)
    {
        $data['title'] = 'Detail Pendaftar - PMB Universitas Syedza Saintika';
        
        $mahasiswaModel = $this->model('MahasiswaModel');
        $data['pendaftar'] = $mahasiswaModel->getPendaftarById($id);
        
        if (!$data['pendaftar']) {
            header('Location: ' . BASE_URL . 'admin/pendaftar');
            exit;
        }
        
        $this->view('templates/header', $data);
        $this->view('templates/navbar_admin', $data);
        $this->view('admin/pendaftar/detail', $data);
        $this->view('templates/footer', $data);
    }

    public function pendaftarVerifikasi($id)
    {
        $data['title'] = 'Verifikasi Pendaftar - PMB Universitas Syedza Saintika';
        
        $mahasiswaModel = $this->model('MahasiswaModel');
        $data['pendaftar'] = $mahasiswaModel->getPendaftarById($id);
        
        if (!$data['pendaftar']) {
            header('Location: ' . BASE_URL . 'admin/pendaftar');
            exit;
        }
        
        $this->view('templates/header', $data);
        $this->view('templates/navbar_admin', $data);
        $this->view('admin/pendaftar/verifikasi', $data);
        $this->view('templates/footer', $data);
    }

    public function doVerifikasi()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'admin/pendaftar');
            exit;
        }

        $mahasiswaModel = $this->model('MahasiswaModel');
        $result = $mahasiswaModel->verifikasiPendaftar($_POST['id'], $_POST['keterangan'] ?? '');
        
        if ($result) {
            $_SESSION['success'] = 'Pendaftar berhasil diverifikasi';
        } else {
            $_SESSION['error'] = 'Gagal memverifikasi pendaftar';
        }
        
        header('Location: ' . BASE_URL . 'admin/pendaftar');
    }

    public function doReject()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'admin/pendaftar');
            exit;
        }

        $mahasiswaModel = $this->model('MahasiswaModel');
        $result = $mahasiswaModel->rejectPendaftar($_POST['id'], $_POST['keterangan']);
        
        if ($result) {
            $_SESSION['success'] = 'Pendaftar berhasil ditolak';
        } else {
            $_SESSION['error'] = 'Gagal menolak pendaftar';
        }
        
        header('Location: ' . BASE_URL . 'admin/pendaftar');
    }

    // Prodi methods
    public function prodi()
    {
        $data['title'] = 'Data Program Studi - PMB Universitas Syedza Saintika';
        
        $prodiModel = $this->model('ProdiModel');
        $data['prodi_list'] = $prodiModel->getAllProdi();
        
        $this->view('templates/header', $data);
        $this->view('templates/navbar_admin', $data);
        $this->view('admin/prodi/index', $data);
        $this->view('templates/footer', $data);
    }

    public function prodiTambah()
    {
        $data['title'] = 'Tambah Program Studi - PMB Universitas Syedza Saintika';
        
        $this->view('templates/header', $data);
        $this->view('templates/navbar_admin', $data);
        $this->view('admin/prodi/tambah', $data);
        $this->view('templates/footer', $data);
    }

    public function prodiEdit($id)
    {
        $data['title'] = 'Edit Program Studi - PMB Universitas Syedza Saintika';
        
        $prodiModel = $this->model('ProdiModel');
        $data['prodi'] = $prodiModel->getProdiById($id);
        
        if (!$data['prodi']) {
            header('Location: ' . BASE_URL . 'admin/prodi');
            exit;
        }
        
        $this->view('templates/header', $data);
        $this->view('templates/navbar_admin', $data);
        $this->view('admin/prodi/edit', $data);
        $this->view('templates/footer', $data);
    }

    public function prodiHapus($id)
    {
        $prodiModel = $this->model('ProdiModel');
        $result = $prodiModel->deleteProdi($id);
        
        if ($result) {
            $_SESSION['success'] = 'Program studi berhasil dihapus';
        } else {
            $_SESSION['error'] = 'Gagal menghapus program studi';
        }
        
        header('Location: ' . BASE_URL . 'admin/prodi');
    }

    // Tahun Ajaran methods
    public function tahunAjaran()
    {
        $data['title'] = 'Data Tahun Ajaran - PMB Universitas Syedza Saintika';
        
        $tahunAjaranModel = $this->model('TahunAjaranModel');
        $data['tahun_ajaran_list'] = $tahunAjaranModel->getAllTahunAjaran();
        
        $this->view('templates/header', $data);
        $this->view('templates/navbar_admin', $data);
        $this->view('admin/tahun_ajaran/index', $data);
        $this->view('templates/footer', $data);
    }

    public function tahunAjaranTambah()
    {
        $data['title'] = 'Tambah Tahun Ajaran - PMB Universitas Syedza Saintika';
        
        $this->view('templates/header', $data);
        $this->view('templates/navbar_admin', $data);
        $this->view('admin/tahun_ajaran/tambah', $data);
        $this->view('templates/footer', $data);
    }

    public function tahunAjaranEdit($id)
    {
        $data['title'] = 'Edit Tahun Ajaran - PMB Universitas Syedza Saintika';
        
        $tahunAjaranModel = $this->model('TahunAjaranModel');
        $data['tahun_ajaran'] = $tahunAjaranModel->getTahunAjaranById($id);
        
        if (!$data['tahun_ajaran']) {
            header('Location: ' . BASE_URL . 'admin/tahun_ajaran');
            exit;
        }
        
        $this->view('templates/header', $data);
        $this->view('templates/navbar_admin', $data);
        $this->view('admin/tahun_ajaran/edit', $data);
        $this->view('templates/footer', $data);
    }

    public function tahunAjaranHapus($id)
    {
        $tahunAjaranModel = $this->model('TahunAjaranModel');
        $result = $tahunAjaranModel->deleteTahunAjaran($id);
        
        if ($result) {
            $_SESSION['success'] = 'Tahun ajaran berhasil dihapus';
        } else {
            $_SESSION['error'] = 'Gagal menghapus tahun ajaran';
        }
        
        header('Location: ' . BASE_URL . 'admin/tahun_ajaran');
    }

    public function tahunAjaranAktifkan($id)
    {
        $tahunAjaranModel = $this->model('TahunAjaranModel');
        $result = $tahunAjaranModel->aktifkanTahunAjaran($id);
        
        if ($result) {
            $_SESSION['success'] = 'Tahun ajaran berhasil diaktifkan';
        } else {
            $_SESSION['error'] = 'Gagal mengaktifkan tahun ajaran';
        }
        
        header('Location: ' . BASE_URL . 'admin/tahun_ajaran');
    }

    // Provinsi methods
    public function provinsi()
    {
        $data['title'] = 'Data Provinsi - PMB Universitas Syedza Saintika';
        
        $provinsiModel = $this->model('ProvinsiModel');
        $data['provinsi_list'] = $provinsiModel->getAllProvinsi();
        
        $this->view('templates/header', $data);
        $this->view('templates/navbar_admin', $data);
        $this->view('admin/provinsi/index', $data);
        $this->view('templates/footer', $data);
    }

    public function provinsiTambah()
    {
        $data['title'] = 'Tambah Provinsi - PMB Universitas Syedza Saintika';
        
        $this->view('templates/header', $data);
        $this->view('templates/navbar_admin', $data);
        $this->view('admin/provinsi/tambah', $data);
        $this->view('templates/footer', $data);
    }

    public function provinsiEdit($id)
    {
        $data['title'] = 'Edit Provinsi - PMB Universitas Syedza Saintika';
        
        $provinsiModel = $this->model('ProvinsiModel');
        $data['provinsi'] = $provinsiModel->getProvinsiById($id);
        
        if (!$data['provinsi']) {
            header('Location: ' . BASE_URL . 'admin/provinsi');
            exit;
        }
        
        $this->view('templates/header', $data);
        $this->view('templates/navbar_admin', $data);
        $this->view('admin/provinsi/edit', $data);
        $this->view('templates/footer', $data);
    }

    public function provinsiHapus($id)
    {
        $provinsiModel = $this->model('ProvinsiModel');
        $result = $provinsiModel->deleteProvinsi($id);
        
        if ($result) {
            $_SESSION['success'] = 'Provinsi berhasil dihapus';
        } else {
            $_SESSION['error'] = 'Gagal menghapus provinsi';
        }
        
        header('Location: ' . BASE_URL . 'admin/provinsi');
    }

    // Kabupaten methods
    public function kabupaten()
    {
        $data['title'] = 'Data Kabupaten - PMB Universitas Syedza Saintika';
        
        $kabupatenModel = $this->model('KabupatenModel');
        $provinsiModel = $this->model('ProvinsiModel');
        
        $data['kabupaten_list'] = $kabupatenModel->getAllKabupaten();
        $data['provinsi_list'] = $provinsiModel->getAllProvinsi();
        
        $this->view('templates/header', $data);
        $this->view('templates/navbar_admin', $data);
        $this->view('admin/kabupaten/index', $data);
        $this->view('templates/footer', $data);
    }

    public function kabupatenTambah()
    {
        $data['title'] = 'Tambah Kabupaten - PMB Universitas Syedza Saintika';
        
        $provinsiModel = $this->model('ProvinsiModel');
        $data['provinsi_list'] = $provinsiModel->getAllProvinsi();
        
        $this->view('templates/header', $data);
        $this->view('templates/navbar_admin', $data);
        $this->view('admin/kabupaten/tambah', $data);
        $this->view('templates/footer', $data);
    }

    public function kabupatenEdit($id)
    {
        $data['title'] = 'Edit Kabupaten - PMB Universitas Syedza Saintika';
        
        $kabupatenModel = $this->model('KabupatenModel');
        $provinsiModel = $this->model('ProvinsiModel');
        
        $data['kabupaten'] = $kabupatenModel->getKabupatenById($id);
        $data['provinsi_list'] = $provinsiModel->getAllProvinsi();
        
        if (!$data['kabupaten']) {
            header('Location: ' . BASE_URL . 'admin/kabupaten');
            exit;
        }
        
        $this->view('templates/header', $data);
        $this->view('templates/navbar_admin', $data);
        $this->view('admin/kabupaten/edit', $data);
        $this->view('templates/footer', $data);
    }

    public function kabupatenHapus($id)
    {
        $kabupatenModel = $this->model('KabupatenModel');
        $result = $kabupatenModel->deleteKabupaten($id);
        
        if ($result) {
            $_SESSION['success'] = 'Kabupaten berhasil dihapus';
        } else {
            $_SESSION['error'] = 'Gagal menghapus kabupaten';
        }
        
        header('Location: ' . BASE_URL . 'admin/kabupaten');
    }

    public function getKabupatenByProvinsi()
    {
        header('Content-Type: application/json');
        
        if (!isset($_GET['provinsi_id'])) {
            echo json_encode(['success' => false, 'data' => []]);
            exit;
        }
        
        $kabupatenModel = $this->model('KabupatenModel');
        $data = $kabupatenModel->getKabupatenByProvinsi($_GET['provinsi_id']);
        
        echo json_encode(['success' => true, 'data' => $data]);
    }

    // Grafik methods
    public function grafik()
    {
        $data['title'] = 'Grafik & Statistik - PMB Universitas Syedza Saintika';
        
        $mahasiswaModel = $this->model('MahasiswaModel');
        $data['grafik_data'] = $mahasiswaModel->getGrafikData();
        
        $this->view('templates/header', $data);
        $this->view('templates/navbar_admin', $data);
        $this->view('admin/grafik/index', $data);
        $this->view('templates/footer', $data);
    }

    // Export methods
    public function export()
    {
        $format = $_GET['format'] ?? 'excel';
        $filter = $_GET;
        
        $mahasiswaModel = $this->model('MahasiswaModel');
        
        if ($format === 'excel') {
            $mahasiswaModel->exportToExcel($filter);
        } elseif ($format === 'pdf') {
            $mahasiswaModel->exportToPDF($filter);
        }
    }

    // Pengaturan methods
    public function profil()
    {
        $data['title'] = 'Profil Admin - PMB Universitas Syedza Saintika';
        
        $userModel = $this->model('UserModel');
        $data['admin'] = $userModel->getUserById($_SESSION['user_id']);
        
        $this->view('templates/header', $data);
        $this->view('templates/navbar_admin', $data);
        $this->view('admin/pengaturan/profil', $data);
        $this->view('templates/footer', $data);
    }

    public function password()
    {
        $data['title'] = 'Ganti Password - PMB Universitas Syedza Saintika';
        
        $this->view('templates/header', $data);
        $this->view('templates/navbar_admin', $data);
        $this->view('admin/pengaturan/password', $data);
        $this->view('templates/footer', $data);
    }
}