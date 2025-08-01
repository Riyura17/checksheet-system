<?php
$host = 'localhost';
$dbname = 'checksheet_system';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Create tables if they don't exist
$queries = [
    "CREATE TABLE IF NOT EXISTS operators (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        employee_id VARCHAR(50) UNIQUE,
        position VARCHAR(50),
        department VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS subcategories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS checksheets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        operator_id INT,
        check_date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (operator_id) REFERENCES operators(id) ON DELETE SET NULL
    )",
    
    "CREATE TABLE IF NOT EXISTS check_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        checksheet_id INT,
        subcategory_id INT,
        status ENUM('yes', 'no') DEFAULT 'no',
        description TEXT,
        FOREIGN KEY (checksheet_id) REFERENCES checksheets(id) ON DELETE CASCADE,
        FOREIGN KEY (subcategory_id) REFERENCES subcategories(id) ON DELETE CASCADE
    )"
];

foreach ($queries as $query) {
    $pdo->exec($query);
}
?>