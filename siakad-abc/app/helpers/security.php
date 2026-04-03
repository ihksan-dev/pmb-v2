<?php
/**
 * Security Helper Functions
 * Fungsi-fungsi keamanan untuk aplikasi SIAKAD
 */

/**
 * Hash password menggunakan bcrypt
 */
function hashPassword($password) {
    $config = require __DIR__ . '/../config/app.php';
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => $config['password_cost']]);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Sanitize input dari XSS
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 */
function generateCsrfToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCsrfToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get CSRF token field for forms
 */
function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . generateCsrfToken() . '">';
}

/**
 * Redirect with flash message
 */
function redirectWithFlash($url, $type, $message) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
    header("Location: {$url}");
    exit();
}

/**
 * Get and clear flash message
 */
function getFlash() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Check if user has access to module
 */
function hasModuleAccess($modulKode, $role) {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->prepare("SELECT allowed_roles, is_aktif FROM akses_modul WHERE modul_kode = :modul_kode LIMIT 1");
    $stmt->execute(['modul_kode' => $modulKode]);
    $modul = $stmt->fetch();
    
    if (!$modul || !$modul['is_aktif']) {
        return false;
    }
    
    $allowedRoles = json_decode($modul['allowed_roles'], true);
    return in_array($role, $allowedRoles);
}

/**
 * Log audit trail
 */
function logAudit($userIdentifier, $action, $modulKode, $tableName, $recordId, $oldData = null, $newData = null) {
    $db = Database::getInstance()->getConnection();
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
    
    $sql = "INSERT INTO audit_log (user_identifier, action, modul_kode, table_name, record_id, old_data, new_data, ip_address) 
            VALUES (:user_identifier, :action, :modul_kode, :table_name, :record_id, :old_data, :new_data, :ip_address)";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        'user_identifier' => $userIdentifier,
        'action' => $action,
        'modul_kode' => $modulKode,
        'table_name' => $tableName,
        'record_id' => $recordId,
        'old_data' => $oldData ? json_encode($oldData) : null,
        'new_data' => $newData ? json_encode($newData) : null,
        'ip_address' => $ipAddress
    ]);
}

/**
 * Validate file upload
 */
function validateFileUpload($file, $allowedExtensions = null, $maxSize = null) {
    $config = require __DIR__ . '/../config/app.php';
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['valid' => false, 'message' => 'Upload error occurred'];
    }
    
    $allowedExt = $allowedExtensions ?? $config['allowed_extensions'];
    $maxFileSize = $maxSize ?? $config['max_file_size'];
    
    // Check file size
    if ($file['size'] > $maxFileSize) {
        return ['valid' => false, 'message' => 'File size exceeds maximum allowed'];
    }
    
    // Check file extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt)) {
        return ['valid' => false, 'message' => 'File type not allowed'];
    }
    
    // Check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowedMimes = [
        'pdf' => 'application/pdf',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];
    
    $validMime = false;
    foreach ($allowedExt as $ext) {
        if (isset($allowedMimes[$ext]) && $allowedMimes[$ext] === $mimeType) {
            $validMime = true;
            break;
        }
    }
    
    if (!$validMime) {
        return ['valid' => false, 'message' => 'Invalid file type'];
    }
    
    return ['valid' => true, 'message' => 'File is valid'];
}

/**
 * Upload file dengan nama aman
 */
function uploadFile($file, $subDir = '') {
    $config = require __DIR__ . '/../config/app.php';
    
    $validation = validateFileUpload($file);
    if (!$validation['valid']) {
        return ['success' => false, 'message' => $validation['message']];
    }
    
    $uploadPath = $config['upload_path'];
    if ($subDir) {
        $uploadPath .= rtrim($subDir, '/') . '/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
    }
    
    // Generate safe filename
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $safeName = uniqid() . '_' . time() . '.' . $ext;
    $destination = $uploadPath . $safeName;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return [
            'success' => true,
            'filename' => $safeName,
            'path' => $subDir . $safeName,
            'url' => $config['base_url'] . '/uploads/' . $subDir . $safeName
        ];
    }
    
    return ['success' => false, 'message' => 'Failed to move uploaded file'];
}

/**
 * Prevent SQL injection - use prepared statements instead
 */
function escapeString($str) {
    $db = Database::getInstance()->getConnection();
    return $db->quote($str);
}

/**
 * Rate limiting helper
 */
function checkRateLimit($identifier, $limit = 5, $timeWindow = 300) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $key = 'rate_limit_' . md5($identifier);
    $now = time();
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 1, 'reset' => $now + $timeWindow];
        return true;
    }
    
    if ($now > $_SESSION[$key]['reset']) {
        $_SESSION[$key] = ['count' => 1, 'reset' => $now + $timeWindow];
        return true;
    }
    
    if ($_SESSION[$key]['count'] >= $limit) {
        return false;
    }
    
    $_SESSION[$key]['count']++;
    return true;
}
