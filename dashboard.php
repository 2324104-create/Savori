<?php
// Get stats
$total_drinks = $db->query("SELECT COUNT(*) FROM drinks")->fetchColumn();
$total_users = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$available_today = $db->query("SELECT COUNT(DISTINCT d.id) FROM drinks d LEFT JOIN stock s ON d.id = s.drink_id WHERE d.is_available = 1 AND (s.available_quantity > 0 OR s.available_quantity IS NULL)")->fetchColumn();

// Get popular drinks
$popular = $db->query("SELECT * FROM drinks WHERE is_available = 1 ORDER BY popularity_score DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-4">
    <!-- Welcome Section -->
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold text-brown">☕ Selamat Datang di Savori</h1>
        <p class="lead">Temukan kopi sempurna untuk mood Anda hari ini</p>
        
        <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="mt-4">
            <a href="?page=register" class="btn btn-brown btn-lg">
                <i class="fas fa-user-plus"></i> Daftar Sekarang
            </a>
            <a href="?page=login" class="btn btn-outline-brown btn-lg">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Stats -->
    <div class="row mb-5">
        <div class="col-md-4 mb-3">
            <div class="card text-center h-100 shadow">
                <div class="card-body">
                    <i class="fas fa-coffee fa-3x text-brown mb-3"></i>
                    <h2><?php echo $total_drinks; ?></h2>
                    <h5>Jenis Kopi</h5>
                    <p class="text-muted">Tersedia di sistem</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center h-100 shadow">
                <div class="card-body">
                    <i class="fas fa-users fa-3x text-primary mb-3"></i>
                    <h2><?php echo $total_users; ?></h2>
                    <h5>Pengguna</h5>
                    <p class="text-muted">Terdaftar</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center h-100 shadow">
                <div class="card-body">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h2><?php echo $available_today; ?></h2>
                    <h5>Tersedia Hari Ini</h5>
                    <p class="text-muted">Siap diseduh</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="card shadow mb-5">
        <div class="card-header bg-brown text-white">
            <h4 class="mb-0"><i class="fas fa-bolt"></i> Mulai Eksplorasi</h4>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <a href="?page=recommendation" class="btn btn-outline-brown w-100 h-100 py-3">
                        <i class="fas fa-magic fa-2x mb-2 d-block"></i>
                        <span>Dapatkan Rekomendasi</span>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="#popular" class="btn btn-outline-brown w-100 h-100 py-3">
                        <i class="fas fa-fire fa-2x mb-2 d-block"></i>
                        <span>Lihat Populer</span>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="?page=favorites" class="btn btn-outline-brown w-100 h-100 py-3">
                        <i class="fas fa-heart fa-2x mb-2 d-block"></i>
                        <span>Favorit Saya</span>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="?page=reviews" class="btn btn-outline-brown w-100 h-100 py-3">
                        <i class="fas fa-star fa-2x mb-2 d-block"></i>
                        <span>Beri Review</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Popular Drinks -->
    <div class="mb-5" id="popular">
        <h2 class="mb-4 text-brown"><i class="fas fa-crown"></i> Kopi Terpopuler</h2>
        <div class="row g-4">
            <?php foreach ($popular as $drink): ?>
            <div class="col-md-4">
                <div class="card h-100 shadow">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="drink-icon mx-auto">
                                <i class="fas fa-coffee fa-3x text-brown"></i>
                            </div>
                        </div>
                        
                        <h5 class="card-title text-center"><?php echo htmlspecialchars($drink['name']); ?></h5>
                        <p class="text-center mb-3">
                            <span class="badge bg-brown"><?php echo $drink['type']; ?></span>
                        </p>
                        
                        <p class="card-text text-muted text-center">
                            <small><?php echo $drink['description']; ?></small>
                        </p>
                        
                        <div class="details text-center mb-3">
                            <small class="text-muted d-block">
                                <i class="fas fa-clock"></i> <?php echo $drink['preparation_time']; ?> menit
                            </small>
                            <small class="text-muted">
                                <i class="fas fa-bolt"></i> <?php echo $drink['caffeine_level']; ?>
                            </small>
                        </div>
                        
                        <div class="text-center">
                            <a href="?page=recommendation" class="btn btn-sm btn-brown">
                                <i class="fas fa-magic"></i> Rekomendasikan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- How It Works -->
    <div class="card shadow">
        <div class="card-header bg-brown text-white">
            <h4 class="mb-0"><i class="fas fa-question-circle"></i> Cara Kerja Savori</h4>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3 mb-3">
                    <div class="step-number">1</div>
                    <h5>Pilih Mood</h5>
                    <p>Pilih kondisi Anda saat ini</p>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="step-number">2</div>
                    <h5>Pilih Cuaca</h5>
                    <p>Pilih cuaca di tempat Anda</p>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="step-number">3</div>
                    <h5>Pilih Waktu</h5>
                    <p>Pilih waktu menikmati kopi</p>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="step-number">4</div>
                    <h5>Dapatkan Rekomendasi</h5>
                    <p>Sistem akan memberikan rekomendasi terbaik</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.step-number {
    width: 50px;
    height: 50px;
    background: var(--brown);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: bold;
    margin: 0 auto 10px;
}

.drink-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #FFF8DC, #F5DEB3);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card {
    transition: transform 0.3s;
}

.card:hover {
    transform: translateY(-5px);
}
</style>