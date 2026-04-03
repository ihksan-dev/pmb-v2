<?php
/**
 * Main Entry Point
 * SIAKAD Universitas ABC
 */

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Define base path
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');

// Autoloader
spl_autoload_register(function ($class) {
    // Core classes
    $coreFile = APP_PATH . '/core/' . $class . '.php';
    if (file_exists($coreFile)) {
        require_once $coreFile;
        return;
    }
    
    // Models
    $modelFile = APP_PATH . '/models/' . $class . '.php';
    if (file_exists($modelFile)) {
        require_once $modelFile;
        return;
    }
    
    // Controllers
    $controllerFile = APP_PATH . '/controllers/' . $class . '.php';
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        return;
    }
});

// Load helper functions
require_once APP_PATH . '/helpers/security.php';

// Load configuration
$config = require APP_PATH . '/config/app.php';

// Initialize router
$router = new Router();

// Define routes
// Auth routes
$router->get('/auth/login', 'auth@login');
$router->post('/auth/login', 'auth@authenticate');
$router->get('/auth/logout', 'auth@logout');

// Dashboard
$router->get('/dashboard', 'dashboard@index');
$router->get('/', 'dashboard@index');

// User Management (Admin only)
$router->get('/users', 'users@index');
$router->get('/users/create', 'users@create');
$router->post('/users/store', 'users@store');
$router->get('/users/edit/{id}', 'users@edit');
$router->post('/users/update/{id}', 'users@update');
$router->post('/users/delete/{id}', 'users@delete');

// Dispatch request
try {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];
    
    $router->dispatch($uri, $method);
} catch (Exception $e) {
    http_response_code(500);
    echo "<h1>Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    if (ini_get('display_errors')) {
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
}
