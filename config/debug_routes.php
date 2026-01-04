<?php
// Debug routing
define('APP_ROOT', __DIR__);
require_once 'bootstrap.php';

echo "Testing route: /admin/reports\n";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n";

$path = parse_url('/gallery/admin/reports', PHP_URL_PATH);
$path = str_replace('/gallery', '', $path);
echo "Processed path: " . $path . "\n";

$router = new Router();
$reflection = new ReflectionClass($router);
$routesProperty = $reflection->getProperty('routes');
$routesProperty->setAccessible(true);
$routes = $routesProperty->getValue($router);

echo "Available GET routes:\n";
foreach ($routes['GET'] as $route => $controller) {
    echo "  $route => $controller\n";
}
?>