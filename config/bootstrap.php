<?php
// Application Bootstrap
define('APP_ROOT', dirname(__DIR__));
define('APP_ENV', $_ENV['APP_ENV'] ?? 'development');
define('APP_DEBUG', APP_ENV === 'development');

// Autoloader
spl_autoload_register(function ($class) {
    $paths = [
        APP_ROOT . '/app/Controllers/',
        APP_ROOT . '/app/Models/',
        APP_ROOT . '/app/Services/',
        APP_ROOT . '/app/Middleware/',
        APP_ROOT . '/core/Database/',
        APP_ROOT . '/core/Security/',
        APP_ROOT . '/core/Error/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Initialize core components
require_once APP_ROOT . '/core/Error/ErrorHandler.php';
require_once APP_ROOT . '/core/Security/SecurityManager.php';
require_once APP_ROOT . '/core/Security/CSRFProtection.php';
require_once APP_ROOT . '/core/Database/DatabaseManager.php';

ErrorHandler::init();
SecurityManager::init();

// Application instance
class App {
    private static $instance;
    private $db;
    
    private function __construct() {
        $this->db = DatabaseManager::getInstance();
    }
    
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getDB() {
        return $this->db;
    }
    
    public function run() {
        $router = new Router();
        $router->dispatch();
    }
}

// Simple Router
class Router {
    private $routes = [];
    
    public function __construct() {
        $this->loadRoutes();
    }
    
    private function loadRoutes() {
        // Web routes
        $this->routes['GET']['/'] = 'HomeController@index';
        $this->routes['GET']['/search'] = 'SearchController@index';
        $this->routes['GET']['/artists'] = 'ArtistController@index';
        $this->routes['GET']['/artists/{id}/paintings'] = 'ArtistController@paintings';
        $this->routes['GET']['/category/{id}'] = 'CategoryController@paintings';
        $this->routes['GET']['/cart'] = 'CartController@index';
        $this->routes['GET']['/admin'] = 'AdminController@dashboard';
        $this->routes['GET']['/admin/login'] = 'AdminController@login';
        $this->routes['POST']['/admin/login'] = 'AdminController@authenticate';
        $this->routes['GET']['/admin/logout'] = 'AdminController@logout';
        
        // Admin management routes
        $this->routes['GET']['/admin/artists'] = 'AdminController@artists';
        $this->routes['POST']['/admin/artists'] = 'AdminController@createArtist';
        $this->routes['PUT']['/admin/artists/{id}'] = 'AdminController@updateArtist';
        $this->routes['DELETE']['/admin/artists/{id}'] = 'AdminController@deleteArtist';
        
        $this->routes['GET']['/admin/paintings'] = 'AdminController@paintings';
        $this->routes['POST']['/admin/paintings'] = 'AdminController@createPainting';
        $this->routes['PUT']['/admin/paintings/{id}'] = 'AdminController@updatePainting';
        $this->routes['DELETE']['/admin/paintings/{id}'] = 'AdminController@deletePainting';
        
        $this->routes['GET']['/admin/orders'] = 'AdminController@orders';
        $this->routes['POST']['/admin/orders/{id}/status'] = 'AdminController@updateOrderStatus';
        
        $this->routes['GET']['/admin/reports'] = 'AdminController@reports';
        $this->routes['GET']['/admin/settings'] = 'AdminController@settings';
        $this->routes['POST']['/admin/settings'] = 'AdminController@updateSettings';
        
        // Admin user management routes
        $this->routes['GET']['/admin/users'] = 'AdminController@users';
        $this->routes['POST']['/admin/users/create'] = 'AdminController@createUser';
        $this->routes['POST']['/admin/users/{id}/reset-password'] = 'AdminController@resetUserPassword';
        $this->routes['POST']['/admin/users/{id}/unlock'] = 'AdminController@unlockUser';
        $this->routes['DELETE']['/admin/users/{id}'] = 'AdminController@deleteUser';
        
        // API routes
        $this->routes['GET']['/api/search'] = 'SearchController@api';
        $this->routes['GET']['/api/paintings'] = 'API\\PaintingController@index';
        $this->routes['GET']['/api/artists'] = 'API\\ArtistController@index';
        $this->routes['GET']['/api/categories'] = 'API\\CategoryController@index';
        $this->routes['POST']['/api/cart/items'] = 'API\\CartController@items';
        $this->routes['POST']['/api/orders'] = 'API\\OrderController@create';
        $this->routes['GET']['/api/artists/{id}/paintings'] = 'API\\ArtistController@paintings';
    }
    
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = str_replace('/gallery', '', $path);
        
        // Handle HTTP method override
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }
        
        // Remove trailing slash except for root
        if ($path !== '/' && substr($path, -1) === '/') {
            $path = rtrim($path, '/');
        }
        
        if (isset($this->routes[$method][$path])) {
            $this->callController($this->routes[$method][$path]);
        } else {
            // Check for dynamic routes
            foreach ($this->routes[$method] as $route => $controller) {
                if (preg_match($this->routeToRegex($route), $path, $matches)) {
                    array_shift($matches);
                    $this->callController($controller, $matches);
                    return;
                }
            }
            http_response_code(404);
            require APP_ROOT . '/app/Views/errors/404.php';
        }
    }
    
    private function routeToRegex($route) {
        return '#^' . preg_replace('/\{[^}]+\}/', '([^/]+)', $route) . '$#';
    }
    
    private function callController($controllerAction, $params = []) {
        list($controller, $action) = explode('@', $controllerAction);
        
        if (strpos($controller, 'API\\') === 0) {
            $controllerClass = $controller;
        } else {
            $controllerClass = $controller;
        }
        
        $controllerInstance = new $controllerClass();
        call_user_func_array([$controllerInstance, $action], $params);
    }
}
?>