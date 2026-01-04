-- Maithili Bikash Kosh Gallery Database
-- Complete database with sample data for testing

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Create database
CREATE DATABASE IF NOT EXISTS `maithili_gallery` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `maithili_gallery`;

-- --------------------------------------------------------

-- Table structure for table `admin_users`
CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `failed_attempts` int(11) DEFAULT 0,
  `locked_until` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table `admin_users`
INSERT INTO `admin_users` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-01-01 10:00:00');

-- --------------------------------------------------------

-- Table structure for table `artists`
CREATE TABLE `artists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table `artists`
INSERT INTO `artists` (`id`, `name`, `email`, `phone`, `address`, `bio`, `profile_image`, `status`, `created_at`) VALUES
(1, 'Rajesh Kumar Jha', 'rajesh@example.com', '9876543210', 'Madhubani, Bihar', 'Master artist specializing in traditional Mithila paintings with over 20 years of experience.', NULL, 'active', '2024-01-01 10:00:00'),
(2, 'Sunita Devi', 'sunita@example.com', '9876543211', 'Darbhanga, Bihar', 'Renowned female artist known for intricate Kohbar and Bharni style paintings.', NULL, 'active', '2024-01-02 10:00:00'),
(3, 'Amit Singh', 'amit@example.com', '9876543212', 'Samastipur, Bihar', 'Contemporary Mithila artist blending traditional techniques with modern themes.', NULL, 'active', '2024-01-03 10:00:00'),
(4, 'Kavita Sharma', 'kavita@example.com', '9876543213', 'Sitamarhi, Bihar', 'Expert in Godna and Tantrik style Mithila art forms.', NULL, 'active', '2024-01-04 10:00:00');

-- --------------------------------------------------------

-- Table structure for table `categories`
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table `categories`
INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Traditional Mithila', 'Traditional Mithila art with cultural and mythological themes', '2024-01-01 10:00:00'),
(2, 'Modern Mithila', 'Contemporary interpretations of Mithila art with modern themes', '2024-01-01 10:00:00'),
(3, 'Religious', 'Religious and spiritual themed paintings depicting Hindu deities', '2024-01-01 10:00:00'),
(4, 'Nature', 'Nature and wildlife themed paintings with traditional motifs', '2024-01-01 10:00:00'),
(5, 'Kohbar', 'Traditional wedding chamber art with fertility symbols', '2024-01-01 10:00:00'),
(6, 'Bharni', 'Filled paintings with vibrant colors and intricate patterns', '2024-01-01 10:00:00');

-- --------------------------------------------------------

-- Table structure for table `paintings`
CREATE TABLE `paintings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `artist_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `dimensions` varchar(50) DEFAULT NULL,
  `medium` varchar(100) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `status` enum('available','sold','reserved') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `artist_id` (`artist_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `paintings_ibfk_1` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`),
  CONSTRAINT `paintings_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table `paintings`
INSERT INTO `paintings` (`id`, `title`, `artist_id`, `category_id`, `description`, `price`, `dimensions`, `medium`, `image_path`, `status`, `created_at`) VALUES
(1, 'Radha Krishna in Vrindavan', 1, 3, 'Beautiful depiction of Radha and Krishna in the gardens of Vrindavan with traditional Mithila motifs', 15000.00, '24x18 inches', 'Acrylic on Canvas', NULL, 'available', '2024-01-05 10:00:00'),
(2, 'Peacock Dance', 2, 4, 'Vibrant painting of peacocks dancing in monsoon with traditional geometric patterns', 12000.00, '20x16 inches', 'Natural Colors on Handmade Paper', NULL, 'available', '2024-01-06 10:00:00'),
(3, 'Kohbar Ghar', 2, 5, 'Traditional wedding chamber art with fertility symbols and auspicious motifs', 18000.00, '30x24 inches', 'Natural Pigments on Canvas', NULL, 'sold', '2024-01-07 10:00:00'),
(4, 'Durga Maa', 1, 3, 'Powerful depiction of Goddess Durga in traditional Mithila style', 20000.00, '36x24 inches', 'Acrylic on Canvas', NULL, 'available', '2024-01-08 10:00:00'),
(5, 'Fish and Lotus', 3, 1, 'Traditional Mithila painting featuring fish and lotus symbols of prosperity', 8000.00, '16x12 inches', 'Watercolor on Paper', NULL, 'available', '2024-01-09 10:00:00'),
(6, 'Modern Tree of Life', 3, 2, 'Contemporary interpretation of the tree of life with traditional patterns', 14000.00, '24x20 inches', 'Mixed Media on Canvas', NULL, 'available', '2024-01-10 10:00:00'),
(7, 'Ganesha Blessing', 4, 3, 'Lord Ganesha in traditional Mithila art style with intricate borders', 16000.00, '20x20 inches', 'Natural Colors on Silk', NULL, 'reserved', '2024-01-11 10:00:00'),
(8, 'Village Life', 1, 1, 'Depicting rural life in Mithila region with traditional activities', 10000.00, '18x14 inches', 'Acrylic on Canvas', NULL, 'available', '2024-01-12 10:00:00'),
(9, 'Bharni Style Mandala', 2, 6, 'Intricate mandala design in traditional Bharni style with vibrant colors', 13000.00, '22x22 inches', 'Natural Pigments on Paper', NULL, 'available', '2024-01-13 10:00:00'),
(10, 'Sun and Moon', 4, 1, 'Celestial bodies depicted in traditional Mithila geometric patterns', 11000.00, '20x16 inches', 'Acrylic on Canvas', NULL, 'sold', '2024-01-14 10:00:00');

-- --------------------------------------------------------

-- Table structure for table `orders`
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_address` text DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','shipped','delivered','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table `orders`
INSERT INTO `orders` (`id`, `customer_name`, `customer_email`, `customer_phone`, `customer_address`, `total_amount`, `status`, `created_at`) VALUES
(1, 'Priya Sharma', 'priya@example.com', '9876543220', '123 MG Road, Delhi', 18000.00, 'delivered', '2024-01-15 10:00:00'),
(2, 'Rahul Gupta', 'rahul@example.com', '9876543221', '456 Park Street, Kolkata', 15000.00, 'shipped', '2024-01-16 11:00:00'),
(3, 'Anjali Singh', 'anjali@example.com', '9876543222', '789 Brigade Road, Bangalore', 11000.00, 'delivered', '2024-01-17 12:00:00'),
(4, 'Vikash Kumar', 'vikash@example.com', '9876543223', '321 FC Road, Pune', 12000.00, 'confirmed', '2024-01-18 13:00:00'),
(5, 'Meera Joshi', 'meera@example.com', '9876543224', '654 Linking Road, Mumbai', 8000.00, 'pending', '2024-01-19 14:00:00'),
(6, 'Suresh Yadav', 'suresh@example.com', '9876543225', '987 Anna Salai, Chennai', 14000.00, 'confirmed', '2024-01-20 15:00:00'),
(7, 'Kavya Reddy', 'kavya@example.com', '9876543226', '147 Banjara Hills, Hyderabad', 16000.00, 'shipped', '2024-01-21 16:00:00'),
(8, 'Arjun Patel', 'arjun@example.com', '9876543227', '258 CG Road, Ahmedabad', 10000.00, 'delivered', '2024-01-22 17:00:00');

-- --------------------------------------------------------

-- Table structure for table `order_items`
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `painting_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `painting_id` (`painting_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`painting_id`) REFERENCES `paintings` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table `order_items`
INSERT INTO `order_items` (`id`, `order_id`, `painting_id`, `price`) VALUES
(1, 1, 3, 18000.00),
(2, 2, 1, 15000.00),
(3, 3, 10, 11000.00),
(4, 4, 2, 12000.00),
(5, 5, 5, 8000.00),
(6, 6, 6, 14000.00),
(7, 7, 7, 16000.00),
(8, 8, 8, 10000.00);

-- --------------------------------------------------------

-- Auto increment values
ALTER TABLE `admin_users` AUTO_INCREMENT=2;
ALTER TABLE `artists` AUTO_INCREMENT=5;
ALTER TABLE `categories` AUTO_INCREMENT=7;
ALTER TABLE `paintings` AUTO_INCREMENT=11;
ALTER TABLE `orders` AUTO_INCREMENT=9;
ALTER TABLE `order_items` AUTO_INCREMENT=9;

COMMIT;