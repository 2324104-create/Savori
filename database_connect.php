<?php
require_once __DIR__ . '/../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Create tables if not exists
$tables = [
    "CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100),
        role ENUM('user','admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS drinks (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        type VARCHAR(50),
        preparation_time INT DEFAULT 5,
        difficulty ENUM('Easy','Medium','Hard') DEFAULT 'Medium',
        caffeine_level ENUM('Low','Medium','High','Very High') DEFAULT 'Medium',
        popularity_score INT DEFAULT 50,
        is_available BOOLEAN DEFAULT TRUE
    )",
    
    "CREATE TABLE IF NOT EXISTS stock (
        id INT PRIMARY KEY AUTO_INCREMENT,
        drink_id INT,
        day DATE DEFAULT CURDATE(),
        available_quantity INT DEFAULT 20,
        FOREIGN KEY (drink_id) REFERENCES drinks(id)
    )",
    
    "CREATE TABLE IF NOT EXISTS recommendations (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT,
        drink_id INT,
        mood VARCHAR(50),
        weather VARCHAR(50),
        time_of_day VARCHAR(20),
        recommended_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (drink_id) REFERENCES drinks(id)
    )",
    
    "CREATE TABLE IF NOT EXISTS reviews (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT,
        drink_id INT,
        rating INT CHECK(rating BETWEEN 1 AND 5),
        comment TEXT,
        review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (drink_id) REFERENCES drinks(id)
    )",
    
    "CREATE TABLE IF NOT EXISTS favorites (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT,
        drink_id INT,
        added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (drink_id) REFERENCES drinks(id),
        UNIQUE KEY unique_fav (user_id, drink_id)
    )"
];

foreach ($tables as $sql) {
    try {
        $db->exec($sql);
    } catch (PDOException $e) {
        // Table already exists, continue
    }
}

// Insert default admin user if not exists
$checkAdmin = $db->query("SELECT COUNT(*) FROM users WHERE username = 'admin'")->fetchColumn();
if ($checkAdmin == 0) {
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $db->exec("INSERT INTO users (username, email, password, full_name, role) 
               VALUES ('admin', 'admin@savori.com', '$password', 'Admin Savori', 'admin')");
}

// Insert sample drinks if empty
$checkDrinks = $db->query("SELECT COUNT(*) FROM drinks")->fetchColumn();
if ($checkDrinks == 0) {
    $sample_drinks = [
        "('Espresso', 'Kopi pekat dengan rasa kuat', 'Espresso', 5, 'Medium', 'High', 95)",
        "('Cappuccino', 'Espresso dengan steamed milk dan foam', 'Cappuccino', 7, 'Medium', 'Medium', 90)",
        "('Latte', 'Espresso dengan banyak susu', 'Latte', 8, 'Easy', 'Medium', 92)",
        "('Americano', 'Espresso dengan air panas', 'Americano', 5, 'Easy', 'High', 85)",
        "('Mocha', 'Latte dengan coklat', 'Mocha', 10, 'Easy', 'Medium', 88)",
        "('Cold Brew', 'Kopi seduh dingin', 'Cold Brew', 1440, 'Easy', 'Medium', 82)",
        "('Macchiato', 'Espresso dengan sedikit susu', 'Macchiato', 6, 'Medium', 'High', 80)",
        "('Flat White', 'Seperti latte tapi lebih banyak kopi', 'Flat White', 8, 'Hard', 'High', 75)",
        "('Turkish Coffee', 'Kopi rebus khas Turki', 'Turkish', 15, 'Hard', 'Very High', 70)",
        "('Frappuccino', 'Kopi blended dengan es', 'Frappuccino', 10, 'Easy', 'Low', 87)"
    ];
    
    foreach ($sample_drinks as $drink) {
        $db->exec("INSERT INTO drinks (name, description, type, preparation_time, difficulty, caffeine_level, popularity_score) VALUES $drink");
    }
    
    // Insert stock for today
    $db->exec("INSERT INTO stock (drink_id, available_quantity) 
               SELECT id, 20 FROM drinks");
}
?>