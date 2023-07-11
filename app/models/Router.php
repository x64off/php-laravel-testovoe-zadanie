<?php
class Router
{
    protected static $controller;
    protected static $method;
    protected static $params;
    protected static $routes    =  [];
    private static $instance;
    public function __construct()
    {
        
        self::route();
        
    }
    public static function addRoute($route, $controller, $method)
    {
        self::$routes[$route] = ['controller' => $controller, 'method' => $method];
    }
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
            
        }
        return self::$instance;
    }
    public static function getParams(){
        return self::$params;
    }
    public function route()
    {
        csrf::getInstance();
        $url = $_SERVER['REQUEST_URI'];
        Render::addData("site_url",System::getConfig('system')['site_url']);
        $url = str_replace(System::getConfig('system')['site_url'],'',$url);
        
        $url = trim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        
        foreach (System::getConfig('routes') as $key => $value) {
            self::addRoute($key,$value[0],$value[1]);
        }
        foreach (self::$routes as $route => $info) {
            
            if (self::matchRoute($route, $url, $info['controller'], $info['method'])) {
                return;
            }
        }
        System::showError('404 not found.');
    }
    private static function getPostParams(){
        $params = [];
        if (isset($_POST['csrf_token'])){
            if(!csrf::verify($_POST['csrf_token'])) return [];
            unset($_POST['csrf_token']);
        }else return [];
        foreach ($_POST as $key => $value) {
            $params[$key]= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
        return $params;
    }
    protected static function matchRoute($route, $url, $controller, $method)
    {
        $routeParts = explode('/', $route);
        $urlParts = explode('/', $url);
        if (count($routeParts) !== count($urlParts)) {
            return false;
        }
        $params = ["get"=>[],"post"=>[]];
        for ($i = 0; $i < count($routeParts); $i++) {
            $routePart = $routeParts[$i];
            $urlPart = $urlParts[$i];
            
            if (strpos($routePart, '{') !== false && strpos($routePart, '}') !== false) {
                $paramName = trim($routePart, '{}');
                $params['get'][$paramName] = htmlspecialchars($urlPart, ENT_QUOTES, 'UTF-8');
            } elseif ($routePart !== $urlPart) {
                return false;
            }
        }
        $params['post'] = self::getPostParams();
        if (class_exists($controller)) {
            $controllerInstance = new $controller();
            call_user_func_array([$controllerInstance, $method], $params);
            return true;
        } else {
            return false;
        }
    }
}
