<?php
/**
 * Router Class
 * Simple routing untuk aplikasi MVC
 */

class Router {
    protected $routes = [];
    
    /**
     * Register GET route
     */
    public function get($path, $controller) {
        $this->routes['GET'][$path] = $controller;
    }
    
    /**
     * Register POST route
     */
    public function post($path, $controller) {
        $this->routes['POST'][$path] = $controller;
    }
    
    /**
     * Dispatch request to controller
     */
    public function dispatch($uri, $method) {
        // Remove base path and query string
        $basePath = parse_url($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME);
        $uri = str_replace($basePath, '', $uri);
        $uri = strtok($uri, '?');
        
        // Normalize URI
        $uri = '/' . trim($uri, '/');
        
        if (!isset($this->routes[$method][$uri])) {
            http_response_code(404);
            die("404 - Page not found");
        }
        
        $controllerAction = $this->routes[$method][$uri];
        list($controllerName, $action) = explode('@', $controllerAction);
        
        // Build controller class name
        $controllerClass = ucfirst($controllerName) . 'Controller';
        $controllerFile = __DIR__ . '/../controllers/' . $controllerClass . '.php';
        
        if (!file_exists($controllerFile)) {
            throw new Exception("Controller {$controllerClass} not found");
        }
        
        require_once $controllerFile;
        
        $controller = new $controllerClass();
        
        if (!method_exists($controller, $action)) {
            throw new Exception("Method {$action} not found in {$controllerClass}");
        }
        
        call_user_func_array([$controller, $action], []);
    }
    
    /**
     * Get all routes (for debugging)
     */
    public function getRoutes() {
        return $this->routes;
    }
}
