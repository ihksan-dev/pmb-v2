<?php
require_once '../core/Database.php';

class ProdiModel 
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAllProdi()
    {
        $this->db->query("SELECT * FROM prodi ORDER BY nama ASC");
        return $this->db->resultSet();
    }

    public function getProdiById($id)
    {
        $this->db->query("SELECT * FROM prodi WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function createProdi($kode, $nama, $jenjang, $kuota, $biaya, $status)
    {
        $this->db->query("INSERT INTO prodi (kode, nama, jenjang, kuota, biaya_pendaftaran, status, created_at) 
                         VALUES (:kode, :nama, :jenjang, :kuota, :biaya, :status, NOW())");
        $this->db->bind(':kode', $kode);
        $this->db->bind(':nama', $nama);
        $this->db->bind(':jenjang', $jenjang);
        $this->db->bind(':kuota', $kuota);
        $this->db->bind(':biaya', $biaya);
        $this->db->bind(':status', $status);
        
        return $this->db->execute();
    }

    public function updateProdi($id, $kode, $nama, $jenjang, $kuota, $biaya, $status)
    {
        $this->db->query("UPDATE prodi SET 
                         kode = :kode,
                         nama = :nama,
                         jenjang = :jenjang,
                         kuota = :kuota,
                         biaya_pendaftaran = :biaya,
                         status = :status,
                         updated_at = NOW()
                         WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':kode', $kode);
        $this->db->bind(':nama', $nama);
        $this->db->bind(':jenjang', $jenjang);
        $this->db->bind(':kuota', $kuota);
        $this->db->bind(':biaya', $biaya);
        $this->db->bind(':status', $status);
        
        return $this->db->execute();
    }

    public function deleteProdi($id)
    {
        $this->db->query("DELETE FROM prodi WHERE id = :id");
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }
}