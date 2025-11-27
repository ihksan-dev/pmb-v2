<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pmb_syedzasaintika');

// Application Configuration
define('BASE_URL', 'http://localhost/pmb_syedzasaintika');
define('APP_NAME', 'PMB Syedza Saintika');

// Security Configuration
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_TIMEOUT', 1800); // 30 minutes

// File Upload Configuration
define('MAX_PHOTO_SIZE', 2 * 1024 * 1024); // 2MB
define('MAX_DOC_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_PHOTO_TYPES', ['jpg', 'jpeg', 'png']);
define('ALLOWED_DOC_TYPES', ['pdf']);

// Captcha Configuration
define('CAPTCHA_LENGTH', 6);
define('CAPTCHA_EXPIRE', 300); // 5 minutes in seconds

// Registration Number Configuration
define('REG_NUMBER_LENGTH', 8);