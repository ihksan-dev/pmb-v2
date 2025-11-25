-- Database: pmb
-- --------------------------------------------------------

-- Tabel untuk menyimpan data administrator
CREATE TABLE `admins` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel referensi untuk Program Studi
CREATE TABLE `prodi` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_prodi` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel untuk mengelola Tahun Ajaran
CREATE TABLE `academic_years` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `year_name` VARCHAR(9) NOT NULL UNIQUE,
  `is_active` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `year_name` (`year_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel untuk data Provinsi
CREATE TABLE `provinces` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel untuk data Kabupaten/Kota
CREATE TABLE `regencies` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `province_id` INT(11) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_province_id` (`province_id`),
  CONSTRAINT `fk_regencies_province` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel utama untuk data calon mahasiswa
CREATE TABLE `students` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `gender` ENUM('L', 'P') NOT NULL,
  `birth_date` DATE NOT NULL,
  `address` TEXT NOT NULL,
  `province_id` INT(11) NOT NULL,
  `regency_id` INT(11) NOT NULL,
  `prodi_id` INT(11) NOT NULL,
  `academic_year_id` INT(11) NOT NULL,
  `status` ENUM('pending', 'verified', 'rejected') NOT NULL DEFAULT 'pending',
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_login_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  CONSTRAINT `fk_students_prodi` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`id`),
  CONSTRAINT `fk_students_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
  CONSTRAINT `fk_students_province` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`),
  CONSTRAINT `fk_students_regency` FOREIGN KEY (`regency_id`) REFERENCES `regencies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert data awal untuk admin (username: admin, password: admin123)
INSERT INTO `admins` (`username`, `password`) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert data program studi awal
INSERT INTO `prodi` (`nama_prodi`) VALUES 
('Teknik Informatika'),
('Sistem Informasi'),
('Teknik Elektro'),
('Teknik Mesin'),
('Manajemen'),
('Akuntansi'),
('Ilmu Hukum'),
('Psikologi');

-- Insert data tahun ajaran awal (satu tahun diaktifkan)
INSERT INTO `academic_years` (`year_name`, `is_active`) VALUES 
('2024/2025', 1),
('2023/2024', 0),
('2022/2023', 0);

-- Insert data provinsi awal (contoh beberapa provinsi di Indonesia)
INSERT INTO `provinces` (`name`) VALUES 
('Aceh'),
('Sumatera Utara'),
('Sumatera Barat'),
('Riau'),
('Jambi'),
('Sumatera Selatan'),
('Bengkulu'),
('Lampung'),
('Kepulauan Bangka Belitung'),
('Kepulauan Riau'),
('DKI Jakarta'),
('Jawa Barat'),
('Jawa Tengah'),
('DI Yogyakarta'),
('Jawa Timur'),
('Banten'),
('Bali'),
('Nusa Tenggara Barat'),
('Nusa Tenggara Timur'),
('Kalimantan Barat'),
('Kalimantan Tengah'),
('Kalimantan Selatan'),
('Kalimantan Timur'),
('Kalimantan Utara'),
('Sulawesi Utara'),
('Sulawesi Tengah'),
('Sulawesi Selatan'),
('Sulawesi Tenggara'),
('Gorontalo'),
('Sulawesi Barat'),
('Maluku'),
('Maluku Utara'),
('Papua'),
('Papua Barat');

-- Insert data kabupaten/kota awal (contoh beberapa kabupaten/kota untuk beberapa provinsi)
INSERT INTO `regencies` (`province_id`, `name`) VALUES 
(1, 'Kabupaten Aceh Barat'),
(1, 'Kabupaten Aceh Barat Daya'),
(1, 'Kabupaten Aceh Besar'),
(1, 'Kota Banda Aceh'),
(2, 'Kabupaten Asahan'),
(2, 'Kabupaten Batu Bara'),
(2, 'Kota Medan'),
(2, 'Kota Pematang Siantar'),
(3, 'Kabupaten Agam'),
(3, 'Kabupaten Dharmasraya'),
(3, 'Kota Padang'),
(3, 'Kota Padang Panjang'),
(11, 'Kabupaten Kepulauan Seribu'),
(11, 'Kota Jakarta Pusat'),
(11, 'Kota Jakarta Utara'),
(11, 'Kota Jakarta Barat'),
(11, 'Kota Jakarta Selatan'),
(11, 'Kota Jakarta Timur'),
(12, 'Kabupaten Bandung'),
(12, 'Kabupaten Bekasi'),
(12, 'Kota Bandung'),
(12, 'Kota Bekasi'),
(12, 'Kota Depok'),
(13, 'Kabupaten Banjarnegara'),
(13, 'Kabupaten Banyumas'),
(13, 'Kota Semarang'),
(13, 'Kota Surakarta'),
(14, 'Kabupaten Bantul'),
(14, 'Kota Yogyakarta'),
(15, 'Kabupaten Bangkalan'),
(15, 'Kabupaten Banyuwangi'),
(15, 'Kota Surabaya'),
(15, 'Kota Malang'),
(16, 'Kabupaten Lebak'),
(16, 'Kota Tangerang'),
(16, 'Kota Tangerang Selatan'),
(17, 'Kabupaten Badung'),
(17, 'Kota Denpasar'),
(21, 'Kabupaten Barito Kuala'),
(21, 'Kota Banjarmasin'),
(23, 'Kabupaten Kutai Kartanegara'),
(23, 'Kota Balikpapan'),
(23, 'Kota Samarinda'),
(24, 'Kabupaten Bulungan'),
(24, 'Kota Tarakan'),
(25, 'Kabupaten Bolaang Mongondow'),
(25, 'Kota Manado'),
(27, 'Kabupaten Gowa'),
(27, 'Kota Makassar'),
(27, 'Kota Palopo'),
(33, 'Kabupaten Jayapura'),
(33, 'Kota Jayapura'),
(34, 'Kabupaten Manokwari'),
(34, 'Kota Manokwari');