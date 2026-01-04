<?php
require_once 'bootstrap.php';

try {
    $db = DatabaseManager::getInstance();
    
    echo "Setting up database...\n";
    
    // Create database and tables
    $sql = file_get_contents('database.sql');
    $statements = explode(';', $sql);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $db->getPDO()->exec($statement);
        }
    }
    
    echo "Database setup completed!\n";
    echo "Visit: http://localhost/gallery/\n";
    echo "Admin: http://localhost/gallery/admin (admin/admin123)\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>