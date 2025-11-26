<?php
require_once '../core/Database.php';

class TahunAjaranModel 
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAllTahunAjaran()
    {
        $this->db->query("SELECT * FROM tahun_ajaran ORDER BY tahun_mulai DESC");
        return $this->db->resultSet();
    }

    public function getTahunAjaranById($id)
    {
        $this->db->query("SELECT * FROM tahun_ajaran WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function createTahunAjaran($tahun, $periode, $tanggal_mulai, $tanggal_selesai, $kuota_total, $status)
    {
        // If setting as active, deactivate others
        if ($status === 'aktif') {
            $this->db->query("UPDATE tahun_ajaran SET status = 'tidak_aktif' WHERE status = 'aktif'");
            $this->db->execute();
        }
        
        $this->db->query("INSERT INTO tahun_ajaran (tahun_ajaran, periode, tanggal_mulai, tanggal_selesai, kuota_total, status, created_at) 
                         VALUES (:tahun, :periode, :tanggal_mulai, :tanggal_selesai, :kuota_total, :status, NOW())");
        $this->db->bind(':tahun', $tahun);
        $this->db->bind(':periode', $periode);
        $this->db->bind(':tanggal_mulai', $tanggal_mulai);
        $this->db->bind(':tanggal_selesai', $tanggal_selesai);
        $this->db->bind(':kuota_total', $kuota_total);
        $this->db->bind(':status', $status);
        
        return $this->db->execute();
    }

    public function updateTahunAjaran($id, $tahun, $periode, $tanggal_mulai, $tanggal_selesai, $kuota_total, $status)
    {
        // If setting as active, deactivate others
        if ($status === 'aktif') {
            $this->db->query("UPDATE tahun_ajaran SET status = 'tidak_aktif' WHERE status = 'aktif' AND id != :id");
            $this->db->execute();
        }
        
        $this->db->query("UPDATE tahun_ajaran SET 
                         tahun_ajaran = :tahun,
                         periode = :periode,
                         tanggal_mulai = :tanggal_mulai,
                         tanggal_selesai = :tanggal_selesai,
                         kuota_total = :kuota_total,
                         status = :status,
                         updated_at = NOW()
                         WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':tahun', $tahun);
        $this->db->bind(':periode', $periode);
        $this->db->bind(':tanggal_mulai', $tanggal_mulai);
        $this->db->bind(':tanggal_selesai', $tanggal_selesai);
        $this->db->bind(':kuota_total', $kuota_total);
        $this->db->bind(':status', $status);
        
        return $this->db->execute();
    }

    public function deleteTahunAjaran($id)
    {
        $this->db->query("DELETE FROM tahun_ajaran WHERE id = :id");
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    public function aktifkanTahunAjaran($id)
    {
        // Deactivate all
        $this->db->query("UPDATE tahun_ajaran SET status = 'tidak_aktif'");
        $this->db->execute();
        
        // Activate selected
        $this->db->query("UPDATE tahun_ajaran SET status = 'aktif', updated_at = NOW() WHERE id = :id");
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }
}