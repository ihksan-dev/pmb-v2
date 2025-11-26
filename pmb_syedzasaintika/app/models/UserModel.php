<?php
require_once '../core/Model.php';

class UserModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function register($data)
    {
        $this->db->query('INSERT INTO users (username, email, password_hash, role) VALUES (:username, :email, :password_hash, :role)');
        
        // Bind values
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password_hash', $data['password']);
        $this->db->bind(':role', 'mahasiswa'); // Default role for registration
        
        // Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function login($username, $password)
    {
        $this->db->query('SELECT * FROM users WHERE username = :username OR email = :username');
        
        // Bind value
        $this->db->bind(':username', $username);
        
        $row = $this->db->single();
        
        if ($row) {
            $hashedPassword = $row['password_hash'];
            if (password_verify($password, $hashedPassword)) {
                return $row;
            }
        }
        
        return false;
    }

    public function findUserByUsername($username)
    {
        $this->db->query('SELECT * FROM users WHERE username = :username');
        $this->db->bind(':username', $username);
        
        $row = $this->db->single();
        
        return $row;
    }

    public function findUserByEmail($email)
    {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);
        
        $row = $this->db->single();
        
        return $row;
    }

    public function updateLastLogin($id)
    {
        $this->db->query('UPDATE users SET last_login = NOW() WHERE id_user = :id');
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    public function incrementLoginAttempts($username)
    {
        $this->db->query('UPDATE users SET login_attempts = login_attempts + 1 WHERE username = :username');
        $this->db->bind(':username', $username);
        
        return $this->db->execute();
    }

    public function resetLoginAttempts($username)
    {
        $this->db->query('UPDATE users SET login_attempts = 0, locked_until = NULL WHERE username = :username');
        $this->db->bind(':username', $username);
        
        return $this->db->execute();
    }

    public function getUserById($id)
    {
        $this->db->query('SELECT * FROM users WHERE id_user = :id');
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }
}