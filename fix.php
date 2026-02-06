<?php
/**
 * FIX SCRIPT untuk Savori
 * Akses: http://localhost/Savori/fix.php
 */

echo "<!DOCTYPE html>";
echo "<html><head><title>Fix Savori</title>";
echo "<style>";
echo "body { font-family: Arial; margin: 40px; background: #f5f5f5; }";
echo ".success { color: green; padding: 10px; background: #d4edda; margin: 5px 0; }";
echo ".error { color: red; padding: 10px; background: #f8d7da; margin: 5px 0; }";
echo ".warning { color: orange; padding: 10px; background: #fff3cd; margin: 5px 0; }";
echo ".box { background: white; padding: 20px; border-radius: 10px; margin: 20px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
echo "</style>";
echo "</head><body>";
echo "<h1>🔧 Savori Fix Script</h1>";
echo "<div class='box'>";

// 1. Cek direktori
echo "<h2>1. Cek Struktur Direktori</h2>";
$required_dirs = [
    'config',
    'includes', 
    'pages',
    'assets/css',
    'assets/js',
    'assets/images',
    'api',
    'models',
    'database'
];

foreach ($required_dirs as $dir) {
    $path = __DIR__ . '/' . $dir;
    if (!is_dir($path)) {
        if (mkdir($path, 0777, true)) {
            echo "<div class='success'>✅ Created directory: $dir</div>";
        } else {
            echo "<div class='error'>❌ Failed to create: $dir</div>";
        }
    } else {
        echo "<div class='success'>✅ Directory exists: $dir</div>";
    }
}

// 2. Cek file penting
echo "<h2>2. Cek File Penting</h2>";
$required_files = [
    'config/config.php' => "<?php
// Konfigurasi Savori
session_start();
define('BASE_URL', 'http://localhost/Savori/');
define('SITE_NAME', 'Savori');
define('SITE_DESC', 'Coffee Recommendation App');
date_default_timezone_set('Asia/Jakarta');
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>",

    'config/database.php' => "<?php
class Database {
    private \$host = 'localhost';
    private \$db_name = 'savori_db';
    private \$username = 'root';
    private \$password = '';
    public \$conn;

    public function getConnection() {
        \$this->conn = null;
        try {
            \$this->conn = new PDO(
                'mysql:host=' . \$this->host . ';dbname=' . \$this->db_name,
                \$this->username,
                \$this->password
            );
            \$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            \$this->conn->exec('set names utf8');
        } catch(PDOException \$exception) {
            echo 'Connection error: ' . \$exception->getMessage();
        }
        return \$this->conn;
    }
}
?>",

    'includes/database_connect.php' => "<?php
// Koneksi Database
require_once __DIR__ . '/../config/database.php';

\$database = new Database();
\$db = \$database->getConnection();

if (!\$db) {
    die('Database connection failed!');
}

\$GLOBALS['db'] = \$db;
?>",

    'includes/header.php' => "<!-- Header file -->",

    'includes/footer.php' => "<!-- Footer file -->",

    'index.php' => "<?php
// Main entry point
require_once 'config/config.php';
require_once 'includes/database_connect.php';

// Simple routing
\$page = isset(\$_GET['page']) ? \$_GET['page'] : 'home';
\$page = preg_replace('/[^a-z0-9_]/', '', \$page);

// Include header
include 'includes/header.php';

// Include page
\$page_file = 'pages/' . \$page . '.php';
if (file_exists(\$page_file)) {
    include \$page_file;
} else {
    echo '<h2>Page not found: ' . htmlspecialchars(\$page) . '</h2>';
    echo '<a href=\"?page=home\">Go Home</a>';
}

// Include footer
include 'includes/footer.php';
?>",

    'pages/home.php' => "<?php
echo '<h1>Welcome to Savori! ☕</h1>';
echo '<p>Your coffee recommendation app is working!</p>';
echo '<a href=\"?page=recommendation\" style=\"background: #8B4513; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;\">Get Recommendation</a>';
?>"
];

foreach ($required_files as $file => $content) {
    $file_path = __DIR__ . '/' . $file;
    
    if (!file_exists($file_path)) {
        // Buat direktori jika belum ada
        $dir = dirname($file_path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        
        if (file_put_contents($file_path, $content)) {
            echo "<div class='success'>✅ Created file: $file</div>";
        } else {
            echo "<div class='error'>❌ Failed to create: $file</div>";
        }
    } else {
        echo "<div class='success'>✅ File exists: $file</div>";
    }
}

// 3. Cek koneksi database
echo "<h2>3. Test Database Connection</h2>";
try {
    $host = 'localhost';
    $dbname = 'savori_db';
    $username = 'root';
    $password = '';
    
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Cek database
    $stmt = $conn->query("SHOW DATABASES LIKE 'savori_db'");
    if ($stmt->rowCount() > 0) {
        echo "<div class='success'>✅ Database 'savori_db' exists</div>";
        
        // Cek tables
        $conn_db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $tables = $conn_db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($tables) > 0) {
            echo "<div class='success'>✅ Tables found: " . count($tables) . "</div>";
            foreach ($tables as $table) {
                echo "<div style='margin-left: 20px;'>📊 $table</div>";
            }
        } else {
            echo "<div class='warning'>⚠️ No tables found. Please import savori.sql</div>";
        }
        
    } else {
        echo "<div class='warning'>⚠️ Database 'savori_db' not found. Creating...</div>";
        
        // Buat database
        $conn->exec("CREATE DATABASE IF NOT EXISTS savori_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "<div class='success'>✅ Database created successfully</div>";
        
        echo "<div class='warning'>⚠️ Please import savori.sql manually via phpMyAdmin</div>";
    }
    
} catch(PDOException $e) {
    echo "<div class='error'>❌ Database error: " . $e->getMessage() . "</div>";
    echo "<div class='warning'>⚠️ Make sure MySQL is running in XAMPP Control Panel</div>";
}

// 4. Cek session
echo "<h2>4. PHP Configuration Check</h2>";
if (session_status() === PHP_SESSION_NONE) {
    echo "<div class='success'>✅ Sessions available</div>";
} else {
    echo "<div class='warning'>⚠️ Session status: " . session_status() . "</div>";
}

// 5. Cek BASE_URL
echo "<h2>5. URL Check</h2>";
$current_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
echo "<div>Current URL: $current_url</div>";
echo "<div>Suggested BASE_URL: http://localhost/Savori/</div>";

echo "</div>"; // Close box

// Link ke aplikasi
echo "<div class='box'>";
echo "<h2>🎯 Next Steps:</h2>";
echo "<ol>";
echo "<li><a href='http://localhost/Savori/' target='_blank'>Open Savori App</a></li>";
echo "<li><a href='http://localhost/phpmyadmin' target='_blank'>Open phpMyAdmin to import database</a></li>";
echo "<li>Import file: savori/database/savori.sql</li>";
echo "<li>Login with: admin / admin123</li>";
echo "</ol>";
echo "</div>";

echo "</body></html>";
?>