<?php
require_once '../core/Model.php';

class MahasiswaModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function createDraftRecord($idUser)
    {
        // Get the active year
        $this->db->query('SELECT id_tahun FROM tahun_ajaran WHERE status_aktif = 1 LIMIT 1');
        $tahunAjaran = $this->db->single();
        
        if (!$tahunAjaran) {
            return false; // No active year
        }

        $this->db->query('INSERT INTO mahasiswa (id_user, id_prodi, id_tahun_ajaran, status_pendaftaran) VALUES (:id_user, 0, :id_tahun_ajaran, :status)');
        
        $this->db->bind(':id_user', $idUser);
        $this->db->bind(':id_tahun_ajaran', $tahunAjaran['id_tahun']);
        $this->db->bind(':status', 'draft');
        
        return $this->db->execute();
    }

    public function getMahasiswaByUserId($idUser)
    {
        $this->db->query('SELECT m.*, p.nama_prodi, ta.tahun_ajaran, pr.nama_provinsi, k.nama_kabupaten 
                         FROM mahasiswa m 
                         LEFT JOIN prodi p ON m.id_prodi = p.id_prodi 
                         LEFT JOIN tahun_ajaran ta ON m.id_tahun_ajaran = ta.id_tahun 
                         LEFT JOIN provinsi pr ON m.id_provinsi = pr.id_provinsi 
                         LEFT JOIN kabupaten k ON m.id_kabupaten = k.id_kabupaten 
                         WHERE m.id_user = :id_user');
        
        $this->db->bind(':id_user', $idUser);
        
        return $this->db->single();
    }

    public function updateMahasiswaData($idUser, $data)
    {
        $this->db->query('UPDATE mahasiswa SET 
                         nik = :nik, 
                         nama_lengkap = :nama_lengkap, 
                         tempat_lahir = :tempat_lahir, 
                         tanggal_lahir = :tanggal_lahir, 
                         jenis_kelamin = :jenis_kelamin, 
                         id_provinsi = :id_provinsi, 
                         id_kabupaten = :id_kabupaten, 
                         alamat = :alamat, 
                         kode_pos = :kode_pos, 
                         no_telepon = :no_telepon, 
                         asal_sekolah = :asal_sekolah, 
                         tahun_lulus = :tahun_lulus, 
                         nama_ortu = :nama_ortu, 
                         pekerjaan_ortu = :pekerjaan_ortu, 
                         no_telepon_ortu = :no_telepon_ortu,
                         foto = :foto,
                         ijazah = :ijazah
                         WHERE id_user = :id_user');
        
        $this->db->bind(':nik', $data['nik']);
        $this->db->bind(':nama_lengkap', $data['nama_lengkap']);
        $this->db->bind(':tempat_lahir', $data['tempat_lahir']);
        $this->db->bind(':tanggal_lahir', $data['tanggal_lahir']);
        $this->db->bind(':jenis_kelamin', $data['jenis_kelamin']);
        $this->db->bind(':id_provinsi', $data['id_provinsi']);
        $this->db->bind(':id_kabupaten', $data['id_kabupaten']);
        $this->db->bind(':alamat', $data['alamat']);
        $this->db->bind(':kode_pos', $data['kode_pos']);
        $this->db->bind(':no_telepon', $data['no_telepon']);
        $this->db->bind(':asal_sekolah', $data['asal_sekolah']);
        $this->db->bind(':tahun_lulus', $data['tahun_lulus']);
        $this->db->bind(':nama_ortu', $data['nama_ortu']);
        $this->db->bind(':pekerjaan_ortu', $data['pekerjaan_ortu']);
        $this->db->bind(':no_telepon_ortu', $data['no_telepon_ortu']);
        $this->db->bind(':foto', $data['foto']);
        $this->db->bind(':ijazah', $data['ijazah']);
        $this->db->bind(':id_user', $idUser);
        
        return $this->db->execute();
    }

    public function submitPendaftaran($idUser, $nomorPendaftaran)
    {
        $this->db->query('UPDATE mahasiswa SET 
                         nomor_pendaftaran = :nomor_pendaftaran,
                         status_pendaftaran = :status,
                         tanggal_submit = NOW()
                         WHERE id_user = :id_user');
        
        $this->db->bind(':nomor_pendaftaran', $nomorPendaftaran);
        $this->db->bind(':status', 'submitted');
        $this->db->bind(':id_user', $idUser);
        
        return $this->db->execute();
    }

    public function getAllPendaftar($filter = [])
    {
        $sql = 'SELECT m.*, u.username, u.email, p.nama_prodi, pr.nama_provinsi, k.nama_kabupaten 
                FROM mahasiswa m 
                JOIN users u ON m.id_user = u.id_user
                LEFT JOIN prodi p ON m.id_prodi = p.id_prodi 
                LEFT JOIN provinsi pr ON m.id_provinsi = pr.id_provinsi 
                LEFT JOIN kabupaten k ON m.id_kabupaten = k.id_kabupaten';
        
        $params = [];
        
        if (!empty($filter['status'])) {
            $sql .= ' WHERE m.status_pendaftaran = :status';
            $params[':status'] = $filter['status'];
        }
        
        if (!empty($filter['prodi'])) {
            if (strpos($sql, 'WHERE') === false) {
                $sql .= ' WHERE m.id_prodi = :prodi';
            } else {
                $sql .= ' AND m.id_prodi = :prodi';
            }
            $params[':prodi'] = $filter['prodi'];
        }
        
        if (!empty($filter['provinsi'])) {
            if (strpos($sql, 'WHERE') === false) {
                $sql .= ' WHERE m.id_provinsi = :provinsi';
            } else {
                $sql .= ' AND m.id_provinsi = :provinsi';
            }
            $params[':provinsi'] = $filter['provinsi'];
        }
        
        if (!empty($filter['kabupaten'])) {
            if (strpos($sql, 'WHERE') === false) {
                $sql .= ' WHERE m.id_kabupaten = :kabupaten';
            } else {
                $sql .= ' AND m.id_kabupaten = :kabupaten';
            }
            $params[':kabupaten'] = $filter['kabupaten'];
        }
        
        if (!empty($filter['tanggal_mulai']) && !empty($filter['tanggal_selesai'])) {
            if (strpos($sql, 'WHERE') === false) {
                $sql .= ' WHERE m.tanggal_submit BETWEEN :tanggal_mulai AND :tanggal_selesai';
            } else {
                $sql .= ' AND m.tanggal_submit BETWEEN :tanggal_mulai AND :tanggal_selesai';
            }
            $params[':tanggal_mulai'] = $filter['tanggal_mulai'];
            $params[':tanggal_selesai'] = $filter['tanggal_selesai'];
        }
        
        $sql .= ' ORDER BY m.tanggal_submit DESC';
        
        $this->db->query($sql);
        
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        
        return $this->db->resultSet();
    }

    public function getPendaftarById($id)
    {
        $this->db->query('SELECT m.*, u.username, u.email, p.nama_prodi, ta.tahun_ajaran, pr.nama_provinsi, k.nama_kabupaten 
                         FROM mahasiswa m 
                         JOIN users u ON m.id_user = u.id_user
                         LEFT JOIN prodi p ON m.id_prodi = p.id_prodi 
                         LEFT JOIN tahun_ajaran ta ON m.id_tahun_ajaran = ta.id_tahun 
                         LEFT JOIN provinsi pr ON m.id_provinsi = pr.id_provinsi 
                         LEFT JOIN kabupaten k ON m.id_kabupaten = k.id_kabupaten 
                         WHERE m.id_mahasiswa = :id');
        
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }

    public function verifyPendaftar($id, $verifiedBy, $keterangan = null, $status = 'verified')
    {
        if ($status === 'rejected' && empty($keterangan)) {
            return false; // Keterangan is required for rejection
        }
        
        $sql = 'UPDATE mahasiswa SET 
                status_pendaftaran = :status,
                verified_by = :verified_by,
                verified_at = NOW()';
        
        if ($status === 'rejected') {
            $sql .= ', keterangan_reject = :keterangan';
        }
        
        $sql .= ' WHERE id_mahasiswa = :id';
        
        $this->db->query($sql);
        $this->db->bind(':status', $status);
        $this->db->bind(':verified_by', $verifiedBy);
        $this->db->bind(':id', $id);
        
        if ($status === 'rejected') {
            $this->db->bind(':keterangan', $keterangan);
        }
        
        return $this->db->execute();
    }
}