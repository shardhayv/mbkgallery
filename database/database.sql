-- Maithili Bikash Kosh Gallery Database
CREATE DATABASE IF NOT EXISTS maithili_gallery;
USE maithili_gallery;

-- Admin users table
CREATE TABLE admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Artists table
CREATE TABLE artists (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    bio TEXT,
    profile_image VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Paintings table
CREATE TABLE paintings (
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
);

-- Orders table
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20),
    customer_address TEXT,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Order items table
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    painting_id INT,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (painting_id) REFERENCES paintings(id)
);

-- Insert sample categories
INSERT INTO categories (name, description) VALUES
('Traditional Mithila', 'Traditional Mithila art with cultural themes'),
('Modern Mithila', 'Contemporary interpretations of Mithila art'),
('Religious', 'Religious and spiritual themed paintings'),
('Nature', 'Nature and wildlife themed paintings'),
('Handcrafts', 'Other handcrafted items');

-- Insert sample artist
INSERT INTO artists (name, email, phone, bio) VALUES
('Sample Artist', 'artist@example.com', '9876543210', 'Experienced Mithila artist from Maithili Bikash Kosh');

-- Insert default admin user (password: admin123)
INSERT INTO admin_users (username, password) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');