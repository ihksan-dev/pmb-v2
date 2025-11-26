<?php
require_once '../core/Database.php';

class UserModel 
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getUserByUsername($username)
    {
        $this->db->query("SELECT * FROM users WHERE username = :username");
        $this->db->bind(':username', $username);
        return $this->db->single();
    }

    public function getUserByEmail($email)
    {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    public function getUserById($id)
    {
        $this->db->query("SELECT * FROM users WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function registerUser($username, $email, $password)
    {
        $this->db->query("INSERT INTO users (username, email, password, role, created_at) VALUES (:username, :email, :password, 'mahasiswa', NOW())");
        $this->db->bind(':username', $username);
        $this->db->bind(':email', $email);
        $this->db->bind(':password', $password);
        
        return $this->db->execute();
    }

    public function updateProfile($id, $username, $email)
    {
        $this->db->query("UPDATE users SET username = :username, email = :email WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':username', $username);
        $this->db->bind(':email', $email);
        
        return $this->db->execute();
    }

    public function updatePassword($id, $password)
    {
        $this->db->query("UPDATE users SET password = :password WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':password', $password);
        
        return $this->db->execute();
    }
}