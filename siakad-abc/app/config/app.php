<?php
/**
 * Application Configuration
 * SIAKAD Universitas ABC
 */

return [
    'app_name' => 'SIAKAD Universitas ABC',
    'base_url' => getenv('APP_URL') ?: 'http://localhost/siakad-abc/public',
    'timezone' => 'Asia/Jakarta',
    'session_lifetime' => 120, // minutes
    
    // Security
    'encryption_key' => getenv('ENCRYPTION_KEY') ?: 'your-secret-key-change-in-production',
    'password_cost' => 12, // bcrypt cost
    
    // Upload settings
    'upload_path' => dirname(__DIR__, 2) . '/public/uploads/',
    'allowed_extensions' => ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'],
    'max_file_size' => 5242880, // 5MB
    
    // Pagination
    'per_page' => 20,
];
