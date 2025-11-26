<?php
require_once '../core/Database.php';

class KabupatenModel 
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAllKabupaten()
    {
        $this->db->query("SELECT k.*, p.nama AS provinsi_nama 
                         FROM kabupaten k
                         LEFT JOIN provinsi p ON k.provinsi_id = p.id
                         ORDER BY p.nama, k.nama ASC");
        return $this->db->resultSet();
    }

    public function getKabupatenById($id)
    {
        $this->db->query("SELECT * FROM kabupaten WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getKabupatenByProvinsi($provinsi_id)
    {
        $this->db->query("SELECT * FROM kabupaten WHERE provinsi_id = :provinsi_id ORDER BY nama ASC");
        $this->db->bind(':provinsi_id', $provinsi_id);
        return $this->db->resultSet();
    }

    public function createKabupaten($provinsi_id, $kode, $nama, $jenis)
    {
        $this->db->query("INSERT INTO kabupaten (provinsi_id, kode, nama, jenis, created_at) 
                         VALUES (:provinsi_id, :kode, :nama, :jenis, NOW())");
        $this->db->bind(':provinsi_id', $provinsi_id);
        $this->db->bind(':kode', $kode);
        $this->db->bind(':nama', $nama);
        $this->db->bind(':jenis', $jenis);
        
        return $this->db->execute();
    }

    public function updateKabupaten($id, $provinsi_id, $kode, $nama, $jenis)
    {
        $this->db->query("UPDATE kabupaten SET 
                         provinsi_id = :provinsi_id,
                         kode = :kode,
                         nama = :nama,
                         jenis = :jenis,
                         updated_at = NOW()
                         WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':provinsi_id', $provinsi_id);
        $this->db->bind(':kode', $kode);
        $this->db->bind(':nama', $nama);
        $this->db->bind(':jenis', $jenis);
        
        return $this->db->execute();
    }

    public function deleteKabupaten($id)
    {
        $this->db->query("DELETE FROM kabupaten WHERE id = :id");
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }
}