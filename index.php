<?php
// Start session
session_start();

// Define constants
define('SITE_NAME', 'Savori');
define('SITE_DESC', 'Rekomendasi Kopi Terbaik');

// Database connection
require_once 'includes/database_connect.php';

// Default page
$page = $_GET['page'] ?? 'home';

// Available pages
$pages = [
    'home' => 'Home',
    'login' => 'Login',
    'register' => 'Register',
    'recommendation' => 'Rekomendasi',
    'recommendation_result' => 'Hasil Rekomendasi',
    'favorites' => 'Favorit',
    'reviews' => 'Reviews',
    'admin' => 'Admin Panel',
    'stock' => 'Manajemen Stok',
    'dashboard' => 'Dashboard',
    'logout' => 'Logout'
];

// Check if page exists
if (!array_key_exists($page, $pages)) {
    $page = 'home';
}

// Include header
require_once 'includes/header.php';

// Include page content
$page_file = "pages/{$page}.php";
if (file_exists($page_file)) {
    require_once $page_file;
} else {
    echo '<div class="container py-5 text-center">
            <h2 class="text-danger">Halaman Tidak Ditemukan</h2>
            <p>Halaman yang Anda cari tidak tersedia.</p>
            <a href="?page=home" class="btn btn-brown">Kembali ke Home</a>
          </div>';
}
// Available pages
$pages = [
    'home' => 'Home',
    'login' => 'Login',
    'register' => 'Register',
    'recommendation' => 'Rekomendasi',
    'recommendation_result' => 'Hasil Rekomendasi',
    'coffee_list' => 'Daftar Kopi',
    'favorites' => 'Favorit',
    'reviews' => 'Reviews',
    'admin' => 'Admin Panel',
    'stock' => 'Manajemen Stok',
    'dashboard' => 'Dashboard',
    'logout' => 'Logout'
];
// Include footer
require_once 'includes/footer.php';
?>