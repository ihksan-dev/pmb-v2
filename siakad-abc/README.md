# SIAKAD Universitas ABC

Sistem Informasi Akademik (SIAKAD) untuk Universitas ABC dibangun menggunakan PHP Native dengan arsitektur MVC.

## 📋 Fitur Utama

### 1. **Manajemen Akses Berbasis Modul**
- Pusat kebijakan akses terpusat di tabel `akses_modul`
- Role-based access control (RBAC) untuk Admin, Keuangan, Administrasi, Dosen, dan Mahasiswa
- Audit trail lengkap untuk semua operasi kritis

### 2. **Autentikasi & Authorization**
- Password hashing dengan bcrypt (cost factor 12)
- CSRF protection untuk semua form
- Rate limiting untuk mencegah brute force
- Session management yang aman

### 3. **Modul yang Tersedia**
- **User Management**: Manajemen akun staff (Admin only)
- **Master Prodi**: Data program studi
- **Master Dosen**: Data dosen
- **Master Mahasiswa**: Data mahasiswa
- **Kurikulum & Mata Kuliah**: Pengelolaan kurikulum dan MK
- **Semester & Kelas**: Konfigurasi semester aktif
- **Penjadwalan**: Jadwal kuliah dan penugasan dosen
- **Keuangan**: Verifikasi pembayaran dan kartu ujian
- **KRS Workflow**: Pengisian dan validasi KRS
- **Penilaian**: Input nilai dan bobot penilaian
- **Absensi**: Absensi perkuliahan (16 pertemuan)
- **Skripsi**: Manajemen skripsi/tugas akhir

## 🏗️ Struktur Direktori

```
siakad-abc/
├── app/
│   ├── config/          # Konfigurasi aplikasi
│   │   ├── database.php
│   │   └── app.php
│   ├── controllers/     # Controller (C dalam MVC)
│   │   ├── AuthController.php
│   │   └── ...
│   ├── core/            # Core classes
│   │   ├── Database.php
│   │   ├── Model.php
│   │   ├── Controller.php
│   │   └── Router.php
│   ├── helpers/         # Helper functions
│   │   └── security.php
│   ├── middleware/      # Middleware (untuk future development)
│   ├── models/          # Model (M dalam MVC)
│   │   ├── UserModel.php
│   │   └── ...
│   └── views/           # View (V dalam MVC)
│       ├── auth/
│       │   └── login.php
│       └── ...
├── database/
│   └── schema.sql       # Database schema lengkap
├── public/
│   ├── assets/          # CSS, JS, images
│   ├── uploads/         # File uploads
│   ├── .htaccess        # URL rewriting
│   └── index.php        # Entry point
└── README.md
```

## 🚀 Instalasi

### Prasyarat
- PHP >= 7.4
- MySQL >= 5.7 atau MariaDB >= 10.2
- Apache dengan mod_rewrite atau Nginx
- Composer (opsional, untuk dependency management)

### Langkah Instalasi

1. **Clone repository**
```bash
cd /var/www/html
# Atau copy folder siakad-abc ke web root Anda
```

2. **Konfigurasi Database**
```bash
# Buat database baru
mysql -u root -p
CREATE DATABASE siakad_abc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Import schema
mysql -u root -p siakad_abc < database/schema.sql
```

3. **Konfigurasi Environment**
Edit file `app/config/database.php`:
```php
return [
    'host' => 'localhost',
    'port' => '3306',
    'database' => 'siakad_abc',
    'username' => 'root',
    'password' => 'your_password',
    // ...
];
```

4. **Set Permissions**
```bash
chmod -R 755 /path/to/siakad-abc
chmod -R 777 /path/to/siakad-abc/public/uploads
```

5. **Akses Aplikasi**
```
http://localhost/siakad-abc/public
```

## 🔐 Default Credentials

Setelah instalasi, buat user admin pertama secara manual:

```sql
INSERT INTO users (username, password_hash, nama, role, modul_kode, status) 
VALUES (
    'admin',
    '$2y$12$...' -- Generate dengan password_hash('your_password'),
    'Administrator',
    'admin',
    'user_management',
    'aktif'
);
```

Atau gunakan script PHP berikut untuk generate hash:
```php
<?php
require 'app/helpers/security.php';
echo hashPassword('YourSecurePassword123');
```

## 🛡️ Keamanan

### Fitur Keamanan yang Diimplementasikan:

1. **SQL Injection Prevention**
   - Prepared statements untuk semua query
   - PDO dengan emulated prepares disabled

2. **XSS Protection**
   - Output escaping dengan `htmlspecialchars()`
   - Input sanitization helper function

3. **CSRF Protection**
   - Token-based CSRF protection
   - Token validation di semua form POST

4. **Password Security**
   - Bcrypt hashing dengan cost factor 12
   - Password minimum length enforcement (recommended)

5. **Session Security**
   - Secure session configuration
   - Session regeneration on login

6. **Audit Trail**
   - Logging semua operasi CRUD
   - IP address tracking
   - Old/new data comparison

7. **Rate Limiting**
   - Login attempt throttling
   - Configurable time window

8. **File Upload Security**
   - MIME type validation
   - Extension whitelist
   - Safe filename generation
   - Size limits

## 📊 Database Schema Highlights

### Trigger: Validasi Kurikulum KRS
```sql
-- Mencegah mahasiswa mengambil MK di luar kurikulumnya
CREATE TRIGGER trg_krs_validate_kurikulum 
BEFORE INSERT ON krs_detail
FOR EACH ROW
BEGIN
    -- Validasi otomatis di level database
    -- Tidak bisa di-bypass dari application layer
END
```

### View: Transkrip Nilai Sementara
```sql
-- Menghitung nilai akhir, grade, dan IPK secara real-time
CREATE VIEW v_transkrip_nilai_sementara AS
SELECT ... -- Auto-calculation berdasarkan bobot komponen
```

## 🔄 Alur Kerja Berdasarkan Role

### Admin
1. Inisialisasi modul akses
2. Buat akun staff
3. Buka semester baru
4. Generate draft KRS mahasiswa
5. Monitor audit log

### Keuangan
1. Verifikasi pembayaran manual
2. Cetak kartu ujian

### Administrasi
1. Susun jadwal dan kelas
2. Input absensi
3. Input nilai (jika dosen berhalangan)

### Kaprodi/Sekretaris (Login sebagai Dosen)
1. Validasi KRS per MK
2. Awasi proses skripsi
3. Setujui pembimbing dan penguji

### Dosen
1. Tetapkan bobot penilaian
2. Input nilai mahasiswa
3. Input absensi
4. Bimbing skripsi

### Mahasiswa
1. Cek status pembayaran
2. Isi KRS (jika sudah approved)
3. Pantau nilai dan transkrip
4. Ajukan skripsi

## 📝 Development Guidelines

### Menambah Model Baru
```php
class MahasiswaModel extends Model {
    protected $table = 'mahasiswa';
    protected $primaryKey = 'nim';
    
    // Custom methods...
}
```

### Menambah Controller Baru
```php
class MahasiswaController extends Controller {
    private $mahasiswaModel;
    
    public function __construct() {
        parent::__construct();
        $this->mahasiswaModel = new MahasiswaModel();
    }
    
    public function index() {
        $this->requireRole(['admin', 'administrasi', 'prodi']);
        $data = $this->mahasiswaModel->all();
        $this->view('mahasiswa/index', ['data' => $data]);
    }
}
```

### Menambah Route
```php
// Di public/index.php
$router->get('/mahasiswa', 'mahasiswa@index');
$router->post('/mahasiswa/store', 'mahasiswa@store');
```

## 🐛 Troubleshooting

### Error: "Database connection failed"
- Periksa kredensial di `app/config/database.php`
- Pastikan MySQL service berjalan
- Cek firewall settings

### Error: "404 - Page not found"
- Pastikan mod_rewrite Apache enabled
- Cek `.htaccess` di folder public
- Verifikasi RewriteBase sesuai path instalasi

### Error: "Permission denied" pada uploads
```bash
chmod -R 777 public/uploads
chown -R www-data:www-data public/uploads
```

## 📄 License

&copy; 2024 Universitas ABC. All rights reserved.

## 👥 Contact

Untuk pertanyaan atau dukungan, hubungi:
- Email: admin@universitas-abc.ac.id
- Phone: +62 xxx xxxx xxxx
