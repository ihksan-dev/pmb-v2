<?php
require_once '../core/Database.php';

class ProvinsiModel 
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAllProvinsi()
    {
        $this->db->query("SELECT * FROM provinsi ORDER BY nama ASC");
        return $this->db->resultSet();
    }

    public function getProvinsiById($id)
    {
        $this->db->query("SELECT * FROM provinsi WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function createProvinsi($kode, $nama)
    {
        $this->db->query("INSERT INTO provinsi (kode, nama, created_at) 
                         VALUES (:kode, :nama, NOW())");
        $this->db->bind(':kode', $kode);
        $this->db->bind(':nama', $nama);
        
        return $this->db->execute();
    }

    public function updateProvinsi($id, $kode, $nama)
    {
        $this->db->query("UPDATE provinsi SET 
                         kode = :kode,
                         nama = :nama,
                         updated_at = NOW()
                         WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':kode', $kode);
        $this->db->bind(':nama', $nama);
        
        return $this->db->execute();
    }

    public function deleteProvinsi($id)
    {
        $this->db->query("DELETE FROM provinsi WHERE id = :id");
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }
}