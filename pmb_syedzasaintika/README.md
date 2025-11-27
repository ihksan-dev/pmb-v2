# Sistem Pendaftaran Mahasiswa Baru (PMB) - Universitas Syedza Saintika

## Deskripsi
Sistem Pendaftaran Mahasiswa Baru (PMB) adalah aplikasi berbasis web yang dirancang untuk mengelola proses pendaftaran mahasiswa baru di Universitas Syedza Saintika. Sistem ini memungkinkan calon mahasiswa untuk mendaftar secara online dan admin untuk mengelola proses verifikasi pendaftaran.

## Fitur Utama
- **Pendaftaran Online**: Calon mahasiswa dapat mendaftar akun dan mengisi formulir pendaftaran secara online
- **Verifikasi Admin**: Admin dapat memverifikasi data pendaftar dan menyetujui atau menolak pendaftaran
- **Manajemen Program Studi**: Admin dapat mengelola data program studi
- **Manajemen Tahun Ajaran**: Admin dapat mengelola tahun ajaran aktif
- **Keamanan**: Sistem dilengkapi dengan captcha, CSRF protection, dan validasi input
- **Cetak Formulir**: Pendaftar dapat mencetak formulir pendaftaran setelah diverifikasi

## Teknologi yang Digunakan
- PHP Native (MVC Pattern)
- MySQL Database
- Bootstrap 5 (Frontend Framework)
- JavaScript (Client-side functionality)

## Struktur Aplikasi
```
pmb_syedzasaintika/
│
├── app/
│   ├── controllers/          # Controller files
│   ├── models/              # Model files
│   ├── views/               # View files
│   ├── middleware/          # Security layers
│   └── helpers/             # Helper functions
│
├── public/                  # Document root
│   ├── index.php           # Entry point
│   ├── assets/             # CSS, JS, Images
│   └── uploads/            # File uploads
│
├── core/                    # Core framework files
├── config/                  # Configuration files
├── logs/                    # Application logs
└── docs/                    # Documentation
```

## Instalasi
1. Clone repository ini
2. Buat database MySQL baru
3. Import file `database_schema.sql` ke database
4. Konfigurasi koneksi database di `config/config.php`
5. Akses aplikasi melalui web server

## Konfigurasi Database
Ubah konfigurasi di `config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'pmb_syedzasaintika');
```

## Hak Akses Default
### Admin
- Username: `admin`
- Password: `Admin@2025`
- Email: `admin@syedzasaintika.ac.id`

### Mahasiswa Testing
- Username: `mahasiswa01`
- Password: `Test@123`
- Email: `mahasiswa01@example.com`

## Lisensi
Proyek ini adalah bagian dari sistem informasi Universitas Syedza Saintika.