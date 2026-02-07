<?php
// Get all drinks with stock info
$stmt = $db->query("
    SELECT d.*, s.available_quantity, s.day as stock_date 
    FROM drinks d 
    LEFT JOIN stock s ON d.id = s.drink_id AND s.day = CURDATE()
    WHERE d.is_available = 1
    ORDER BY d.popularity_score DESC
");
$coffees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories
$categories = array_unique(array_column($coffees, 'type'));
?>

<div class="container py-4">
    <h1 class="mb-4"><i class="fas fa-list"></i> Daftar Kopi Tersedia</h1>
    
    <!-- Filter by category -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-filter"></i> Filter Berdasarkan Kategori</h5>
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-sm btn-brown" onclick="filterCoffee('all')">Semua</button>
                <?php foreach ($categories as $category): ?>
                <button class="btn btn-sm btn-outline-brown" onclick="filterCoffee('<?php echo $category; ?>')">
                    <?php echo $category; ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Coffee List -->
    <div class="row" id="coffeeList">
        <?php foreach ($coffees as $coffee): ?>
        <div class="col-md-4 mb-4 coffee-item" data-category="<?php echo $coffee['type']; ?>">
            <div class="card h-100 shadow">
                <div class="card-body">
                    <!-- Stock Indicator -->
                    <div class="position-absolute top-0 end-0 m-2">
                        <?php if ($coffee['available_quantity'] > 10): ?>
                            <span class="badge bg-success">Tersedia</span>
                        <?php elseif ($coffee['available_quantity'] > 0): ?>
                            <span class="badge bg-warning">Menipis</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Habis</span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Coffee Icon -->
                    <div class="text-center mb-3">
                        <div class="coffee-icon mx-auto">
                            <i class="fas fa-coffee fa-3x text-brown"></i>
                        </div>
                    </div>
                    
                    <!-- Coffee Name -->
                    <h5 class="card-title text-center"><?php echo htmlspecialchars($coffee['name']); ?></h5>
                    
                    <!-- Coffee Type -->
                    <p class="text-center mb-3">
                        <span class="badge bg-brown"><?php echo $coffee['type']; ?></span>
                    </p>
                    
                    <!-- Description -->
                    <p class="card-text text-muted small text-center mb-3">
                        <?php echo $coffee['description']; ?>
                    </p>
                    
                    <!-- Details -->
                    <div class="details mb-3">
                        <div class="row text-center">
                            <div class="col-6">
                                <small class="text-muted d-block">Waktu</small>
                                <div class="fw-bold">
                                    <i class="fas fa-clock"></i> <?php echo $coffee['preparation_time']; ?> min
                                </div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Kafein</small>
                                <div class="fw-bold">
                                    <i class="fas fa-bolt"></i> <?php echo $coffee['caffeine_level']; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stock Information -->
                    <div class="stock-info mb-3">
                        <div class="progress" style="height: 10px;">
                            <?php 
                            $stock_percentage = ($coffee['available_quantity'] / 20) * 100;
                            $stock_class = '';
                            if ($stock_percentage > 50) $stock_class = 'bg-success';
                            elseif ($stock_percentage > 20) $stock_class = 'bg-warning';
                            else $stock_class = 'bg-danger';
                            ?>
                            <div class="progress-bar <?php echo $stock_class; ?>" 
                                 style="width: <?php echo $stock_percentage; ?>%">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">Stok hari ini</small>
                            <small class="fw-bold <?php echo $stock_percentage > 50 ? 'text-success' : ($stock_percentage > 20 ? 'text-warning' : 'text-danger'); ?>">
                                <?php echo $coffee['available_quantity'] ?: 0; ?> / 20 cup
                            </small>
                        </div>
                    </div>
                    
                    <!-- Popularity -->
                    <div class="popularity mb-3">
                        <small class="text-muted d-block mb-1">Popularitas</small>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" style="width: <?php echo $coffee['popularity_score']; ?>%">
                            </div>
                        </div>
                        <small class="text-muted"><?php echo $coffee['popularity_score']; ?>%</small>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <a href="?page=recommendation" class="btn btn-brown">
                            <i class="fas fa-magic"></i> Rekomendasikan
                        </a>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($coffee['available_quantity'] > 0): ?>
                        <button class="btn btn-success" onclick="orderDrink(<?php echo $coffee['id']; ?>)">
                            <i class="fas fa-shopping-cart"></i> Pesan Sekarang
                        </button>
                        <?php else: ?>
                        <button class="btn btn-secondary" disabled>
                            <i class="fas fa-times-circle"></i> Stok Habis
                        </button>
                        <?php endif; ?>
                        
                        <button class="btn btn-outline-brown" onclick="addToFavorite(<?php echo $coffee['id']; ?>)">
                            <i class="far fa-heart"></i> Simpan ke Favorit
                        </button>
                        <?php else: ?>
                        <a href="?page=login" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Login untuk Memesan
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <?php if (empty($coffees)): ?>
    <div class="text-center py-5">
        <i class="fas fa-coffee fa-5x text-muted mb-4"></i>
        <h3>Tidak ada kopi tersedia</h3>
        <p class="text-muted">Silakan cek kembali nanti</p>
    </div>
    <?php endif; ?>
</div>

<style>
.coffee-icon {
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
    border: none;
    border-radius: 15px;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}

.stock-info .progress {
    border-radius: 5px;
}

.popularity .progress {
    border-radius: 5px;
    background-color: #f0f0f0;
}
</style>

<script>
function filterCoffee(category) {
    const items = document.querySelectorAll('.coffee-item');
    const buttons = document.querySelectorAll('.card-body .btn');
    
    buttons.forEach(btn => {
        if (category === 'all') {
            btn.classList.remove('btn-brown');
            btn.classList.add('btn-outline-brown');
        } else {
            if (btn.textContent.includes(category)) {
                btn.classList.remove('btn-outline-brown');
                btn.classList.add('btn-brown');
            } else {
                btn.classList.remove('btn-brown');
                btn.classList.add('btn-outline-brown');
            }
        }
    });
    
    items.forEach(item => {
        if (category === 'all' || item.dataset.category === category) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function orderDrink(drinkId) {
    if (confirm('Pesan kopi ini?')) {
        fetch('api/order_drink.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({drink_id: drinkId})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ Pesanan berhasil! Stok akan berkurang.');
                setTimeout(() => location.reload(), 1500);
            } else {
                alert('❌ ' + data.message);
            }
        });
    }
}

function addToFavorite(drinkId) {
    fetch('api/save_favorite.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({drink_id: drinkId})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Kopi ditambahkan ke favorit!');
        } else {
            alert('⚠️ ' + data.message);
        }
    });
}
</script>