<?php
// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            
            echo '<script>window.location.href = "?page=dashboard";</script>';
            exit;
        } else {
            $error = "Username atau password salah!";
        }
    } else {
        $error = "Harap isi semua field!";
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card login-card shadow">
                <div class="card-header text-center bg-brown text-white">
                    <h3 class="mb-0"><i class="fas fa-coffee"></i> Login ke Savori</h3>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-group mb-3">
                            <label for="username" class="form-label">Username atau Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="username" name="username" 
                                       placeholder="Masukkan username atau email" required>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Masukkan password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Ingat saya</label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-4 text-center">
                        <p>Belum punya akun? <a href="?page=register" class="text-brown fw-bold">Daftar disini</a></p>
                        <p><a href="#" class="text-muted">Lupa password?</a></p>
                    </div>
                    
                    <div class="text-center mt-4">
                        <p class="text-muted">Atau login dengan:</p>
                        <div class="d-flex justify-content-center gap-3">
                            <button class="btn btn-outline-danger">
                                <i class="fab fa-google"></i> Google
                            </button>
                            <button class="btn btn-outline-primary">
                                <i class="fab fa-facebook"></i> Facebook
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Demo Accounts -->
            <div class="card mt-4 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-info-circle"></i> Akun Demo</h5>
                    <p class="card-text">Gunakan akun berikut untuk testing:</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>Admin:</strong> admin / admin123
                        </li>
                        <li class="list-group-item">
                            <strong>User:</strong> user1 / password123
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.login-card {
    border-radius: 15px;
    border: none;
    margin-top: 50px;
}
.bg-brown {
    background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);
}
.btn-primary {
    background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);
    border: none;
}
.text-brown {
    color: #8B4513 !important;
}
</style>

<script>
// Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});
</script>
<?php
// File: pages/login.php, register.php, admin.php, dll
ob_start(); // Tambahkan ini di baris pertama

// Kode PHP lainnya...

// Sebelum header() atau di akhir file:
ob_end_flush();
?>