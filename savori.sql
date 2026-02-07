-- File: database/savori_simple.sql
-- Jalankan di phpMyAdmin

CREATE DATABASE IF NOT EXISTS savori_db;
USE savori_db;

-- Simple tables
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    role ENUM('user','admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE drinks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    type VARCHAR(50),
    preparation_time INT DEFAULT 5,
    difficulty ENUM('Easy','Medium','Hard') DEFAULT 'Medium',
    caffeine_level ENUM('Low','Medium','High','Very High') DEFAULT 'Medium',
    popularity_score INT DEFAULT 50,
    is_available BOOLEAN DEFAULT TRUE
);

CREATE TABLE stock (
    id INT PRIMARY KEY AUTO_INCREMENT,
    drink_id INT,
    day DATE DEFAULT CURDATE(),
    available_quantity INT DEFAULT 20,
    FOREIGN KEY (drink_id) REFERENCES drinks(id)
);

-- Insert admin user (password: admin123)
INSERT INTO users (username, email, password, full_name, role) 
VALUES ('admin', 'admin@savori.com', '$2y$10$7rLSvRVyTQORapkDOqmkhetjF6H9lJHngr4hJMSM2lHObJbW5EQh6', 'Admin Savori', 'admin');

-- Insert sample drinks
INSERT INTO drinks (name, description, type, preparation_time, difficulty, caffeine_level, popularity_score) VALUES
('Espresso', 'Kopi pekat dengan rasa kuat', 'Espresso', 5, 'Medium', 'High', 95),
('Cappuccino', 'Espresso dengan steamed milk dan foam', 'Cappuccino', 7, 'Medium', 'Medium', 90),
('Latte', 'Espresso dengan banyak susu', 'Latte', 8, 'Easy', 'Medium', 92),
('Americano', 'Espresso dengan air panas', 'Americano', 5, 'Easy', 'High', 85),
('Mocha', 'Latte dengan coklat', 'Mocha', 10, 'Easy', 'Medium', 88),
('Cold Brew', 'Kopi seduh dingin', 'Cold Brew', 1440, 'Easy', 'Medium', 82),
('Macchiato', 'Espresso dengan sedikit susu', 'Macchiato', 6, 'Medium', 'High', 80),
('Flat White', 'Seperti latte tapi lebih banyak kopi', 'Flat White', 8, 'Hard', 'High', 75),
('Turkish Coffee', 'Kopi rebus khas Turki', 'Turkish', 15, 'Hard', 'Very High', 70),
('Frappuccino', 'Kopi blended dengan es', 'Frappuccino', 10, 'Easy', 'Low', 87);

-- Insert stock
INSERT INTO stock (drink_id, available_quantity) 
SELECT id, 20 FROM drinks;