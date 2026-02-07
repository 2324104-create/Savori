<?php
/**
 * Configuration File
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Base URL configuration
define('BASE_URL', 'http://localhost/Savori/');

// Site configuration
define('SITE_NAME', 'Savori');
define('SITE_DESC', 'Aplikasi Rekomendasi Kopi Terbaik');

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
$db_connect_path = __DIR__ . '/../includes/database_connect.php';
if (file_exists($db_connect_path)) {
    require_once $db_connect_path;
} else {
    die("File koneksi database tidak ditemukan. Path: " . $db_connect_path);
}

// Helper function untuk meload models
function loadModel($modelName) {
    $modelPath = __DIR__ . '/../models/' . $modelName . '.php';
    if (file_exists($modelPath)) {
        require_once $modelPath;
    } else {
        die("Model tidak ditemukan: " . $modelPath);
    }
}

// Helper function untuk meload includes
function loadInclude($includeName) {
    $includePath = __DIR__ . '/../includes/' . $includeName . '.php';
    if (file_exists($includePath)) {
        require_once $includePath;
    } else {
        die("Include file tidak ditemukan: " . $includePath);
    }
}
?>