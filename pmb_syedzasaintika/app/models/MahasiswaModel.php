<?php
require_once '../core/Database.php';

class MahasiswaModel 
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getMahasiswaByUserId($user_id)
    {
        $this->db->query("SELECT * FROM mahasiswa WHERE user_id = :user_id");
        $this->db->bind(':user_id', $user_id);
        return $this->db->single();
    }

    public function getPendaftaranByUserId($user_id)
    {
        $this->db->query("SELECT m.*, p.nama AS prodi_nama, prov.nama AS provinsi_nama, k.nama AS kabupaten_nama 
                         FROM mahasiswa m
                         LEFT JOIN prodi p ON m.prodi_id = p.id
                         LEFT JOIN provinsi prov ON m.provinsi_id = prov.id
                         LEFT JOIN kabupaten k ON m.kabupaten_id = k.id
                         WHERE m.user_id = :user_id");
        $this->db->bind(':user_id', $user_id);
        return $this->db->single();
    }

    public function saveDraft($user_id, $data)
    {
        // Check if record exists
        $existing = $this->getMahasiswaByUserId($user_id);
        
        if ($existing) {
            // Update existing record
            $this->db->query("UPDATE mahasiswa SET 
                             nik = :nik, 
                             nama_lengkap = :nama_lengkap, 
                             tempat_lahir = :tempat_lahir, 
                             tanggal_lahir = :tanggal_lahir, 
                             jenis_kelamin = :jenis_kelamin, 
                             provinsi_id = :provinsi_id, 
                             kabupaten_id = :kabupaten_id, 
                             alamat = :alamat, 
                             kode_pos = :kode_pos, 
                             no_telepon = :no_telepon, 
                             asal_sekolah = :asal_sekolah, 
                             tahun_lulus = :tahun_lulus, 
                             prodi_id = :prodi_id, 
                             nama_ayah = :nama_ayah, 
                             nama_ibu = :nama_ibu, 
                             pekerjaan_ayah = :pekerjaan_ayah, 
                             pekerjaan_ibu = :pekerjaan_ibu, 
                             no_telepon_ortu = :no_telepon_ortu, 
                             updated_at = NOW()
                             WHERE user_id = :user_id");
        } else {
            // Insert new record
            $this->db->query("INSERT INTO mahasiswa (
                             user_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, 
                             jenis_kelamin, provinsi_id, kabupaten_id, alamat, kode_pos, 
                             no_telepon, asal_sekolah, tahun_lulus, prodi_id, 
                             nama_ayah, nama_ibu, pekerjaan_ayah, pekerjaan_ibu, 
                             no_telepon_ortu, status, created_at, updated_at
                             ) VALUES (
                             :user_id, :nik, :nama_lengkap, :tempat_lahir, :tanggal_lahir, 
                             :jenis_kelamin, :provinsi_id, :kabupaten_id, :alamat, :kode_pos, 
                             :no_telepon, :asal_sekolah, :tahun_lulus, :prodi_id, 
                             :nama_ayah, :nama_ibu, :pekerjaan_ayah, :pekerjaan_ibu, 
                             :no_telepon_ortu, 'draft', NOW(), NOW()
                             )");
        }
        
        $this->bindUserData($data, $user_id);
        return $this->db->execute();
    }

    public function submitForm($user_id, $data, $foto_filename, $ijazah_filename, $registration_number)
    {
        $this->db->query("UPDATE mahasiswa SET 
                         nomor_pendaftaran = :nomor_pendaftaran,
                         foto = :foto,
                         ijazah = :ijazah,
                         status = 'submitted',
                         updated_at = NOW()
                         WHERE user_id = :user_id");
        
        $this->db->bind(':nomor_pendaftaran', $registration_number);
        $this->db->bind(':foto', $foto_filename);
        $this->db->bind(':ijazah', $ijazah_filename);
        $this->db->bind(':user_id', $user_id);
        
        return $this->db->execute();
    }

    private function bindUserData($data, $user_id)
    {
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':nik', $data['nik'] ?? '');
        $this->db->bind(':nama_lengkap', $data['nama_lengkap'] ?? '');
        $this->db->bind(':tempat_lahir', $data['tempat_lahir'] ?? '');
        $this->db->bind(':tanggal_lahir', $data['tanggal_lahir'] ?? '');
        $this->db->bind(':jenis_kelamin', $data['jenis_kelamin'] ?? '');
        $this->db->bind(':provinsi_id', $data['provinsi_id'] ?? 0);
        $this->db->bind(':kabupaten_id', $data['kabupaten_id'] ?? 0);
        $this->db->bind(':alamat', $data['alamat'] ?? '');
        $this->db->bind(':kode_pos', $data['kode_pos'] ?? '');
        $this->db->bind(':no_telepon', $data['no_telepon'] ?? '');
        $this->db->bind(':asal_sekolah', $data['asal_sekolah'] ?? '');
        $this->db->bind(':tahun_lulus', $data['tahun_lulus'] ?? '');
        $this->db->bind(':prodi_id', $data['prodi_id'] ?? 0);
        $this->db->bind(':nama_ayah', $data['nama_ayah'] ?? '');
        $this->db->bind(':nama_ibu', $data['nama_ibu'] ?? '');
        $this->db->bind(':pekerjaan_ayah', $data['pekerjaan_ayah'] ?? '');
        $this->db->bind(':pekerjaan_ibu', $data['pekerjaan_ibu'] ?? '');
        $this->db->bind(':no_telepon_ortu', $data['no_telepon_ortu'] ?? '');
    }

    public function getAllPendaftar($filters = [])
    {
        $sql = "SELECT m.*, u.username, p.nama AS prodi_nama, prov.nama AS provinsi_nama, k.nama AS kabupaten_nama 
                FROM mahasiswa m
                LEFT JOIN users u ON m.user_id = u.id
                LEFT JOIN prodi p ON m.prodi_id = p.id
                LEFT JOIN provinsi prov ON m.provinsi_id = prov.id
                LEFT JOIN kabupaten k ON m.kabupaten_id = k.id";
        
        $conditions = [];
        $params = [];
        
        if (!empty($filters['status'])) {
            $conditions[] = "m.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['prodi_id'])) {
            $conditions[] = "m.prodi_id = :prodi_id";
            $params[':prodi_id'] = $filters['prodi_id'];
        }
        
        if (!empty($filters['provinsi_id'])) {
            $conditions[] = "m.provinsi_id = :provinsi_id";
            $params[':provinsi_id'] = $filters['provinsi_id'];
        }
        
        if (!empty($filters['kabupaten_id'])) {
            $conditions[] = "m.kabupaten_id = :kabupaten_id";
            $params[':kabupaten_id'] = $filters['kabupaten_id'];
        }
        
        if (!empty($filters['search'])) {
            $conditions[] = "(m.nama_lengkap LIKE :search OR m.nomor_pendaftaran LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        $sql .= " ORDER BY m.created_at DESC";
        
        $this->db->query($sql);
        
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        
        return $this->db->resultSet();
    }

    public function getPendaftarById($id)
    {
        $this->db->query("SELECT m.*, u.username, p.nama AS prodi_nama, prov.nama AS provinsi_nama, k.nama AS kabupaten_nama 
                         FROM mahasiswa m
                         LEFT JOIN users u ON m.user_id = u.id
                         LEFT JOIN prodi p ON m.prodi_id = p.id
                         LEFT JOIN provinsi prov ON m.provinsi_id = prov.id
                         LEFT JOIN kabupaten k ON m.kabupaten_id = k.id
                         WHERE m.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function verifikasiPendaftar($id, $keterangan = '')
    {
        $this->db->query("UPDATE mahasiswa SET 
                         status = 'verified',
                         keterangan = :keterangan,
                         updated_at = NOW()
                         WHERE id = :id");
        $this->db->bind(':keterangan', $keterangan);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function rejectPendaftar($id, $keterangan)
    {
        $this->db->query("UPDATE mahasiswa SET 
                         status = 'rejected',
                         keterangan = :keterangan,
                         updated_at = NOW()
                         WHERE id = :id");
        $this->db->bind(':keterangan', $keterangan);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function getDashboardStats()
    {
        $stats = [];
        
        // Total pendaftar tahun ini
        $this->db->query("SELECT COUNT(*) as total FROM mahasiswa WHERE YEAR(created_at) = YEAR(NOW())");
        $stats['total_pendaftar'] = $this->db->single()['total'];
        
        // Pending verifikasi
        $this->db->query("SELECT COUNT(*) as total FROM mahasiswa WHERE status = 'submitted'");
        $stats['pending'] = $this->db->single()['total'];
        
        // Terverifikasi
        $this->db->query("SELECT COUNT(*) as total FROM mahasiswa WHERE status = 'verified'");
        $stats['verified'] = $this->db->single()['total'];
        
        // Ditolak
        $this->db->query("SELECT COUNT(*) as total FROM mahasiswa WHERE status = 'rejected'");
        $stats['rejected'] = $this->db->single()['total'];
        
        return $stats;
    }

    public function getGrafikData()
    {
        $data = [];
        
        // Pendaftar per prodi
        $this->db->query("SELECT p.nama AS prodi, COUNT(m.id) AS jumlah 
                         FROM mahasiswa m
                         LEFT JOIN prodi p ON m.prodi_id = p.id
                         WHERE m.status = 'verified'
                         GROUP BY m.prodi_id, p.nama");
        $data['pendaftar_per_prodi'] = $this->db->resultSet();
        
        // Pendaftar per provinsi
        $this->db->query("SELECT prov.nama AS provinsi, COUNT(m.id) AS jumlah 
                         FROM mahasiswa m
                         LEFT JOIN provinsi prov ON m.provinsi_id = prov.id
                         WHERE m.status = 'verified'
                         GROUP BY m.provinsi_id, prov.nama
                         ORDER BY jumlah DESC
                         LIMIT 10");
        $data['pendaftar_per_provinsi'] = $this->db->resultSet();
        
        // Trend pendaftar per bulan
        $this->db->query("SELECT MONTH(created_at) AS bulan, COUNT(*) AS jumlah 
                         FROM mahasiswa 
                         WHERE YEAR(created_at) = YEAR(NOW())
                         GROUP BY MONTH(created_at)
                         ORDER BY bulan");
        $data['trend_bulanan'] = $this->db->resultSet();
        
        // Status pendaftaran
        $this->db->query("SELECT status, COUNT(*) AS jumlah FROM mahasiswa GROUP BY status");
        $data['status_pendaftaran'] = $this->db->resultSet();
        
        return $data;
    }

    public function exportToExcel($filters = [])
    {
        // Implementation for Excel export
        // This would typically use a library like PhpSpreadsheet
        // For now, we'll return the filtered data
        return $this->getAllPendaftar($filters);
    }

    public function exportToPDF($filters = [])
    {
        // Implementation for PDF export
        // This would typically use a library like TCPDF or FPDF
        // For now, we'll return the filtered data
        return $this->getAllPendaftar($filters);
    }
}