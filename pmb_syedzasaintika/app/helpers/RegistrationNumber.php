<?php
class RegistrationNumber 
{
    private $db;

    public function __construct()
    {
        require_once '../core/Database.php';
        $this->db = new Database();
    }

    public function generateNumber()
    {
        // Format: AB21X9J7 (contoh)
        // Generate a unique registration number
        $year = date('y'); // Last 2 digits of year
        $random_chars = $this->generateRandomString(4, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
        
        $registration_number = 'AB' . $year . $random_chars;
        
        // Check if already exists, regenerate if needed
        while ($this->checkDuplicate($registration_number)) {
            $random_chars = $this->generateRandomString(4, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
            $registration_number = 'AB' . $year . $random_chars;
        }
        
        return $registration_number;
    }

    private function generateRandomString($length = 4, $characters = '0123456789')
    {
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function checkDuplicate($registration_number)
    {
        $this->db->query("SELECT id FROM mahasiswa WHERE nomor_pendaftaran = :nomor");
        $this->db->bind(':nomor', $registration_number);
        $result = $this->db->single();
        return $result !== false;
    }
}