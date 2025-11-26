-- DATABASE SCHEMA FOR PMB SYEDZA SAINTIKA

-- 5.1 Tabel Users
CREATE TABLE users (
    id_user INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('mahasiswa', 'admin') NOT NULL DEFAULT 'mahasiswa',
    email VARCHAR(100) UNIQUE NOT NULL,
    status_aktif TINYINT(1) DEFAULT 1,
    last_login DATETIME NULL,
    login_attempts INT(2) DEFAULT 0,
    locked_until DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 5.2 Tabel Mahasiswa
CREATE TABLE mahasiswa (
    id_mahasiswa INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_user INT(11) NOT NULL,
    id_prodi INT(11) NOT NULL,
    id_tahun_ajaran INT(11) NOT NULL,
    nomor_pendaftaran VARCHAR(20) UNIQUE NULL,
    nik VARCHAR(16) UNIQUE NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    tempat_lahir VARCHAR(50) NOT NULL,
    tanggal_lahir DATE NOT NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL,
    id_provinsi INT(11) NOT NULL,
    id_kabupaten INT(11) NOT NULL,
    alamat TEXT NOT NULL,
    kode_pos VARCHAR(10) NULL,
    no_telepon VARCHAR(15) NOT NULL,
    asal_sekolah VARCHAR(100) NOT NULL,
    tahun_lulus YEAR NOT NULL,
    nama_ortu VARCHAR(100) NOT NULL,
    pekerjaan_ortu VARCHAR(50) NOT NULL,
    no_telepon_ortu VARCHAR(15) NOT NULL,
    foto VARCHAR(255) NULL,
    ijazah VARCHAR(255) NULL,
    status_pendaftaran ENUM('draft','submitted','verified','rejected') DEFAULT 'draft',
    keterangan_reject TEXT NULL,
    tanggal_daftar DATETIME DEFAULT CURRENT_TIMESTAMP,
    tanggal_submit DATETIME NULL,
    verified_by INT(11) NULL,
    verified_at DATETIME NULL,
    FOREIGN KEY (id_user) REFERENCES users(id_user),
    FOREIGN KEY (id_prodi) REFERENCES prodi(id_prodi),
    FOREIGN KEY (id_tahun_ajaran) REFERENCES tahun_ajaran(id_tahun),
    FOREIGN KEY (id_provinsi) REFERENCES provinsi(id_provinsi),
    FOREIGN KEY (id_kabupaten) REFERENCES kabupaten(id_kabupaten)
) ENGINE=InnoDB;

-- 5.3 Tabel Captcha Sessions
CREATE TABLE captcha_sessions (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(128) NOT NULL,
    captcha_code VARCHAR(6) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    is_used TINYINT(1) DEFAULT 0,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL
) ENGINE=InnoDB;

-- Additional tables referenced in foreign keys
CREATE TABLE prodi (
    id_prodi INT(11) AUTO_INCREMENT PRIMARY KEY,
    kode_prodi VARCHAR(10) UNIQUE NOT NULL,
    nama_prodi VARCHAR(100) NOT NULL,
    jenjang ENUM('D3', 'D4', 'S1') NOT NULL,
    kuota INT(4) NOT NULL,
    biaya_pendaftaran DECIMAL(10,2) NOT NULL,
    status_aktif TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE tahun_ajaran (
    id_tahun INT(11) AUTO_INCREMENT PRIMARY KEY,
    tahun_ajaran VARCHAR(9) NOT NULL, -- Format: 2025/2026
    periode ENUM('Ganjil', 'Genap') NOT NULL,
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE NOT NULL,
    kuota_total INT(4) NOT NULL,
    status_aktif TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE provinsi (
    id_provinsi INT(11) AUTO_INCREMENT PRIMARY KEY,
    kode_provinsi VARCHAR(5) UNIQUE NOT NULL,
    nama_provinsi VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE kabupaten (
    id_kabupaten INT(11) AUTO_INCREMENT PRIMARY KEY,
    kode_kabupaten VARCHAR(8) UNIQUE NOT NULL,
    nama_kabupaten VARCHAR(100) NOT NULL,
    jenis ENUM('Kabupaten', 'Kota') NOT NULL,
    id_provinsi INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_provinsi) REFERENCES provinsi(id_provinsi)
) ENGINE=InnoDB;

-- 5.4 Default Data

-- Admin Accounts
INSERT INTO users (username, password_hash, role, email, status_aktif) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'admin@syedzasaintika.ac.id', 1),
('superadmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'superadmin@syedzasaintika.ac.id', 1);

-- Mahasiswa Testing
INSERT INTO users (username, password_hash, role, email, status_aktif) VALUES 
('mahasiswa01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mahasiswa', 'mahasiswa01@example.com', 1);

-- Program Studi
INSERT INTO prodi (kode_prodi, nama_prodi, jenjang, kuota, biaya_pendaftaran) VALUES
('MIK-D3', 'Manajemen Informasi Kesehatan (D3)', 'D3', 50, 200000.00),
('TI-S1', 'Teknik Informatika (S1)', 'S1', 40, 250000.00),
('KEB-D4', 'Kebidanan (D4)', 'D4', 60, 300000.00),
('SI-S1', 'Sistem Informasi (S1)', 'S1', 35, 250000.00),
('FARM-D3', 'Farmasi (D3)', 'D3', 45, 300000.00),
('KEP-D3', 'Keperawatan (D3)', 'D3', 55, 300000.00);

-- Tahun Ajaran
INSERT INTO tahun_ajaran (tahun_ajaran, periode, tanggal_mulai, tanggal_selesai, kuota_total, status_aktif) VALUES
('2025/2026', 'Ganjil', '2025-08-01', '2026-01-31', 285, 1);

-- Provinsi (38 provinsi Indonesia)
INSERT INTO provinsi (kode_provinsi, nama_provinsi) VALUES
('11', 'Aceh'),
('12', 'Sumatera Utara'),
('13', 'Sumatera Barat'),
('14', 'Riau'),
('15', 'Jambi'),
('16', 'Sumatera Selatan'),
('17', 'Bengkulu'),
('18', 'Lampung'),
('19', 'Kepulauan Bangka Belitung'),
('21', 'Kepulauan Riau'),
('31', 'DKI Jakarta'),
('32', 'Jawa Barat'),
('33', 'Jawa Tengah'),
('34', 'DI Yogyakarta'),
('35', 'Jawa Timur'),
('36', 'Banten'),
('51', 'Bali'),
('52', 'Nusa Tenggara Barat'),
('53', 'Nusa Tenggara Timur'),
('61', 'Kalimantan Barat'),
('62', 'Kalimantan Tengah'),
('63', 'Kalimantan Selatan'),
('64', 'Kalimantan Timur'),
('65', 'Kalimantan Utara'),
('71', 'Sulawesi Utara'),
('72', 'Sulawesi Tengah'),
('73', 'Sulawesi Selatan'),
('74', 'Sulawesi Tenggara'),
('75', 'Gorontalo'),
('76', 'Sulawesi Barat'),
('81', 'Maluku'),
('82', 'Maluku Utara'),
('91', 'Papua'),
('92', 'Papua Barat'),
('93', 'Papua Tengah'),
('94', 'Papua Pegunungan'),
('95', 'Papua Selatan'),
('96', 'Papua Barat Daya');

-- Kabupaten for Sumatera Barat (19 kabupaten/kota) + Jakarta (6)
INSERT INTO kabupaten (kode_kabupaten, nama_kabupaten, jenis, id_provinsi) VALUES
('1301', 'Kabupaten Pesisir Selatan', 'Kabupaten', 13),
('1302', 'Kabupaten Solok', 'Kabupaten', 13),
('1303', 'Kabupaten Sijunjung', 'Kabupaten', 13),
('1304', 'Kabupaten Tanah Datar', 'Kabupaten', 13),
('1305', 'Kabupaten Padang Pariaman', 'Kabupaten', 13),
('1306', 'Kabupaten Agam', 'Kabupaten', 13),
('1307', 'Kabupaten Lima Puluh Kota', 'Kabupaten', 13),
('1308', 'Kabupaten Pasaman', 'Kabupaten', 13),
('1309', 'Kabupaten Solok Selatan', 'Kabupaten', 13),
('1310', 'Kabupaten Dharmasraya', 'Kabupaten', 13),
('1311', 'Kabupaten Pasaman Barat', 'Kabupaten', 13),
('1312', 'Kabupaten Kepulauan Mentawai', 'Kabupaten', 13),
('1371', 'Kota Padang', 'Kota', 13),
('1372', 'Kota Solok', 'Kota', 13),
('1373', 'Kota Sawah Lunto', 'Kota', 13),
('1374', 'Kota Padang Panjang', 'Kota', 13),
('1375', 'Kota Bukittinggi', 'Kota', 13),
('1376', 'Kota Payakumbuh', 'Kota', 13),
('1377', 'Kota Pariaman', 'Kota', 13),
('3171', 'Kota Jakarta Pusat', 'Kota', 31),
('3172', 'Kota Jakarta Utara', 'Kota', 31),
('3173', 'Kota Jakarta Barat', 'Kota', 31),
('3174', 'Kota Jakarta Selatan', 'Kota', 31),
('3175', 'Kota Jakarta Timur', 'Kota', 31),
('3176', 'Kepulauan Seribu', 'Kabupaten', 31);