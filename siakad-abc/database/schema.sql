-- ============================================================
-- SIAKAD UNIVERSITAS ABC - DATABASE SCHEMA v1.3
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- ============================================================
-- 🔐 1. PUSAT KEBIJAKAN AKSES (WALIKI SEMUA MODUL)
-- ============================================================
CREATE TABLE IF NOT EXISTS akses_modul (
    modul_kode VARCHAR(50) PRIMARY KEY,
    modul_nama VARCHAR(100) NOT NULL,
    table_name VARCHAR(100) NOT NULL,
    allowed_roles JSON NOT NULL,
    is_aktif BOOLEAN DEFAULT TRUE,
    updated_by_username VARCHAR(50) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 👥 2. AUTENTIKASI STAFF (Admin, Keuangan, Administrasi)
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    username VARCHAR(50) PRIMARY KEY,
    password_hash VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    role ENUM('admin','keuangan','administrasi') NOT NULL,
    modul_kode VARCHAR(50) NOT NULL DEFAULT 'user_management',
    status ENUM('aktif','nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (modul_kode) REFERENCES akses_modul(modul_kode) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 🏛 3. STRUKTUR AKADEMIK (Prodi → Dosen Circular Resolve)
-- ============================================================
CREATE TABLE IF NOT EXISTS prodi (
    kode VARCHAR(20) PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    jenjang ENUM('sarjana','diploma','magister','doktor') NOT NULL,
    kaprodi_nuptk VARCHAR(30),
    sekretaris_prodi_nuptk VARCHAR(30),
    email VARCHAR(100),
    phone VARCHAR(20),
    modul_kode VARCHAR(50) NOT NULL DEFAULT 'master_prodi',
    status ENUM('aktif','nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_leadership_diff CHECK (kaprodi_nuptk != sekretaris_prodi_nuptk OR sekretaris_prodi_nuptk IS NULL),
    FOREIGN KEY (modul_kode) REFERENCES akses_modul(modul_kode) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS dosen (
    nuptk VARCHAR(30) PRIMARY KEY,
    password_hash VARCHAR(255) NOT NULL,
    prodi_kode VARCHAR(20) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    jabatan_fungsional VARCHAR(50),
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    modul_kode VARCHAR(50) NOT NULL DEFAULT 'master_dosen',
    status ENUM('aktif','cuti','nonaktif') DEFAULT 'aktif',
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (prodi_kode) REFERENCES prodi(kode) ON DELETE RESTRICT,
    FOREIGN KEY (modul_kode) REFERENCES akses_modul(modul_kode) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Resolve Circular FK: Prodi ← Dosen
ALTER TABLE prodi ADD CONSTRAINT fk_prodi_kaprodi
    FOREIGN KEY (kaprodi_nuptk) REFERENCES dosen(nuptk) ON DELETE SET NULL;
ALTER TABLE prodi ADD CONSTRAINT fk_prodi_sekretaris
    FOREIGN KEY (sekretaris_prodi_nuptk) REFERENCES dosen(nuptk) ON DELETE SET NULL;

-- ============================================================
-- 🎓 4. MAHASISWA (Auth Embedded | PK = NIM)
-- ============================================================
CREATE TABLE IF NOT EXISTS mahasiswa (
    nim VARCHAR(20) PRIMARY KEY,
    password_hash VARCHAR(255) NOT NULL,
    prodi_kode VARCHAR(20) NOT NULL,
    kurikulum_id BIGINT NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    angkatan YEAR NOT NULL,
    dosen_pa_nuptk VARCHAR(30),
    modul_kode VARCHAR(50) NOT NULL DEFAULT 'master_mahasiswa',
    status ENUM('aktif','cuti','lulus','do','nonaktif') DEFAULT 'aktif',
    tanggal_masuk DATE,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (prodi_kode) REFERENCES prodi(kode) ON DELETE RESTRICT,
    FOREIGN KEY (dosen_pa_nuptk) REFERENCES dosen(nuptk) ON DELETE SET NULL,
    FOREIGN KEY (modul_kode) REFERENCES akses_modul(modul_kode) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 📚 5. KURIKULUM & MATA KULIAH (1 Kurikulum → Banyak MK)
-- ============================================================
CREATE TABLE IF NOT EXISTS kurikulum (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    prodi_kode VARCHAR(20) NOT NULL,
    tahun_mulai YEAR NOT NULL,
    tahun_akhir YEAR,
    total_sks_wajib DECIMAL(4,2) DEFAULT 144.00,
    modul_kode VARCHAR(50) NOT NULL DEFAULT 'master_kurikulum',
    status ENUM('aktif','arsip') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (prodi_kode) REFERENCES prodi(kode) ON DELETE CASCADE,
    FOREIGN KEY (modul_kode) REFERENCES akses_modul(modul_kode) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS mata_kuliah (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    kurikulum_id BIGINT NOT NULL,
    kode VARCHAR(20) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    sks DECIMAL(3,1) NOT NULL CHECK (sks > 0 AND sks <= 6),
    semester_rekomendasi INT CHECK (semester_rekomendasi BETWEEN 1 AND 14),
    tipe ENUM('wajib','pilihan','sp') DEFAULT 'wajib',
    modul_kode VARCHAR(50) NOT NULL DEFAULT 'master_mk',
    status ENUM('aktif','nonaktif') DEFAULT 'aktif',
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kurikulum_id) REFERENCES kurikulum(id) ON DELETE RESTRICT,
    FOREIGN KEY (modul_kode) REFERENCES akses_modul(modul_kode) ON DELETE RESTRICT,
    UNIQUE KEY uk_mk_kurikulum_kode (kurikulum_id, kode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 📅 6. SEMESTER, KELAS & JADWAL
-- ============================================================
CREATE TABLE IF NOT EXISTS semester_aktif (
    tahun_akademik VARCHAR(20) PRIMARY KEY,
    nama VARCHAR(50) NOT NULL,
    tipe ENUM('ganjil','genap','pendek') NOT NULL,
    batas_sks_default DECIMAL(4,1) NOT NULL,
    modul_kode VARCHAR(50) NOT NULL DEFAULT 'manajemen_semester',
    status_krs_global ENUM('closed','open') DEFAULT 'closed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (modul_kode) REFERENCES akses_modul(modul_kode) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS kelas (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    semester_tahun_akademik VARCHAR(20) NOT NULL,
    mk_id BIGINT NOT NULL,
    kode_kelas VARCHAR(30) NOT NULL,
    kuota INT DEFAULT 40 CHECK (kuota > 0),
    modul_kode VARCHAR(50) NOT NULL DEFAULT 'penjadwalan_kelas',
    is_bobot_locked BOOLEAN DEFAULT FALSE,
    status ENUM('aktif','dibuka','selesai') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (semester_tahun_akademik) REFERENCES semester_aktif(tahun_akademik) ON DELETE RESTRICT,
    FOREIGN KEY (mk_id) REFERENCES mata_kuliah(id) ON DELETE RESTRICT,
    FOREIGN KEY (modul_kode) REFERENCES akses_modul(modul_kode) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS jadwal_kuliah (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    kelas_id BIGINT NOT NULL,
    ruangan VARCHAR(50) NOT NULL,
    hari ENUM('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu') NOT NULL,
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    modul_kode VARCHAR(50) NOT NULL DEFAULT 'penjadwalan_kelas',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE CASCADE,
    FOREIGN KEY (modul_kode) REFERENCES akses_modul(modul_kode) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS kelas_dosen (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    kelas_id BIGINT NOT NULL,
    dosen_nuptk VARCHAR(30) NOT NULL,
    peran ENUM('dosen_tunggal','dosen_team') DEFAULT 'dosen_tunggal',
    modul_kode VARCHAR(50) NOT NULL DEFAULT 'penjadwalan_kelas',
    status_aktif BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE CASCADE,
    FOREIGN KEY (dosen_nuptk) REFERENCES dosen(nuptk) ON DELETE CASCADE,
    FOREIGN KEY (modul_kode) REFERENCES akses_modul(modul_kode) ON DELETE RESTRICT,
    UNIQUE KEY uk_kelas_dosen (kelas_id, dosen_nuptk)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 💰 7. KEUANGAN & WORKFLOW KRS
-- ============================================================
CREATE TABLE IF NOT EXISTS pembayaran_manual (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    mahasiswa_nim VARCHAR(20) NOT NULL,
    semester_tahun_akademik VARCHAR(20) NOT NULL,
    modul_kode VARCHAR(50) NOT NULL DEFAULT 'keuangan_pembayaran',
    bukti_bayar_url VARCHAR(255),
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    verified_by_username VARCHAR(50),
    verified_at TIMESTAMP NULL,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mahasiswa_nim) REFERENCES mahasiswa(nim) ON DELETE CASCADE,
    FOREIGN KEY (semester_tahun_akademik) REFERENCES semester_aktif(tahun_akademik) ON DELETE RESTRICT,
    FOREIGN KEY (verified_by_username) REFERENCES users(username) ON DELETE SET NULL,
    FOREIGN KEY (modul_kode) REFERENCES akses_modul(modul_kode) ON DELETE RESTRICT,
    UNIQUE KEY uk_pembayaran_mhs_sem (mahasiswa_nim, semester_tahun_akademik)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS krs_header (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    mahasiswa_nim VARCHAR(20) NOT NULL,
    semester_tahun_akademik VARCHAR(20) NOT NULL,
    modul_kode VARCHAR(50) NOT NULL DEFAULT 'krs_workflow',
    total_sks DECIMAL(4,1) DEFAULT 0.00,
    status_krs ENUM('draft','submitted','approved','rejected') DEFAULT 'draft',
    admin_generated_by VARCHAR(50),
    finance_approved_by VARCHAR(50),
    prodi_validated_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mahasiswa_nim) REFERENCES mahasiswa(nim) ON DELETE CASCADE,
    FOREIGN KEY (semester_tahun_akademik) REFERENCES semester_aktif(tahun_akademik) ON DELETE RESTRICT,
    FOREIGN KEY (admin_generated_by) REFERENCES users(username) ON DELETE SET NULL,
    FOREIGN KEY (finance_approved_by) REFERENCES users(username) ON DELETE SET NULL,
    FOREIGN KEY (modul_kode) REFERENCES akses_modul(modul_kode) ON DELETE RESTRICT,
    UNIQUE KEY uk_krs_mhs_sem (mahasiswa_nim, semester_tahun_akademik)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS krs_detail (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    krs_header_id BIGINT NOT NULL,
    kelas_id BIGINT NOT NULL,
    modul_kode VARCHAR(50) NOT NULL DEFAULT 'krs_workflow',
    status_validasi_prodi ENUM('pending','approved','rejected') DEFAULT 'pending',
    catatan_prodi TEXT,
    validated_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (krs_header_id) REFERENCES krs_header(id) ON DELETE CASCADE,
    FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE RESTRICT,
    FOREIGN KEY (modul_kode) REFERENCES akses_modul(modul_kode) ON DELETE RESTRICT,
    UNIQUE KEY uk_krs_detail_mk (krs_header_id, kelas_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 📝 8. PENILAIAN & KARTU UJIAN
-- ============================================================
CREATE TABLE IF NOT EXISTS komponen_nilai (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    kelas_id BIGINT NOT NULL,
    nama_komponen ENUM('Kehadiran','Tugas','Quiz','Praktek','UTS','UAS','Lainnya') NOT NULL,
    bobot_persen DECIMAL(5,2) NOT NULL CHECK (bobot_persen >= 0 AND bobot_persen <= 100),
    modul_kode VARCHAR(50) NOT NULL DEFAULT 'penilaian_akademik',
    max_nilai DECIMAL(5,2) DEFAULT 100.00,
    status_bobot ENUM('draft','locked') DEFAULT 'draft',
    diatur_oleh_nuptk VARCHAR(30) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE CASCADE,
    FOREIGN KEY (diatur_oleh_nuptk) REFERENCES dosen(nuptk) ON DELETE RESTRICT,
    FOREIGN KEY (modul_kode) REFERENCES akses_modul(modul_kode) ON DELETE RESTRICT,
    UNIQUE KEY uk_komponen_kelas_nama (kelas_id, nama_komponen)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS nilai_mahasiswa (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    kelas_id BIGINT NOT NULL,
    mahasiswa_nim VARCHAR(20) NOT NULL,
    modul_kode VARCHAR(50) NOT NULL DEFAULT 'penilaian_akademik',
    komponen_id BIGINT NOT NULL,
    nilai DECIMAL(5,2) NOT NULL CHECK (nilai >= 0 AND nilai <= 100),
    diinput_oleh_user VARCHAR(50) NOT NULL,
    diinput_oleh_role ENUM('admin','administrasi','dosen') NOT NULL,
    catatan_input TEXT,
    version INT DEFAULT 1,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE CASCADE,
    FOREIGN KEY (mahasiswa_nim) REFERENCES mahasiswa(nim) ON DELETE CASCADE,
    FOREIGN KEY (komponen_id) REFERENCES komponen_nilai(id) ON DELETE RESTRICT,
    FOREIGN KEY (modul_kode) REFERENCES akses_modul(modul_kode) ON DELETE RESTRICT,
    UNIQUE KEY uk_nilai_mhs_komponen (mahasiswa_nim, komponen_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS kartu_ujian (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    mahasiswa_nim VARCHAR(20) NOT NULL,
    semester_tahun_akademik VARCHAR(20) NOT NULL,
    modul_kode VARCHAR(50) NOT NULL DEFAULT 'keuangan_pembayaran',
    jenis_ujian ENUM('UTS','UAS') NOT NULL,
    is_printed_by_finance BOOLEAN DEFAULT FALSE,
    printed_at TIMESTAMP NULL,
    printed_by_username VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mahasiswa_nim) REFERENCES mahasiswa(nim) ON DELETE CASCADE,
    FOREIGN KEY (semester_tahun_akademik) REFERENCES semester_aktif(tahun_akademik) ON DELETE RESTRICT,
    FOREIGN KEY (printed_by_username) REFERENCES users(username) ON DELETE SET NULL,
    FOREIGN KEY (modul_kode) REFERENCES akses_modul(modul_kode) ON DELETE RESTRICT,
    UNIQUE KEY uk_kartu_ujian (mahasiswa_nim, semester_tahun_akademik, jenis_ujian)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 📅 9. ABSENSI PERKULIAHAN (14-16 Pertemuan)
-- ============================================================
CREATE TABLE IF NOT EXISTS absensi_perkuliahan (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    kelas_id BIGINT NOT NULL,
    mahasiswa_nim VARCHAR(20) NOT NULL,
    pertemuan_ke INT NOT NULL CHECK (pertemuan_ke BETWEEN 1 AND 16),
    status_kehadiran ENUM('hadir','sakit','izin','alpa') NOT NULL,
    tanggal_pertemuan DATE,
    diinput_oleh_user VARCHAR(50) NOT NULL,
    diinput_oleh_role ENUM('administrasi','dosen') NOT NULL,
    modul_kode VARCHAR(50) NOT NULL DEFAULT 'manajemen_absensi',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE CASCADE,
    FOREIGN KEY (mahasiswa_nim) REFERENCES mahasiswa(nim) ON DELETE CASCADE,
    FOREIGN KEY (modul_kode) REFERENCES akses_modul(modul_kode) ON DELETE RESTRICT,
    UNIQUE KEY uk_absensi_kelas_mhs_pert (kelas_id, mahasiswa_nim, pertemuan_ke),
    INDEX idx_absensi_kelas (kelas_id),
    INDEX idx_absensi_mhs (mahasiswa_nim)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 🎓 10. SKRIPSI / TUGAS AKHIR
-- ============================================================
CREATE TABLE IF NOT EXISTS skripsi (
    nim VARCHAR(20) PRIMARY KEY,
    judul TEXT NOT NULL,
    tahun_pengajuan YEAR NOT NULL,
    prodi_kode VARCHAR(20) NOT NULL,
    pembimbing_1_nuptk VARCHAR(30) NOT NULL,
    pembimbing_2_nuptk VARCHAR(30),
    penguji_1_nuptk VARCHAR(30),
    penguji_2_nuptk VARCHAR(30),
    status_skripsi ENUM('proposal','bimbingan','seminar','sidang','lulus','revisi','dibatalkan') DEFAULT 'proposal',
    nilai_akhir DECIMAL(4,2),
    tanggal_sidang DATE,
    file_skripsi_url VARCHAR(255),
    modul_kode VARCHAR(50) NOT NULL DEFAULT 'manajemen_skripsi',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (nim) REFERENCES mahasiswa(nim) ON DELETE CASCADE,
    FOREIGN KEY (prodi_kode) REFERENCES prodi(kode) ON DELETE RESTRICT,
    FOREIGN KEY (pembimbing_1_nuptk) REFERENCES dosen(nuptk) ON DELETE RESTRICT,
    FOREIGN KEY (pembimbing_2_nuptk) REFERENCES dosen(nuptk) ON DELETE SET NULL,
    FOREIGN KEY (penguji_1_nuptk) REFERENCES dosen(nuptk) ON DELETE SET NULL,
    FOREIGN KEY (penguji_2_nuptk) REFERENCES dosen(nuptk) ON DELETE SET NULL,
    FOREIGN KEY (modul_kode) REFERENCES akses_modul(modul_kode) ON DELETE RESTRICT,
    CONSTRAINT chk_pembimbing_unik CHECK (pembimbing_1_nuptk != pembimbing_2_nuptk OR pembimbing_2_nuptk IS NULL),
    CONSTRAINT chk_penguji_unik CHECK (penguji_1_nuptk IS NULL OR penguji_2_nuptk IS NULL OR penguji_1_nuptk != penguji_2_nuptk),
    INDEX idx_skripsi_status (status_skripsi),
    INDEX idx_skripsi_pembimbing1 (pembimbing_1_nuptk),
    INDEX idx_skripsi_tahun (tahun_pengajuan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 🛡 11. AUDIT & KONFIGURASI
-- ============================================================
CREATE TABLE IF NOT EXISTS audit_log (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_identifier VARCHAR(50) NOT NULL,
    action VARCHAR(50) NOT NULL,
    modul_kode VARCHAR(50) NOT NULL,
    table_name VARCHAR(50) NOT NULL,
    record_id BIGINT NOT NULL,
    old_data JSON,
    new_data JSON,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (modul_kode) REFERENCES akses_modul(modul_kode) ON DELETE RESTRICT,
    INDEX idx_audit_user_action (user_identifier, action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS system_config (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    config_key VARCHAR(50) UNIQUE NOT NULL,
    config_value JSON NOT NULL,
    modul_kode VARCHAR(50) NOT NULL DEFAULT 'system_config',
    updated_by_username VARCHAR(50),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by_username) REFERENCES users(username) ON DELETE SET NULL,
    FOREIGN KEY (modul_kode) REFERENCES akses_modul(modul_kode) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TRIGGER: Validasi Kurikulum saat Input KRS Detail
-- ============================================================
DELIMITER $$
CREATE TRIGGER IF NOT EXISTS trg_krs_validate_kurikulum 
BEFORE INSERT ON krs_detail
FOR EACH ROW
BEGIN
    DECLARE mhs_kurikulum BIGINT;
    DECLARE mk_kurikulum BIGINT;

    SELECT kurikulum_id INTO mhs_kurikulum
    FROM mahasiswa
    WHERE nim = (SELECT mahasiswa_nim FROM krs_header WHERE id = NEW.krs_header_id);

    SELECT m.kurikulum_id INTO mk_kurikulum 
    FROM kelas k
    JOIN mata_kuliah m ON k.mk_id = m.id 
    WHERE k.id = NEW.kelas_id;

    IF mhs_kurikulum != mk_kurikulum THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'GAGAL: Mahasiswa hanya dapat mengambil mata kuliah sesuai kurikulum yang ditetapkan.';
    END IF;
END$$
DELIMITER ;

-- ============================================================
-- VIEW: Transkrip Nilai Sementara
-- ============================================================
CREATE OR REPLACE VIEW v_transkrip_nilai_sementara AS
SELECT
    m.nim, m.nama_lengkap, p.kode AS prodi_kode, p.nama AS prodi_nama,
    krs.semester_tahun_akademik, k.kode_kelas, mk.kode AS kode_mk, mk.nama AS nama_mk, mk.sks,
    COALESCE(na.nilai_akhir, 0) AS nilai_akhir,
    COALESCE(na.grade, 'E') AS grade,
    COALESCE(na.bobot, 0.00) AS bobot_grade,
    COALESCE(na.sks_x_bobot, 0.00) AS sks_x_bobot,
    krs.status_krs, kd.status_validasi_prodi,
    CASE WHEN na.nilai_akhir IS NOT NULL THEN 'Sudah Dinilai' ELSE 'Belum Dinilai' END AS status_penilaian
FROM mahasiswa m
JOIN prodi p ON m.prodi_kode = p.kode
JOIN krs_header krs ON m.nim = krs.mahasiswa_nim
JOIN krs_detail kd ON krs.id = kd.krs_header_id
JOIN kelas k ON kd.kelas_id = k.id
JOIN mata_kuliah mk ON k.mk_id = mk.id
LEFT JOIN (
    SELECT
        nm.kelas_id, nm.mahasiswa_nim,
        ROUND(SUM(nm.nilai * kn.bobot_persen) / 100, 2) AS nilai_akhir,
        CASE
            WHEN SUM(nm.nilai * kn.bobot_persen) / 100 >= 80 THEN 'A'
            WHEN SUM(nm.nilai * kn.bobot_persen) / 100 >= 70 THEN 'B'
            WHEN SUM(nm.nilai * kn.bobot_persen) / 100 >= 60 THEN 'C'
            WHEN SUM(nm.nilai * kn.bobot_persen) / 100 >= 50 THEN 'D'
            ELSE 'E'
        END AS grade,
        CASE
            WHEN SUM(nm.nilai * kn.bobot_persen) / 100 >= 80 THEN 4.00
            WHEN SUM(nm.nilai * kn.bobot_persen) / 100 >= 70 THEN 3.00
            WHEN SUM(nm.nilai * kn.bobot_persen) / 100 >= 60 THEN 2.00
            WHEN SUM(nm.nilai * kn.bobot_persen) / 100 >= 50 THEN 1.00
            ELSE 0.00
        END AS bobot,
        mk.sks * CASE
            WHEN SUM(nm.nilai * kn.bobot_persen) / 100 >= 80 THEN 4.00
            WHEN SUM(nm.nilai * kn.bobot_persen) / 100 >= 70 THEN 3.00
            WHEN SUM(nm.nilai * kn.bobot_persen) / 100 >= 60 THEN 2.00
            WHEN SUM(nm.nilai * kn.bobot_persen) / 100 >= 50 THEN 1.00
            ELSE 0.00
        END AS sks_x_bobot
    FROM nilai_mahasiswa nm
    JOIN komponen_nilai kn ON nm.komponen_id = kn.id
    JOIN kelas cls ON nm.kelas_id = cls.id
    JOIN mata_kuliah mk ON cls.mk_id = mk.id
    GROUP BY nm.kelas_id, nm.mahasiswa_nim, mk.sks
) na ON k.id = na.kelas_id AND m.nim = na.mahasiswa_nim;

-- ============================================================
-- SEED DATA: Modul Akses
-- ============================================================
INSERT INTO akses_modul (modul_kode, modul_nama, table_name, allowed_roles, is_aktif, updated_by_username) VALUES
('user_management', 'Manajemen User Staff', 'users', '["admin"]', TRUE, 'admin'),
('master_prodi', 'Data Program Studi', 'prodi', '["admin","prodi"]', TRUE, 'admin'),
('master_dosen', 'Data Dosen', 'dosen', '["admin","prodi"]', TRUE, 'admin'),
('master_mahasiswa', 'Data Mahasiswa', 'mahasiswa', '["admin","administrasi","prodi"]', TRUE, 'admin'),
('master_kurikulum', 'Kurikulum & MK', 'kurikulum, mata_kuliah', '["admin","prodi"]', TRUE, 'admin'),
('manajemen_semester', 'Konfigurasi Semester', 'semester_aktif', '["admin"]', TRUE, 'admin'),
('penjadwalan_kelas', 'Jadwal & Kelas', 'kelas, jadwal_kuliah, kelas_dosen', '["admin","administrasi"]', TRUE, 'admin'),
('keuangan_pembayaran', 'Verifikasi Bayar & Kartu Ujian', 'pembayaran_manual, kartu_ujian', '["keuangan","mahasiswa"]', TRUE, 'admin'),
('krs_workflow', 'Input & Validasi KRS', 'krs_header, krs_detail', '["mahasiswa","prodi","admin"]', TRUE, 'admin'),
('penilaian_akademik', 'Bobot & Input Nilai', 'komponen_nilai, nilai_mahasiswa', '["dosen','administrasi','admin"]', TRUE, 'admin'),
('manajemen_absensi', 'Absensi Perkuliahan (14-16 Pert)', 'absensi_perkuliahan', '["administrasi','dosen','mahasiswa"]', TRUE, 'admin'),
('manajemen_skripsi', 'Skripsi & Sidang', 'skripsi', '["mahasiswa','dosen','prodi','admin"]', TRUE, 'admin'),
('system_config', 'Konfigurasi Sistem', 'system_config', '["admin"]', TRUE, 'admin');

COMMIT;
