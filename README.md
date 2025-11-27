# PMB Syedza Saintika - Sistem Penerimaan Mahasiswa Baru

Sistem Penerimaan Mahasiswa Baru untuk Universitas Syedza Saintika berbasis web menggunakan PHP dan MySQL.

## Fitur Utama

### 1. Fitur Calon Mahasiswa
- Registrasi akun dengan captcha
- Login dengan sistem keamanan
- Form pendaftaran multi-step
- Upload dokumen (foto dan ijazah)
- Cek status pendaftaran
- Cetak formulir pendaftaran (jika diterima)

### 2. Fitur Admin
- Dashboard admin dengan statistik
- Verifikasi pendaftar
- Manajemen program studi
- Manajemen tahun ajaran
- Manajemen wilayah (provinsi/kabupaten)
- Laporan dan grafik statistik

## Struktur Database

Database telah dirancang dengan skema yang aman dan terstruktur:

- **users**: Tabel pengguna dengan otentikasi dan otorisasi
- **mahasiswa**: Data pendaftar mahasiswa
- **prodi**: Program studi yang tersedia
- **tahun_ajaran**: Tahun ajaran aktif
- **provinsi**: Data provinsi Indonesia
- **kabupaten**: Data kabupaten/kota per provinsi
- **captcha_sessions**: Tabel untuk keamanan captcha

## Keamanan Sistem

- Hash password dengan bcrypt
- Validasi CSRF
- Prepared statements untuk mencegah SQL injection
- Upload file aman dengan validasi ekstensi dan ukuran
- Rate limiting login
- Session management yang aman

## Instalasi

1. Buat database baru di MySQL
2. Import file `database_schema.sql` untuk membuat struktur tabel dan data awal
3. Konfigurasi koneksi database di file konfigurasi
4. Deploy folder ke web server (Apache/Nginx)
5. Akses sistem melalui browser

## Default Akun

### Admin
- Username: `admin`
- Password: `Admin@2025`
- Email: `admin@syedzasaintika.ac.id`

- Username: `superadmin`
- Password: `Super@2025`
- Email: `superadmin@syedzasaintika.ac.id`

### Mahasiswa Testing
- Username: `mahasiswa01`
- Password: `Test@123`
- Email: `mahasiswa01@example.com`

## Teknologi

- PHP Native (tanpa framework)
- MySQL Database
- JavaScript (frontend)
- CSS/HTML
- Apache/Nginx (web server)

## Catatan

File `database_schema.sql` berisi semua struktur tabel, relasi foreign key, dan data awal yang diperlukan untuk menjalankan sistem. Skema database telah dioptimalkan dengan urutan pembuatan tabel yang benar untuk menghindari masalah constraint.