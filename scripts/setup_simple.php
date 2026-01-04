<?php
// Simple Database Setup using mysqli
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'maithili_gallery';

// Connect to MySQL
$conn = new mysqli($host, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$conn->query("CREATE DATABASE IF NOT EXISTS $database");
$conn->select_db($database);

echo "Database setup started...\n";

// Create tables
$tables = [
    "CREATE TABLE IF NOT EXISTS artists (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100),
        phone VARCHAR(20),
        address TEXT,
        bio TEXT,
        profile_image VARCHAR(255),
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS categories (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(50) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS paintings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(200) NOT NULL,
        artist_id INT,
        category_id INT,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        dimensions VARCHAR(50),
        medium VARCHAR(100),
        image_path VARCHAR(255),
        status ENUM('available', 'sold', 'reserved') DEFAULT 'available',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (artist_id) REFERENCES artists(id),
        FOREIGN KEY (category_id) REFERENCES categories(id)
    )",
    
    "CREATE TABLE IF NOT EXISTS orders (
        id INT PRIMARY KEY AUTO_INCREMENT,
        customer_name VARCHAR(100) NOT NULL,
        customer_email VARCHAR(100) NOT NULL,
        customer_phone VARCHAR(20),
        customer_address TEXT,
        total_amount DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS order_items (
        id INT PRIMARY KEY AUTO_INCREMENT,
        order_id INT,
        painting_id INT,
        price DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id),
        FOREIGN KEY (painting_id) REFERENCES paintings(id)
    )"
];

foreach ($tables as $sql) {
    if ($conn->query($sql)) {
        echo "Table created successfully\n";
    } else {
        echo "Error: " . $conn->error . "\n";
    }
}

// Insert sample data
$sampleData = [
    "INSERT IGNORE INTO categories (id, name, description) VALUES 
        (1, 'Traditional Mithila', 'Traditional Mithila art'),
        (2, 'Modern Mithila', 'Contemporary interpretations'),
        (3, 'Religious', 'Religious themed paintings')",
        
    "INSERT IGNORE INTO artists (id, name, email, phone, bio, status) VALUES 
        (1, 'Sample Artist', 'artist@example.com', '9876543210', 'Experienced Mithila artist', 'active')",
        
    "INSERT IGNORE INTO paintings (title, artist_id, category_id, price, status) VALUES 
        ('Sample Painting 1', 1, 1, 5000.00, 'available'),
        ('Sample Painting 2', 1, 2, 7500.00, 'available'),
        ('Sample Painting 3', 1, 1, 6000.00, 'sold')",
        
    "INSERT IGNORE INTO orders (customer_name, customer_email, total_amount, status) VALUES 
        ('John Doe', 'john@example.com', 6000.00, 'delivered'),
        ('Jane Smith', 'jane@example.com', 5000.00, 'pending')",
        
    "INSERT IGNORE INTO order_items (order_id, painting_id, price) VALUES 
        (1, 3, 6000.00),
        (2, 1, 5000.00)"
];

foreach ($sampleData as $sql) {
    if ($conn->query($sql)) {
        echo "Sample data inserted\n";
    } else {
        echo "Error inserting data: " . $conn->error . "\n";
    }
}

echo "Database setup completed!\n";
$conn->close();
?>