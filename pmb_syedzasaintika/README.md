# Sistem Informasi PMB Universitas Syedza Saintika

Sistem Informasi Penerimaan Mahasiswa Baru (PMB) untuk Universitas Syedza Saintika.

## Struktur Direktori

```
/pmb_syedzasaintika/
│
├── /app/
│   ├── /controllers/              # Controller layer
│   ├── /models/                   # Model layer
│   ├── /views/                    # View layer
│   ├── /middleware/               # Security middleware
│   └── /helpers/                  # Helper functions
│
├── /public/                       # Publicly accessible
│   ├── index.php                  # Entry point aplikasi
│   ├── .htaccess                  # URL rewriting
│   ├── /assets/                   # Static assets
│   └── /uploads/                  # Uploaded files
│
├── /core/                         # Core MVC framework
├── /config/                       # Configuration files
├── /logs/                         # Application logs
└── /docs/                         # Documentation
```

## Fitur Utama

- Sistem autentikasi dengan captcha
- Dashboard calon mahasiswa
- Dashboard admin untuk mengelola pendaftar
- CRUD program studi
- CRUD tahun ajaran
- CRUD provinsi dan kabupaten
- Sistem verifikasi pendaftaran
- Grafik dan statistik pendaftar
- Cetak formulir pendaftaran

## Instalasi

1. Clone repository ini
2. Buat database dan import skema SQL
3. Konfigurasi koneksi database di `config/database.php`
4. Akses aplikasi melalui web server

## Teknologi

- PHP Native (MVC Pattern)
- MySQL Database
- JavaScript (AJAX)
- CSS/HTML