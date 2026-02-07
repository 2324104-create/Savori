<?php
// File untuk menampilkan hasil rekomendasi

// Ambil data dari POST
$mood = $_POST['mood'] ?? $_GET['mood'] ?? 'energik';
$weather = $_POST['weather'] ?? $_GET['weather'] ?? 'cerah';
$time = $_POST['time'] ?? $_GET['time'] ?? 'pagi';

// Coffee data (30+ jenis kopi dari Wikipedia)
$coffeeList = [
    [
        'id' => 1,
        'name' => 'Espresso',
        'description' => 'Kopi pekat yang dibuat dengan memaksa air panas melalui bubuk kopi halus. Dasar untuk banyak minuman kopi lainnya.',
        'type' => 'Espresso',
        'caffeine' => 'Tinggi',
        'match' => 95,
        'icon' => '☕',
        'origin' => 'Italia',
        'temp' => 'Panas',
        'prep_time' => '5 menit'
    ],
    [
        'id' => 2,
        'name' => 'Cappuccino',
        'description' => 'Espresso dengan steamed milk dan milk foam dalam proporsi yang sama.',
        'type' => 'Espresso',
        'caffeine' => 'Sedang',
        'match' => 88,
        'icon' => '☕',
        'origin' => 'Italia',
        'temp' => 'Panas',
        'prep_time' => '7 menit'
    ],
    [
        'id' => 3,
        'name' => 'Latte',
        'description' => 'Espresso dengan lebih banyak steamed milk dan sedikit foam.',
        'type' => 'Espresso',
        'caffeine' => 'Sedang',
        'match' => 85,
        'icon' => '☕',
        'origin' => 'Italia',
        'temp' => 'Panas',
        'prep_time' => '8 menit'
    ],
    [
        'id' => 4,
        'name' => 'Americano',
        'description' => 'Espresso dengan air panas, memberikan kekuatan espresso dengan volume yang lebih besar.',
        'type' => 'Espresso',
        'caffeine' => 'Tinggi',
        'match' => 82,
        'icon' => '☕',
        'origin' => 'Italia/Amerika',
        'temp' => 'Panas',
        'prep_time' => '5 menit'
    ],
    [
        'id' => 5,
        'name' => 'Mocha',
        'description' => 'Latte dengan coklat, terkadang dengan whipped cream.',
        'type' => 'Espresso',
        'caffeine' => 'Sedang',
        'match' => 90,
        'icon' => '☕',
        'origin' => 'Italia',
        'temp' => 'Panas',
        'prep_time' => '10 menit'
    ],
    [
        'id' => 6,
        'name' => 'Cold Brew',
        'description' => 'Kopi yang diseduh dengan air dingin selama 12-24 jam, menghasilkan minuman yang halus dan rendah asam.',
        'type' => 'Dingin',
        'caffeine' => 'Sedang',
        'match' => 92,
        'icon' => '🧊',
        'origin' => 'Jepang',
        'temp' => 'Dingin',
        'prep_time' => '12-24 jam'
    ],
    [
        'id' => 7,
        'name' => 'Turkish Coffee',
        'description' => 'Bubuk kopi sangat halus direbus dengan gula dan air dalam cezve (panci khusus).',
        'type' => 'Tradisional',
        'caffeine' => 'Sangat Tinggi',
        'match' => 75,
        'icon' => '☕',
        'origin' => 'Turki',
        'temp' => 'Panas',
        'prep_time' => '15 menit'
    ],
    [
        'id' => 8,
        'name' => 'Vietnamese Iced Coffee',
        'description' => 'Kopi Vietnam yang kuat disajikan dengan susu kental manis dan es.',
        'type' => 'Dingin',
        'caffeine' => 'Tinggi',
        'match' => 80,
        'icon' => '🧊',
        'origin' => 'Vietnam',
        'temp' => 'Dingin',
        'prep_time' => '10 menit'
    ],
    [
        'id' => 9,
        'name' => 'Irish Coffee',
        'description' => 'Kopi dengan wiski Irlandia dan cream di atasnya.',
        'type' => 'Spesial',
        'caffeine' => 'Sedang',
        'match' => 70,
        'icon' => '☕',
        'origin' => 'Irlandia',
        'temp' => 'Panas',
        'prep_time' => '10 menit'
    ],
    [
        'id' => 10,
        'name' => 'Affogato',
        'description' => 'Dessert dimana segelas es krim vanilla "dikaramelkan" dengan espresso panas.',
        'type' => 'Dessert',
        'caffeine' => 'Sedang',
        'match' => 65,
        'icon' => '🍨',
        'origin' => 'Italia',
        'temp' => 'Campuran',
        'prep_time' => '5 menit'
    ],
    [
        'id' => 11,
        'name' => 'Frappuccino',
        'description' => 'Minuman kopi blended dengan es, biasanya dengan berbagai rasa.',
        'type' => 'Dingin',
        'caffeine' => 'Rendah',
        'match' => 85,
        'icon' => '🧊',
        'origin' => 'Amerika',
        'temp' => 'Dingin',
        'prep_time' => '10 menit'
    ],
    [
        'id' => 12,
        'name' => 'Cortado',
        'description' => 'Espresso dengan jumlah steamed milk yang sama untuk mengurangi keasaman.',
        'type' => 'Espresso',
        'caffeine' => 'Tinggi',
        'match' => 78,
        'icon' => '☕',
        'origin' => 'Spanyol',
        'temp' => 'Panas',
        'prep_time' => '6 menit'
    ],
    [
        'id' => 13,
        'name' => 'Kopi Tubruk',
        'description' => 'Kopi Indonesia tradisional dengan bubuk kopi kasar.',
        'type' => 'Tradisional',
        'caffeine' => 'Tinggi',
        'match' => 88,
        'icon' => '☕',
        'origin' => 'Indonesia',
        'temp' => 'Panas',
        'prep_time' => '5 menit'
    ],
    [
        'id' => 14,
        'name' => 'Café Cubano',
        'description' => 'Espresso Kuba yang dibuat dengan gula selama penyeduhan.',
        'type' => 'Tradisional',
        'caffeine' => 'Tinggi',
        'match' => 72,
        'icon' => '☕',
        'origin' => 'Kuba',
        'temp' => 'Panas',
        'prep_time' => '5 menit'
    ],
    [
        'id' => 15,
        'name' => 'Greek Frappé',
        'description' => 'Kopi Yunani instan yang dikocok dengan es.',
        'type' => 'Dingin',
        'caffeine' => 'Sedang',
        'match' => 68,
        'icon' => '🧊',
        'origin' => 'Yunani',
        'temp' => 'Dingin',
        'prep_time' => '5 menit'
    ]
];

// Hitung rekomendasi berdasarkan preferensi
function calculateRecommendations($mood, $weather, $time, $coffees) {
    $scoredCoffees = [];
    
    foreach ($coffees as $coffee) {
        $score = $coffee['match'];
        
        // Adjust score based on mood
        if ($mood == 'energik' && $coffee['caffeine'] == 'Tinggi') $score += 10;
        if ($mood == 'santai' && $coffee['caffeine'] == 'Rendah') $score += 10;
        if ($mood == 'stres' && $coffee['type'] == 'Tradisional') $score += 8;
        if ($mood == 'bahagia' && in_array($coffee['type'], ['Dessert', 'Spesial'])) $score += 12;
        
        // Adjust score based on weather
        if ($weather == 'panas' && $coffee['temp'] == 'Dingin') $score += 15;
        if ($weather == 'dingin' && $coffee['temp'] == 'Panas') $score += 15;
        if ($weather == 'hujan' && in_array($coffee['type'], ['Tradisional', 'Espresso'])) $score += 10;
        
        // Adjust score based on time
        if ($time == 'pagi' && $coffee['caffeine'] == 'Tinggi') $score += 10;
        if ($time == 'malam' && $coffee['caffeine'] == 'Rendah') $score += 10;
        
        $scoredCoffees[] = [
            'coffee' => $coffee,
            'score' => min(100, $score) // Max 100%
        ];
    }
    
    // Sort by score
    usort($scoredCoffees, function($a, $b) {
        return $b['score'] <=> $a['score'];
    });
    
    // Return top 3
    return array_slice($scoredCoffees, 0, 3);
}

// Get recommendations
$recommendations = calculateRecommendations($mood, $weather, $time, $coffeeList);
?>

<div class="container py-4">
    <!-- Results Header -->
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold text-brown">✨ Hasil Rekomendasi Kopi</h1>
        <p class="lead">Berdasarkan preferensi yang Anda pilih</p>
    </div>
    
    <!-- Preference Summary -->
    <div class="card shadow mb-4 border-success">
        <div class="card-body bg-success bg-opacity-10">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="text-success mb-3">
                        <i class="fas fa-check-circle"></i> Rekomendasi Ditemukan!
                    </h4>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-primary p-2">
                            <i class="fas fa-smile me-1"></i> Mood: <?php echo ucfirst($mood); ?>
                        </span>
                        <span class="badge bg-info p-2">
                            <i class="fas fa-cloud me-1"></i> Cuaca: <?php echo ucfirst($weather); ?>
                        </span>
                        <span class="badge bg-warning p-2">
                            <i class="fas fa-clock me-1"></i> Waktu: <?php echo ucfirst($time); ?>
                        </span>
                        <span class="badge bg-secondary p-2">
                            <i class="fas fa-calendar me-1"></i> <?php echo date('d/m/Y H:i'); ?>
                        </span>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <a href="?page=recommendation" class="btn btn-outline-brown">
                        <i class="fas fa-redo"></i> Cari Lagi
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recommendations -->
    <div class="row g-4">
        <?php foreach ($recommendations as $index => $rec): 
            $coffee = $rec['coffee'];
            $match = $rec['score'];
            $isTop = $index == 0;
        ?>
        <div class="col-md-4">
            <div class="card h-100 shadow <?php echo $isTop ? 'border-warning border-3' : ''; ?> animate__animated animate__fadeInUp" style="animation-delay: <?php echo $index * 0.2; ?>s">
                <?php if ($isTop): ?>
                <div class="card-header bg-warning text-dark">
                    <i class="fas fa-crown me-2"></i> REKOMENDASI TERBAIK
                </div>
                <?php endif; ?>
                
                <div class="card-body text-center">
                    <!-- Match Percentage -->
                    <div class="match-circle mx-auto mb-3">
                        <svg width="100" height="100">
                            <circle cx="50" cy="50" r="40" fill="none" stroke="#e9ecef" stroke-width="8"/>
                            <circle cx="50" cy="50" r="40" fill="none" stroke="#28a745" stroke-width="8" 
                                    stroke-dasharray="251.2" 
                                    stroke-dashoffset="<?php echo 251.2 * (1 - $match/100); ?>"
                                    stroke-linecap="round" transform="rotate(-90 50 50)"/>
                        </svg>
                        <div class="match-text">
                            <strong><?php echo $match; ?>%</strong>
                            <small>Cocok</small>
                        </div>
                    </div>
                    
                    <!-- Coffee Icon -->
                    <div class="coffee-icon mb-3" style="font-size: 3.5rem;">
                        <?php echo $coffee['icon']; ?>
                    </div>
                    
                    <!-- Coffee Name -->
                    <h5 class="card-title"><?php echo $coffee['name']; ?></h5>
                    
                    <!-- Badges -->
                    <p class="text-center mb-3">
                        <span class="badge bg-brown"><?php echo $coffee['type']; ?></span>
                        <span class="badge bg-info"><?php echo $coffee['caffeine']; ?></span>
                        <span class="badge bg-secondary"><?php echo $coffee['origin']; ?></span>
                    </p>
                    
                    <!-- Description -->
                    <p class="card-text text-muted small mb-4">
                        <?php echo $coffee['description']; ?>
                    </p>
                    
                    <!-- Details -->
                    <div class="details mb-4">
                        <div class="row text-center">
                            <div class="col-6 mb-2">
                                <small class="text-muted d-block">Suhu</small>
                                <div class="fw-bold">
                                    <?php echo $coffee['temp'] == 'Panas' ? '🔥 Panas' : '🧊 Dingin'; ?>
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <small class="text-muted d-block">Waktu</small>
                                <div class="fw-bold">
                                    <i class="fas fa-clock"></i> <?php echo $coffee['prep_time']; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <button class="btn btn-success" onclick="orderDrink(<?php echo $coffee['id']; ?>)">
                            <i class="fas fa-shopping-cart"></i> Pesan Sekarang
                        </button>
                        
                        <button class="btn btn-outline-brown" onclick="addToFavorite(<?php echo $coffee['id']; ?>)">
                            <i class="far fa-heart"></i> Simpan ke Favorit
                        </button>
                        <?php else: ?>
                        <a href="?page=login" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Login untuk Memesan
                        </a>
                        <a href="?page=login" class="btn btn-outline-brown">
                            <i class="far fa-heart"></i> Login untuk Simpan Favorit
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
      <!-- More Options -->
    <div class="row mt-5">
        <div class="col-md-12 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-history fa-3x text-brown mb-3"></i>
                    <h5>Ingin Mencoba Lagi?</h5>
                    <p>Cari rekomendasi dengan preferensi berbeda</p>
                    <a href="?page=recommendation" class="btn btn-brown w-100">
                        <i class="fas fa-redo me-2"></i> Cari Rekomendasi Baru
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tips Section -->
    <div class="card shadow mt-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i> Tips Menikmati Kopi</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-start">
                        <div class="me-3 text-warning">
                            <i class="fas fa-temperature-high fa-2x"></i>
                        </div>
                        <div>
                            <h6>Suhu Ideal</h6>
                            <p class="small text-muted mb-0">Kopi panas nikmati dalam 15 menit setelah diseduh</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-start">
                        <div class="me-3 text-warning">
                            <i class="fas fa-cookie fa-2x"></i>
                        </div>
                        <div>
                            <h6>Pairing Makanan</h6>
                            <p class="small text-muted mb-0">Espresso cocok dengan coklat, Latte cocok dengan croissant</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-start">
                        <div class="me-3 text-warning">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <div>
                            <h6>Waktu Terbaik</h6>
                            <p class="small text-muted mb-0">Kopi tinggi kafein baik dinikmati pagi hari untuk energi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.match-circle {
    position: relative;
    width: 100px;
    height: 100px;
    margin: 0 auto;
}

.match-circle svg {
    width: 100%;
    height: 100%;
}

.match-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.match-text strong {
    font-size: 1.8rem;
    display: block;
    color: #28a745;
    font-weight: bold;
}

.match-text small {
    font-size: 0.9rem;
    color: #666;
}

.coffee-icon {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.badge {
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: 500;
}

.bg-brown {
    background-color: #8B4513 !important;
}

.btn-brown {
    background-color: #8B4513;
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-brown:hover {
    background-color: #A0522D;
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(139, 69, 19, 0.3);
}

.btn-outline-brown {
    border: 2px solid #8B4513;
    color: #8B4513;
    background: transparent;
    padding: 12px 30px;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-outline-brown:hover {
    background-color: #8B4513;
    color: white;
    transform: translateY(-3px);
}

.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    transition: transform 0.3s;
}

.card:hover {
    transform: translateY(-5px);
}
</style>

<script>
function orderDrink(drinkId) {
    if (confirm('Pesan kopi ini?')) {
        alert('✅ Pesanan berhasil! Kopi sedang dipersiapkan.');
    }
}

function addToFavorite(drinkId) {
    alert('✅ Kopi telah ditambahkan ke favorit!');
}

// Add animation to cards when they come into view
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });
    
    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s, transform 0.5s';
        observer.observe(card);
    });
});
</script>

<!-- Animate.css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">