<?php
// Handle registration
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    
    // Validation
    $errors = [];
    
    if (empty($username)) $errors[] = "Username harus diisi";
    if (empty($email)) $errors[] = "Email harus diisi";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email tidak valid";
    if (empty($password)) $errors[] = "Password harus diisi";
    if (strlen($password) < 6) $errors[] = "Password minimal 6 karakter";
    if ($password !== $confirm_password) $errors[] = "Password tidak cocok";
    
    // Check if username/email exists
    if (empty($errors)) {
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->rowCount() > 0) {
            $errors[] = "Username atau email sudah terdaftar";
        }
    }
    
    // If no errors, register user
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (username, email, password, full_name) VALUES (?, ?, ?, ?)");
        
        if ($stmt->execute([$username, $email, $hashed_password, $full_name])) {
            // Auto login after registration
            $user_id = $db->lastInsertId();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['user_role'] = 'user';
            $_SESSION['full_name'] = $full_name;
            
            echo '<script>
                alert("Registrasi berhasil! Selamat datang di Savori ☕");
                window.location.href = "?page=dashboard";
            </script>';
            exit;
        } else {
            $errors[] = "Terjadi kesalahan saat registrasi";
        }
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card shadow register-card">
                <div class="card-header text-center bg-brown text-white">
                    <h3 class="mb-0"><i class="fas fa-user-plus"></i> Buat Akun Savori</h3>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-at"></i></span>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           placeholder="Pilih username" required>
                                </div>
                                <small class="text-muted">Minimal 3 karakter</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           placeholder="contoh@email.com" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                       placeholder="Masukkan nama lengkap">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Minimal 6 karakter" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Konfirmasi Password *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                           placeholder="Ulangi password" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">
                                Saya menyetujui <a href="#" class="text-brown">Syarat & Ketentuan</a> dan <a href="#" class="text-brown">Kebijakan Privasi</a>
                            </label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus"></i> Daftar Sekarang
                            </button>
                            <a href="?page=login" class="btn btn-outline-brown">
                                <i class="fas fa-sign-in-alt"></i> Sudah punya akun? Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Benefits -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-magic fa-2x text-brown mb-3"></i>
                            <h5>Rekomendasi Personal</h5>
                            <p>Dapatkan rekomendasi kopi berdasarkan mood dan preferensi Anda</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-heart fa-2x text-brown mb-3"></i>
                            <h5>Simpan Favorit</h5>
                            <p>Simpan kopi favorit dan lihat history rekomendasi Anda</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-star fa-2x text-brown mb-3"></i>
                            <h5>Berikan Review</h5>
                            <p>Bagikan pengalaman Anda dengan memberikan rating dan review</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.register-card {
    border-radius: 15px;
    border: none;
    margin-top: 30px;
}
.btn-outline-brown {
    color: #8B4513;
    border-color: #8B4513;
}
.btn-outline-brown:hover {
    background-color: #8B4513;
    color: white;
}
</style>
<?php
// File: pages/login.php, register.php, admin.php, dll
ob_start(); // Tambahkan ini di baris pertama

// Kode PHP lainnya...

// Sebelum header() atau di akhir file:
ob_end_flush();
?>