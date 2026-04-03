<?php
/**
 * User Model
 * Mengelola data users (admin, keuangan, administrasi)
 */

class UserModel extends Model {
    protected $table = 'users';
    protected $primaryKey = 'username';
    
    /**
     * Find user by username with role check
     */
    public function findByUsername($username) {
        return $this->findBy('username', $username);
    }
    
    /**
     * Authenticate user
     */
    public function authenticate($username, $password) {
        $user = $this->findByUsername($username);
        
        if (!$user) {
            return ['success' => false, 'message' => 'Username tidak ditemukan'];
        }
        
        if ($user['status'] !== 'aktif') {
            return ['success' => false, 'message' => 'Akun Anda tidak aktif. Hubungi administrator.'];
        }
        
        if (!verifyPassword($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Password salah'];
        }
        
        // Check module access
        if (!hasModuleAccess($user['modul_kode'], $user['role'])) {
            return ['success' => false, 'message' => 'Anda tidak memiliki akses ke modul ini'];
        }
        
        return [
            'success' => true,
            'user' => [
                'username' => $user['username'],
                'nama' => $user['nama'],
                'role' => $user['role'],
                'modul_kode' => $user['modul_kode']
            ]
        ];
    }
    
    /**
     * Create new user
     */
    public function createUser($data) {
        // Validate role
        $allowedRoles = ['admin', 'keuangan', 'administrasi'];
        if (!in_array($data['role'], $allowedRoles)) {
            return ['success' => false, 'message' => 'Role tidak valid'];
        }
        
        // Check if username exists
        if ($this->findByUsername($data['username'])) {
            return ['success' => false, 'message' => 'Username sudah digunakan'];
        }
        
        // Hash password
        $data['password_hash'] = hashPassword($data['password']);
        unset($data['password']);
        
        // Set default values
        $data['status'] = $data['status'] ?? 'aktif';
        $data['modul_kode'] = $data['modul_kode'] ?? 'user_management';
        
        try {
            $this->insert($data);
            
            // Log audit
            logAudit(
                $_SESSION['user_id'] ?? 'system',
                'CREATE',
                'user_management',
                'users',
                0,
                null,
                ['username' => $data['username'], 'role' => $data['role']]
            );
            
            return ['success' => true, 'message' => 'User berhasil dibuat'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Gagal membuat user: ' . $e->getMessage()];
        }
    }
    
    /**
     * Update user
     */
    public function updateUser($username, $data) {
        $oldData = $this->findByUsername($username);
        
        if (!$oldData) {
            return ['success' => false, 'message' => 'User tidak ditemukan'];
        }
        
        // Remove sensitive fields
        unset($data['username'], $data['password_hash'], $data['created_at']);
        
        // If password is being changed
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password_hash'] = hashPassword($data['password']);
            unset($data['password']);
        }
        
        try {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET " . 
                implode(', ', array_map(fn($k) => "$k = :$k", array_keys($data))) . 
                " WHERE {$this->primaryKey} = :username");
            
            $data['username'] = $username;
            $result = $stmt->execute($data);
            
            if ($result) {
                // Log audit
                logAudit(
                    $_SESSION['user_id'] ?? 'system',
                    'UPDATE',
                    'user_management',
                    'users',
                    0,
                    $oldData,
                    array_merge(['username' => $username], $data)
                );
                
                return ['success' => true, 'message' => 'User berhasil diupdate'];
            }
            
            return ['success' => false, 'message' => 'Gagal mengupdate user'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Delete user (soft delete by setting status to nonaktif)
     */
    public function deleteUser($username) {
        $user = $this->findByUsername($username);
        
        if (!$user) {
            return ['success' => false, 'message' => 'User tidak ditemukan'];
        }
        
        // Prevent deleting yourself
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $username) {
            return ['success' => false, 'message' => 'Tidak dapat menghapus akun Anda sendiri'];
        }
        
        try {
            return $this->update($username, ['status' => 'nonaktif']);
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get users by role
     */
    public function getByRole($role) {
        return $this->where('role = :role AND status = :status', [
            'role' => $role,
            'status' => 'aktif'
        ], 'nama ASC');
    }
    
    /**
     * Get all active users
     */
    public function getActiveUsers() {
        return $this->where('status = :status', ['status' => 'aktif'], 'role ASC, nama ASC');
    }
}
