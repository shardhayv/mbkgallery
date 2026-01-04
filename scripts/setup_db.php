<?php
// Database Setup Script
define('APP_ROOT', __DIR__);

// Database configuration
$config = [
    'host' => 'localhost',
    'database' => 'maithili_gallery',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];

try {
    // Connect to MySQL server (without database)
    $pdo = new PDO("mysql:host={$config['host']};charset={$config['charset']}", 
                   $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS {$config['database']}");
    $pdo->exec("USE {$config['database']}");
    
    echo "Database created/connected successfully.\n";
    
    // Read and execute SQL file
    $sql = file_get_contents(__DIR__ . '/database.sql');
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^(CREATE DATABASE|USE)/i', $statement)) {
            try {
                $pdo->exec($statement);
            } catch (PDOException $e) {
                // Ignore table exists errors
                if (strpos($e->getMessage(), 'already exists') === false) {
                    echo "Warning: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    echo "Database setup completed successfully.\n";
    
    // Test queries for reports
    echo "\nTesting report queries:\n";
    
    // Test basic counts
    $artistCount = $pdo->query("SELECT COUNT(*) FROM artists WHERE status = 'active'")->fetchColumn();
    echo "Active artists: $artistCount\n";
    
    $paintingCount = $pdo->query("SELECT COUNT(*) FROM paintings WHERE status = 'available'")->fetchColumn();
    echo "Available paintings: $paintingCount\n";
    
    $orderCount = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    echo "Total orders: $orderCount\n";
    
    // Test revenue query
    $revenue = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status IN ('confirmed', 'shipped', 'delivered')")->fetchColumn();
    echo "Total revenue: ₹" . number_format($revenue, 2) . "\n";
    
    echo "\nDatabase is ready for reports!\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    exit(1);
}
?>