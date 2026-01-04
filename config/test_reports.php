<?php
session_start();
$_SESSION['admin_logged_in'] = true; // Bypass auth for testing

define('APP_ROOT', __DIR__);
require_once 'bootstrap.php';

try {
    $controller = new AdminController();
    echo "AdminController created successfully\n";
    
    // Test the reports method
    ob_start();
    $controller->reports();
    $output = ob_get_clean();
    
    echo "Reports method executed successfully\n";
    echo "Output length: " . strlen($output) . " characters\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>