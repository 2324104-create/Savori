<?php
// File: includes/header.php
// JANGAN ADA SPASI/ENTER SEBELUM <?php

// Pastikan session sudah start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current page
$page = $_GET['page'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - <?php echo SITE_DESC; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --brown: #8B4513;
            --light-brown: #D2691E;
            --cream: #FFF8DC;
            --dark: #5D4037;
        }
        
        body {
            background-color: var(--cream);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        main {
            flex: 1;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: var(--brown) !important;
            font-size: 1.5rem;
        }
        
        .nav-link {
            color: var(--dark) !important;
            font-weight: 500;
        }
        
        .nav-link:hover {
            color: var(--brown) !important;
        }
        
        .btn-brown {
            background-color: var(--brown);
            color: white;
            border: none;
        }
        
        .btn-brown:hover {
            background-color: var(--light-brown);
            color: white;
        }
        
        .dropdown-menu {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .dropdown-item:hover {
            background-color: rgba(139, 69, 19, 0.1);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="?page=home">
                <i class="fas fa-coffee"></i> Savori
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'home' ? 'active' : ''; ?>" href="?page=home">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'recommendation' ? 'active' : ''; ?>" href="?page=recommendation">
                            <i class="fas fa-magic"></i> Rekomendasi
                        </a>
                    </li>
                    <li class="nav-item">
    <a class="nav-link <?php echo $page == 'coffee_list' ? 'active' : ''; ?>" href="?page=coffee_list">
        <i class="fas fa-list"></i> Daftar Kopi
    </a>
</li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Menu untuk pengguna login -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'favorites' ? 'active' : ''; ?>" href="?page=favorites">
                            <i class="fas fa-heart"></i> Favorit
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'reviews' ? 'active' : ''; ?>" href="?page=reviews">
                            <i class="fas fa-star"></i> Reviews
                        </a>
                    </li>
                    
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                    <!-- Menu untuk admin -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo in_array($page, ['admin', 'stock']) ? 'active' : ''; ?>" 
                           href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-crown"></i> Admin
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item <?php echo $page == 'admin' ? 'active' : ''; ?>" href="?page=admin">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a></li>
                            <li><a class="dropdown-item <?php echo $page == 'stock' ? 'active' : ''; ?>" href="?page=stock">
                                <i class="fas fa-boxes"></i> Manajemen Stok
                            </a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'logout' ? 'active' : ''; ?>" href="?page=logout">
                            <i class="fas fa-sign-out-alt"></i> Logout
                            <span class="badge bg-brown ms-1"><?php echo $_SESSION['username']; ?></span>
                        </a>
                    </li>
                    
                    <?php else: ?>
                    <!-- Menu untuk pengunjung -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'login' ? 'active' : ''; ?>" href="?page=login">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'register' ? 'active' : ''; ?>" href="?page=register">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <main class="py-4">
<!-- JANGAN ADA SPASI DISINI -->