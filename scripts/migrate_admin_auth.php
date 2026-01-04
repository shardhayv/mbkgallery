<?php
// Simple migration script to update admin_users table

$host = 'localhost';
$dbname = 'maithili_gallery';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if columns already exist
    $result = $pdo->query("SHOW COLUMNS FROM admin_users LIKE 'last_login'");
    if ($result->rowCount() == 0) {
        echo "Adding security columns to admin_users table...\n";
        
        $pdo->exec("ALTER TABLE admin_users 
                   ADD COLUMN last_login timestamp NULL DEFAULT NULL,
                   ADD COLUMN failed_attempts int(11) DEFAULT 0,
                   ADD COLUMN locked_until timestamp NULL DEFAULT NULL,
                   ADD COLUMN updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()");
        
        echo "Security columns added successfully!\n";
    } else {
        echo "Security columns already exist.\n";
    }
    
    // Update default admin password if it's still the old hash
    $admin = $pdo->query("SELECT * FROM admin_users WHERE username = 'admin'")->fetch();
    if ($admin && $admin['password'] === '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi') {
        $newPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE username = 'admin'");
        $stmt->execute([$newPassword]);
        echo "Default admin password updated with proper hash.\n";
    }
    
    echo "Migration completed successfully!\n";
    
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
?>