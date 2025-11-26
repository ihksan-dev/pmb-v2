<?php
class App 
{
    protected $controller = 'Home';
    protected $method = 'index';
    protected $params = [];

    public function __construct()
    {
        $url = $this->parseURL();
        
        if ($url !== null) {
            // Look for controller
            if (file_exists('../app/controllers/' . $url[0] . '.php')) {
                $this->controller = $url[0];
                unset($url[0]);
            }

            require_once '../app/controllers/' . $this->controller . '.php';
            $this->controller = new $this->controller;

            // Look for method
            if (isset($url[1])) {
                if (method_exists($this->controller, $url[1])) {
                    $this->method = $url[1];
                    unset($url[1]);
                }
            }

            // Get parameters
            $this->params = $url ? array_values($url) : [];
        } else {
            require_once '../app/controllers/' . $this->controller . '.php';
            $this->controller = new $this->controller;
        }

        // Run controller and method with parameters
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseURL()
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return null;
    }
}