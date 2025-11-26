-- ================================================
-- DATABASE: PMB UNIVERSITAS SYEDZA SAINTIKA
-- VERSION: 2.0 (dengan Captcha)
-- ================================================

CREATE DATABASE IF NOT EXISTS pmb_syedzasaintika 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE pmb_syedzasaintika;

-- ================================================
-- TABEL: users - Menyimpan data user (admin dan calon mahasiswa)
-- ================================================
CREATE TABLE users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'mahasiswa') NOT NULL DEFAULT 'mahasiswa',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB COMMENT='User accounts (admin & mahasiswa)';

-- ================================================
-- TABEL: provinsi - Data provinsi di Indonesia
-- ================================================
CREATE TABLE provinsi (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    kode_provinsi VARCHAR(10) NOT NULL UNIQUE,
    nama_provinsi VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nama_provinsi (nama_provinsi)
) ENGINE=InnoDB COMMENT='Data provinsi di Indonesia';

-- ================================================
-- TABEL: kabupaten - Data kabupaten/kota di Indonesia
-- ================================================
CREATE TABLE kabupaten (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    kode_kabupaten VARCHAR(10) NOT NULL UNIQUE,
    nama_kabupaten VARCHAR(100) NOT NULL,
    jenis ENUM('Kabupaten', 'Kota') NOT NULL,
    provinsi_id INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (provinsi_id) REFERENCES provinsi(id) ON DELETE CASCADE,
    INDEX idx_provinsi (provinsi_id),
    INDEX idx_nama_kabupaten (nama_kabupaten)
) ENGINE=InnoDB COMMENT='Data kabupaten/kota di Indonesia';

-- ================================================
-- TABEL: prodi - Program Studi yang tersedia
-- ================================================
CREATE TABLE prodi (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    kode_prodi VARCHAR(10) NOT NULL UNIQUE,
    nama_prodi VARCHAR(100) NOT NULL,
    jenjang ENUM('D3', 'D4', 'S1', 'S2', 'S3') NOT NULL,
    kuota INT(11) NOT NULL DEFAULT 0,
    biaya_pendaftaran DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    status ENUM('aktif', 'nonaktif') NOT NULL DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_jenjang (jenjang)
) ENGINE=InnoDB COMMENT='Program Studi yang tersedia';

-- ================================================
-- TABEL: tahun_ajaran - Tahun ajaran yang aktif
-- ================================================
CREATE TABLE tahun_ajaran (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    tahun_ajaran VARCHAR(20) NOT NULL UNIQUE COMMENT 'Format: 2025/2026',
    periode ENUM('Ganjil', 'Genap') NOT NULL,
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE NOT NULL,
    kuota_total INT(11) NOT NULL DEFAULT 0,
    status ENUM('aktif', 'nonaktif') NOT NULL DEFAULT 'nonaktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_tahun_ajaran (tahun_ajaran)
) ENGINE=InnoDB COMMENT='Tahun ajaran yang aktif';

-- ================================================
-- TABEL: mahasiswa - Data calon mahasiswa
-- ================================================
CREATE TABLE mahasiswa (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    nomor_pendaftaran VARCHAR(20) NOT NULL UNIQUE COMMENT 'Format: AB21X9J7',
    nik VARCHAR(16) NOT NULL UNIQUE,
    nama_lengkap VARCHAR(100) NOT NULL,
    tempat_lahir VARCHAR(50) NOT NULL,
    tanggal_lahir DATE NOT NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL,
    provinsi_id INT(11) NOT NULL,
    kabupaten_id INT(11) NOT NULL,
    alamat TEXT NOT NULL,
    kode_pos VARCHAR(10) NOT NULL,
    no_telepon VARCHAR(20) NOT NULL,
    asal_sekolah VARCHAR(100) NOT NULL,
    tahun_lulus INT(4) NOT NULL,
    prodi_id INT(11) NOT NULL,
    nama_ayah VARCHAR(100) NOT NULL,
    pekerjaan_ayah VARCHAR(50) NOT NULL,
    no_telepon_ayah VARCHAR(20) NOT NULL,
    nama_ibu VARCHAR(100) NOT NULL,
    pekerjaan_ibu VARCHAR(50) NOT NULL,
    no_telepon_ibu VARCHAR(20) NOT NULL,
    foto VARCHAR(255) NULL COMMENT 'Path file foto',
    ijazah VARCHAR(255) NULL COMMENT 'Path file ijazah',
    status ENUM('draft', 'submitted', 'verified', 'rejected') NOT NULL DEFAULT 'draft',
    keterangan TEXT NULL COMMENT 'Keterangan jika ditolak',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (provinsi_id) REFERENCES provinsi(id) ON DELETE RESTRICT,
    FOREIGN KEY (kabupaten_id) REFERENCES kabupaten(id) ON DELETE RESTRICT,
    FOREIGN KEY (prodi_id) REFERENCES prodi(id) ON DELETE RESTRICT,
    INDEX idx_user (user_id),
    INDEX idx_prodi (prodi_id),
    INDEX idx_status (status),
    INDEX idx_nik (nik),
    INDEX idx_nomor_pendaftaran (nomor_pendaftaran)
) ENGINE=InnoDB COMMENT='Data calon mahasiswa';

-- ================================================
-- TABEL: logs - Log aktivitas sistem
-- ================================================
CREATE TABLE logs (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NULL,
    action VARCHAR(100) NOT NULL COMMENT 'Jenis aksi: login, logout, insert, update, delete',
    table_name VARCHAR(50) NULL COMMENT 'Nama tabel yang diakses',
    record_id INT(11) NULL COMMENT 'ID record yang terlibat',
    old_values JSON NULL COMMENT 'Nilai lama sebelum update',
    new_values JSON NULL COMMENT 'Nilai baru setelah update',
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB COMMENT='Log aktivitas sistem';

-- ================================================
-- TABEL: sessions - Session storage (jika tidak pakai file session)
-- ================================================
CREATE TABLE sessions (
    id VARCHAR(128) NOT NULL PRIMARY KEY,
    user_id INT(11) NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    payload TEXT NOT NULL,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_last_activity (last_activity)
) ENGINE=InnoDB COMMENT='Session storage';

-- ================================================
-- TABEL BARU: captcha_sessions
-- Deskripsi: Menyimpan captcha untuk registrasi dan login
-- ================================================
CREATE TABLE captcha_sessions (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(128) NOT NULL COMMENT 'PHP session ID',
    captcha_code VARCHAR(6) NOT NULL COMMENT 'Kode captcha 6 digit angka',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP DEFAULT (CURRENT_TIMESTAMP + INTERVAL 5 MINUTE) COMMENT 'Captcha valid 5 menit',
    is_used TINYINT(1) DEFAULT 0 COMMENT '1=sudah digunakan, 0=belum',
    ip_address VARCHAR(45) NULL COMMENT 'IP address user',
    user_agent TEXT NULL COMMENT 'Browser user agent',
    INDEX idx_session (session_id),
    INDEX idx_expires (expires_at),
    INDEX idx_used (is_used)
) ENGINE=InnoDB COMMENT='Captcha untuk registrasi dan login';

-- ================================================
-- EVENT: Auto cleanup captcha expired
-- Jalankan setiap 1 jam
-- ================================================
DELIMITER $$
CREATE EVENT IF NOT EXISTS cleanup_captcha
ON SCHEDULE EVERY 1 HOUR
DO
BEGIN
    DELETE FROM captcha_sessions 
    WHERE expires_at < NOW() OR is_used = 1;
END$$
DELIMITER ;

-- Enable event scheduler
SET GLOBAL event_scheduler = ON;

-- ================================================
-- DATA AWAL: Tabel provinsi (contoh 5 provinsi)
-- ================================================
INSERT INTO provinsi (kode_provinsi, nama_provinsi) VALUES
('11', 'Aceh'),
('12', 'Sumatera Utara'),
('13', 'Sumatera Barat'),
('14', 'Riau'),
('15', 'Jambi');

-- ================================================
-- DATA AWAL: Tabel kabupaten (contoh dari 2 provinsi)
-- ================================================
INSERT INTO kabupaten (kode_kabupaten, nama_kabupaten, jenis, provinsi_id) VALUES
('1101', 'Simeulue', 'Kabupaten', 1),
('1102', 'Aceh Singkil', 'Kabupaten', 1),
('1103', 'Aceh Selatan', 'Kabupaten', 1),
('1104', 'Aceh Tenggara', 'Kabupaten', 1),
('1105', 'Aceh Timur', 'Kabupaten', 1),
('1106', 'Aceh Tengah', 'Kabupaten', 1),
('1107', 'Aceh Barat', 'Kabupaten', 1),
('1108', 'Aceh Besar', 'Kabupaten', 1),
('1109', 'Pidie', 'Kabupaten', 1),
('1110', 'Bireuen', 'Kabupaten', 1),
('1111', 'Aceh Utara', 'Kabupaten', 1),
('1112', 'Aceh Barat Daya', 'Kabupaten', 1),
('1113', 'Gayo Lues', 'Kabupaten', 1),
('1114', 'Aceh Tamiang', 'Kabupaten', 1),
('1115', 'Nagan Raya', 'Kabupaten', 1),
('1116', 'Aceh Jaya', 'Kabupaten', 1),
('1117', 'Bener Meriah', 'Kabupaten', 1),
('1118', 'Pidie Jaya', 'Kabupaten', 1),
('1171', 'Banda Aceh', 'Kota', 1),
('1172', 'Sabang', 'Kota', 1),
('1173', 'Langsa', 'Kota', 1),
('1174', 'Lhokseumawe', 'Kota', 1),
('1175', 'Subulussalam', 'Kota', 1),
('1201', 'Nias', 'Kabupaten', 2),
('1202', 'Mandailing Natal', 'Kabupaten', 2),
('1203', 'Tapanuli Selatan', 'Kabupaten', 2),
('1204', 'Tapanuli Tengah', 'Kabupaten', 2),
('1205', 'Tapanuli Utara', 'Kabupaten', 2),
('1206', 'Toba', 'Kabupaten', 2),
('1207', 'Labuhan Batu', 'Kabupaten', 2),
('1208', 'Asahan', 'Kabupaten', 2),
('1209', 'Simalungun', 'Kabupaten', 2),
('1210', 'Dairi', 'Kabupaten', 2),
('1211', 'Karo', 'Kabupaten', 2),
('1212', 'Deli Serdang', 'Kabupaten', 2),
('1213', 'Langkat', 'Kabupaten', 2),
('1214', 'Nias Selatan', 'Kabupaten', 2),
('1215', 'Humbang Hasundutan', 'Kabupaten', 2),
('1216', 'Pakpak Bharat', 'Kabupaten', 2),
('1217', 'Samosir', 'Kabupaten', 2),
('1218', 'Serdang Bedagai', 'Kabupaten', 2),
('1219', 'Batu Bara', 'Kabupaten', 2),
('1220', 'Padang Lawas Utara', 'Kabupaten', 2),
('1221', 'Padang Lawas', 'Kabupaten', 2),
('1222', 'Labuhan Batu Selatan', 'Kabupaten', 2),
('1223', 'Labuhan Batu Utara', 'Kabupaten', 2),
('1224', 'Nias Utara', 'Kabupaten', 2),
('1225', 'Nias Barat', 'Kabupaten', 2),
('1271', 'Medan', 'Kota', 2),
('1272', 'Pematangsiantar', 'Kota', 2),
('1273', 'Sibolga', 'Kota', 2),
('1274', 'Tanjung Balai', 'Kota', 2),
('1275', 'Binjai', 'Kota', 2),
('1276', 'Tebing Tinggi', 'Kota', 2),
('1277', 'Padang Sidempuan', 'Kota', 2),
('1278', 'Gunungsitoli', 'Kota', 2);

-- ================================================
-- DATA AWAL: Tabel prodi (contoh program studi)
-- ================================================
INSERT INTO prodi (kode_prodi, nama_prodi, jenjang, kuota, biaya_pendaftaran, status) VALUES
('TI11', 'Teknik Informatika', 'S1', 50, 250000.00, 'aktif'),
('SI11', 'Sistem Informasi', 'S1', 40, 250000.00, 'aktif'),
('MI31', 'Manajemen Informatika', 'D3', 30, 200000.00, 'aktif'),
('AK11', 'Akuntansi', 'S1', 45, 250000.00, 'aktif'),
('MN11', 'Manajemen', 'S1', 40, 250000.00, 'aktif'),
('BI11', 'Biologi', 'S1', 35, 250000.00, 'aktif'),
('KI11', 'Kimia', 'S1', 30, 250000.00, 'aktif'),
('FI11', 'Fisika', 'S1', 25, 250000.00, 'aktif');

-- ================================================
-- DATA AWAL: Tabel tahun_ajaran (contoh tahun ajaran)
-- ================================================
INSERT INTO tahun_ajaran (tahun_ajaran, periode, tanggal_mulai, tanggal_selesai, kuota_total, status) VALUES
('2025/2026', 'Ganjil', '2025-08-01', '2026-01-31', 300, 'aktif'),
('2024/2025', 'Ganjil', '2024-08-01', '2025-01-31', 300, 'nonaktif'),
('2024/2025', 'Genap', '2025-02-01', '2025-07-31', 300, 'nonaktif');

-- ================================================
-- DATA AWAL: Tabel users (admin default)
-- ================================================
INSERT INTO users (username, email, password, role, is_active) VALUES
('admin', 'admin@syedzasaintika.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1),
('testuser', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mahasiswa', 1);

-- ================================================
-- CATATAN:
-- Password default untuk semua user adalah 'password'
-- Ini hanya untuk development, di production harus diganti dengan password yang kuat
-- Hash password menggunakan bcrypt
-- ================================================