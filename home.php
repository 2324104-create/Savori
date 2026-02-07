<?php
// Get stats
$total_drinks = $db->query("SELECT COUNT(*) FROM drinks")->fetchColumn();
$total_users = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$available_today = $db->query("SELECT COUNT(DISTINCT d.id) FROM drinks d 
                               LEFT JOIN stock s ON d.id = s.drink_id 
                               WHERE d.is_available = 1 
                               AND (s.available_quantity > 0 OR s.available_quantity IS NULL)")->fetchColumn();

// Get popular drinks with more data
$popular = $db->query("SELECT * FROM drinks WHERE is_available = 1 ORDER BY popularity_score DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);

// Get today's recommendation
$today = date('l');
$dayRecommendations = [
    'Monday' => 'Espresso untuk memulai minggu dengan semangat!',
    'Tuesday' => 'Cappuccino untuk hari yang produktif!',
    'Wednesday' => 'Latte untuk pertengahan minggu yang santai!',
    'Thursday' => 'Mocha untuk hari yang manis!',
    'Friday' => 'Cold Brew untuk akhir pekan yang segar!',
    'Saturday' => 'Turkish Coffee untuk hari spesial!',
    'Sunday' => 'Pour Over untuk refleksi hari Minggu!'
];
$todayRecommendation = $dayRecommendations[$today] ?? 'Rekomendasi kopi terbaik untuk hari ini!';
?>

<div class="container py-4">
    <!-- Hero Section dengan Animasi -->
    <div class="hero-section text-center mb-5 animate__animated animate__fadeIn">
        <div class="floating-coffee">☕</div>
        <h1 class="display-4 fw-bold text-brown mb-3 animate__animated animate__bounceIn">
            <span class="gradient-text">☕ Selamat Datang di Savori</span>
        </h1>
        <p class="lead mb-4 animate__animated animate__fadeInUp">Temukan kopi sempurna untuk mood Anda hari ini</p>
        
        <div class="mt-4 animate__animated animate__fadeInUp animate__delay-1s">
            <div class="today-recommendation mb-4">
                <div class="badge bg-warning text-dark p-3">
                    <i class="fas fa-calendar-day me-2"></i>
                    <strong>Rekomendasi Hari Ini:</strong> <?php echo $todayRecommendation; ?>
                </div>
            </div>
            
            <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                <a href="?page=register" class="btn btn-brown btn-lg pulse-animation">
                    <i class="fas fa-user-plus me-2"></i> Daftar Sekarang
                </a>
                <a href="?page=login" class="btn btn-outline-brown btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i> Login
                </a>
                <a href="?page=recommendation" class="btn btn-warning btn-lg">
                    <i class="fas fa-magic me-2"></i> Coba Sekarang
                </a>
            </div>
            <?php else: ?>
            <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                <a href="?page=recommendation" class="btn btn-brown btn-lg pulse-animation">
                    <i class="fas fa-magic me-2"></i> Dapatkan Rekomendasi
                </a>
                <a href="?page=favorites" class="btn btn-outline-brown btn-lg">
                    <i class="fas fa-heart me-2"></i> Favorit Saya
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Stats dengan Animasi -->
    <div class="row mb-5">
        <div class="col-md-4 mb-3">
            <div class="card text-center h-100 shadow stats-card animate__animated animate__fadeInLeft">
                <div class="card-body">
                    <div class="stats-icon mb-3">
                        <i class="fas fa-coffee fa-3x text-brown"></i>
                    </div>
                    <h2 class="display-4 fw-bold stats-number" data-count="<?php echo $total_drinks; ?>">0</h2>
                    <h5>Jenis Kopi</h5>
                    <p class="text-muted">Tersedia di sistem</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center h-100 shadow stats-card animate__animated animate__fadeInUp">
                <div class="card-body">
                    <div class="stats-icon mb-3">
                        <i class="fas fa-users fa-3x text-primary"></i>
                    </div>
                    <h2 class="display-4 fw-bold stats-number" data-count="<?php echo $total_users; ?>">0</h2>
                    <h5>Pengguna</h5>
                    <p class="text-muted">Terdaftar</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center h-100 shadow stats-card animate__animated animate__fadeInRight">
                <div class="card-body">
                    <div class="stats-icon mb-3">
                        <i class="fas fa-check-circle fa-3x text-success"></i>
                    </div>
                    <h2 class="display-4 fw-bold stats-number" data-count="<?php echo $available_today; ?>">0</h2>
                    <h5>Tersedia Hari Ini</h5>
                    <p class="text-muted">Siap diseduh</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions dengan Hover Effect -->
    <div class="card shadow mb-5 action-card">
        <div class="card-header bg-brown text-white">
            <h4 class="mb-0"><i class="fas fa-bolt me-2"></i> Mulai Eksplorasi Kopi Anda</h4>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-3 col-sm-6">
                    <a href="?page=recommendation" class="action-btn">
                        <div class="icon-wrapper">
                            <i class="fas fa-magic fa-3x"></i>
                        </div>
                        <h5>Dapatkan Rekomendasi</h5>
                        <p class="small text-muted">Berdasarkan mood & preferensi</p>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="#popular" class="action-btn">
                        <div class="icon-wrapper">
                            <i class="fas fa-fire fa-3x"></i>
                        </div>
                        <h5>Lihat Populer</h5>
                        <p class="small text-muted">Kopi paling dicari</p>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="?page=favorites" class="action-btn">
                        <div class="icon-wrapper">
                            <i class="fas fa-heart fa-3x"></i>
                        </div>
                        <h5>Favorit Saya</h5>
                        <p class="small text-muted">Koleksi pribadi Anda</p>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="#how-it-works" class="action-btn">
                        <div class="icon-wrapper">
                            <i class="fas fa-play-circle fa-3x"></i>
                        </div>
                        <h5>Cara Kerja</h5>
                        <p class="small text-muted">Lihat tutorial</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Popular Drills dengan Carousel -->
    <div class="mb-5" id="popular">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-brown"><i class="fas fa-crown me-2"></i> Kopi Terpopuler</h2>
            <a href="?page=recommendation" class="btn btn-sm btn-outline-brown">
                <i class="fas fa-eye me-1"></i> Lihat Semua
            </a>
        </div>
        
        <div class="row g-4">
            <?php foreach ($popular as $index => $drink): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow coffee-card animate__animated animate__fadeInUp" style="animation-delay: <?php echo $index * 0.1; ?>s">
                    <div class="coffee-img">
                        <div class="popular-badge">
                            <i class="fas fa-fire"></i> #<?php echo $index + 1; ?>
                        </div>
                        <div class="coffee-icon">
                            <?php
                            $icons = ['☕', '🧊', '💧', '🍨', '🔥', '⭐'];
                            $icon = $icons[$index % count($icons)];
                            echo $icon;
                            ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($drink['name']); ?></h5>
                        <p class="card-text text-muted small">
                            <?php echo htmlspecialchars(substr($drink['description'], 0, 80)); ?>...
                        </p>
                        
                        <div class="mb-3">
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" 
                                     style="width: <?php echo $drink['popularity_score']; ?>%"
                                     title="Popularity: <?php echo $drink['popularity_score']; ?>%">
                                </div>
                            </div>
                            <small class="text-muted d-block mt-1">
                                Popularitas: <?php echo $drink['popularity_score']; ?>%
                            </small>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-brown"><?php echo $drink['type']; ?></span>
                                <span class="badge bg-info"><?php echo $drink['caffeine_level']; ?></span>
                            </div>
                            <div class="text-warning">
                                <?php
                                $stars = round($drink['popularity_score'] / 20);
                                echo str_repeat('★', $stars) . str_repeat('☆', 5 - $stars);
                                ?>
                            </div>
                        </div>
                        
                        <div class="mt-3 d-grid">
                            <a href="?page=recommendation" class="btn btn-sm btn-brown">
                                <i class="fas fa-magic me-1"></i> Rekomendasikan untuk Saya
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- How It Works dengan Steps -->
    <div class="card shadow how-it-works" id="how-it-works">
        <div class="card-header bg-brown text-white">
            <h4 class="mb-0"><i class="fas fa-question-circle me-2"></i> Cara Kerja Savori</h4>
        </div>
        <div class="card-body p-4">
            <div class="row text-center steps">
                <div class="col-md-3 mb-4 step-item">
                    <div class="step-number animate__animated animate__pulse animate__infinite animate__slow">1</div>
                    <div class="step-icon">
                        <i class="fas fa-smile fa-2x"></i>
                    </div>
                    <h5>Pilih Mood</h5>
                    <p>Pilih kondisi Anda saat ini</p>
                    <div class="step-description">
                        <small class="text-muted">Energik, Santai, Stres, Bahagia, atau Sedih</small>
                    </div>
                </div>
                <div class="col-md-3 mb-4 step-item">
                    <div class="step-number">2</div>
                    <div class="step-icon">
                        <i class="fas fa-cloud-sun fa-2x"></i>
                    </div>
                    <h5>Pilih Cuaca</h5>
                    <p>Pilih cuaca di tempat Anda</p>
                    <div class="step-description">
                        <small class="text-muted">Panas, Dingin, Hujan, atau Cerah</small>
                    </div>
                </div>
                <div class="col-md-3 mb-4 step-item">
                    <div class="step-number">3</div>
                    <div class="step-icon">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <h5>Pilih Waktu</h5>
                    <p>Pilih waktu menikmati kopi</p>
                    <div class="step-description">
                        <small class="text-muted">Pagi, Siang, Sore, atau Malam</small>
                    </div>
                </div>
                <div class="col-md-3 mb-4 step-item">
                    <div class="step-number">4</div>
                    <div class="step-icon">
                        <i class="fas fa-trophy fa-2x"></i>
                    </div>
                    <h5>Dapatkan Rekomendasi</h5>
                    <p>Sistem akan memberikan rekomendasi terbaik</p>
                    <div class="step-description">
                        <small class="text-muted">3 rekomendasi terbaik dengan persentase kecocokan</small>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <div class="d-inline-block p-3 bg-light rounded">
                    <i class="fas fa-lightbulb text-warning fa-2x me-2"></i>
                    <span class="fw-bold">Tips:</span> Login untuk menyimpan rekomendasi dan melihat riwayat!
                </div>
            </div>
        </div>
    </div>
    
    <!-- Testimonial Section -->
    <div class="testimonial-section mt-5">
        <h3 class="text-center mb-4 text-brown">
            <i class="fas fa-comments me-2"></i> Kata Pengguna Savori
        </h3>
        
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="user-avatar">A</div>
                        <div>
                            <h6 class="mb-0">Andi</h6>
                            <small class="text-muted">Coffee Enthusiast</small>
                        </div>
                    </div>
                    <p class="testimonial-text">
                        "Rekomendasi kopinya pas banget dengan mood saya! Sistemnya cerdas."
                    </p>
                    <div class="text-warning">
                        ★★★★★
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="user-avatar">S</div>
                        <div>
                            <h6 class="mb-0">Sari</h6>
                            <small class="text-muted">Barista</small>
                        </div>
                    </div>
                    <p class="testimonial-text">
                        "Sebagai barista, saya terkesan dengan variasi kopi yang direkomendasikan."
                    </p>
                    <div class="text-warning">
                        ★★★★★
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="user-avatar">R</div>
                        <div>
                            <h6 class="mb-0">Rudi</h6>
                            <small class="text-muted">Remote Worker</small>
                        </div>
                    </div>
                    <p class="testimonial-text">
                        "Sempurna untuk menemani kerja remote. Favorit saya Flat White!"
                    </p>
                    <div class="text-warning">
                        ★★★★★
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Styles untuk Home Page yang Lebih Menarik -->
<style>
/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, rgba(139, 69, 19, 0.1) 0%, rgba(210, 105, 30, 0.05) 100%);
    padding: 80px 30px;
    border-radius: 30px;
    margin-top: 20px;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(139, 69, 19, 0.05) 0%, transparent 70%);
    animation: float 20s infinite linear;
}

@keyframes float {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.floating-coffee {
    font-size: 5rem;
    animation: floatUpDown 3s ease-in-out infinite;
    margin-bottom: 20px;
}

@keyframes floatUpDown {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}

.gradient-text {
    background: linear-gradient(45deg, #8B4513, #D2691E, #A0522D);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    display: inline-block;
}

/* Pulse Animation */
.pulse-animation {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Stats Cards */
.stats-card {
    transition: transform 0.3s;
    border: none;
}

.stats-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(139, 69, 19, 0.2);
}

.stats-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #FFF8DC, #F5DEB3);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.stats-number {
    color: #8B4513;
    font-weight: 800;
}

/* Action Buttons */
.action-btn {
    display: block;
    padding: 30px 20px;
    border: 2px solid transparent;
    border-radius: 15px;
    text-align: center;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s;
    height: 100%;
}

.action-btn:hover {
    border-color: #8B4513;
    background: rgba(139, 69, 19, 0.05);
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    text-decoration: none;
    color: inherit;
}

.icon-wrapper {
    width: 80px;
    height: 80px;
    background: linear-gradient(45deg, #8B4513, #D2691E);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    color: white;
}

.action-btn h5 {
    color: #8B4513;
    margin-bottom: 10px;
}

/* Coffee Cards */
.coffee-card {
    transition: all 0.3s;
    border: none;
    overflow: hidden;
}

.coffee-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(139, 69, 19, 0.3);
}

.coffee-img {
    height: 180px;
    background: linear-gradient(45deg, #8B4513, #D2691E);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.popular-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    background: rgba(255, 193, 7, 0.9);
    color: #333;
    padding: 5px 12px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 0.9rem;
}

.coffee-icon {
    font-size: 4rem;
    color: white;
    animation: spin 20s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* How It Works */
.how-it-works {
    border: none;
}

.step-item {
    padding: 20px;
    transition: transform 0.3s;
}

.step-item:hover {
    transform: translateY(-5px);
}

.step-number {
    width: 60px;
    height: 60px;
    background: linear-gradient(45deg, #8B4513, #D2691E);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    font-weight: bold;
    margin: 0 auto 15px;
}

.step-icon {
    margin-bottom: 15px;
    color: #8B4513;
}

.step-description {
    margin-top: 10px;
}

/* Testimonials */
.testimonial-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s;
    height: 100%;
}

.testimonial-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.testimonial-header {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.user-avatar {
    width: 50px;
    height: 50px;
    background: linear-gradient(45deg, #8B4513, #D2691E);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
    margin-right: 15px;
}

.testimonial-text {
    font-style: italic;
    color: #555;
    line-height: 1.6;
    margin-bottom: 15px;
}

/* Today Recommendation */
.today-recommendation .badge {
    font-size: 1.1rem;
    border-radius: 25px;
    animation: glow 2s infinite alternate;
}

@keyframes glow {
    from { box-shadow: 0 0 10px rgba(255, 193, 7, 0.5); }
    to { box-shadow: 0 0 20px rgba(255, 193, 7, 0.8); }
}

/* Progress Bar */
.progress {
    background-color: #f5f5f5;
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    border-radius: 10px;
    transition: width 1s ease-in-out;
}

/* Responsive */
@media (max-width: 768px) {
    .hero-section {
        padding: 50px 20px;
    }
    
    .floating-coffee {
        font-size: 3rem;
    }
    
    .stats-number {
        font-size: 2.5rem;
    }
    
    .action-btn {
        padding: 20px 15px;
    }
    
    .step-item {
        margin-bottom: 30px;
    }
}
</style>

<!-- JavaScript untuk Animasi -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate stats numbers
    const statNumbers = document.querySelectorAll('.stats-number');
    
    statNumbers.forEach(stat => {
        const target = parseInt(stat.getAttribute('data-count'));
        const duration = 2000; // 2 seconds
        const increment = target / (duration / 16); // 60fps
        
        let current = 0;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            stat.textContent = Math.floor(current);
        }, 16);
    });
    
    // Add hover effect to coffee cards
    const coffeeCards = document.querySelectorAll('.coffee-card');
    coffeeCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Animate steps on scroll
    const steps = document.querySelectorAll('.step-item');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.5 });
    
    steps.forEach(step => {
        step.style.opacity = '0';
        step.style.transform = 'translateY(20px)';
        step.style.transition = 'opacity 0.5s, transform 0.5s';
        observer.observe(step);
    });
    
    // Auto-rotate coffee icons
    const coffeeIcons = document.querySelectorAll('.coffee-icon');
    coffeeIcons.forEach(icon => {
        let rotation = 0;
        setInterval(() => {
            rotation += 1;
            icon.style.transform = `rotate(${rotation}deg)`;
        }, 50);
    });
    
    // Add click effect to action buttons
    const actionBtns = document.querySelectorAll('.action-btn');
    actionBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Add ripple effect
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(139, 69, 19, 0.3);
                transform: scale(0);
                animation: ripple 0.6s linear;
                width: ${size}px;
                height: ${size}px;
                top: ${y}px;
                left: ${x}px;
                pointer-events: none;
            `;
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
    
    // Add CSS for ripple animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
});
</script>

<!-- Animate.css for animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">