-- IMS Full Database Schema
-- Use this for fresh installation

-- 1. Categories Table
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Items Table
CREATE TABLE IF NOT EXISTS `items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `country` VARCHAR(100),
    `photo` VARCHAR(255),
    `remarks` TEXT,
    `quantity` INT DEFAULT 0,
    `category_id` INT,
    `low_stock_threshold` INT DEFAULT 10,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (`category_id`),
    INDEX (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Users Table
CREATE TABLE IF NOT EXISTS `newaccountregistration` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255),
    `role` ENUM('user','admin') DEFAULT 'user',
    `status` ENUM('pending','active','suspended') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Purchase Table
CREATE TABLE IF NOT EXISTS `purchase` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `vendor` VARCHAR(255) NOT NULL,
    `date` DATE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Bill Table
CREATE TABLE IF NOT EXISTS `bill` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `vendor_id` INT,
    `item_id` INT,
    `itemname` VARCHAR(255),
    `quantity` INT,
    `price` DECIMAL(10,2),
    INDEX (`vendor_id`),
    INDEX (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add default admin user (password: admin123)
-- You should change this after first login
INSERT IGNORE INTO `newaccountregistration` (username, password_hash, role, status) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active');
