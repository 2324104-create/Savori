<?php
// Admin check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo '<script>alert("Akses ditolak! Hanya admin yang bisa mengakses halaman ini."); window.location.href = "?page=home";</script>';
    exit;
}

// Handle stock update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_stock'])) {
    $drink_id = $_POST['drink_id'];
    $quantity = $_POST['quantity'];
    $day = $_POST['day'];
    
    try {
        $stmt = $db->prepare("UPDATE stock SET available_quantity = ? WHERE drink_id = ? AND day = ?");
        $stmt->execute([$quantity, $drink_id, $day]);
        $success = "Stok berhasil diupdate!";
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Handle daily stock setup
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['setup_daily'])) {
    try {
        // Get all drinks
        $stmt = $db->query("SELECT id FROM drinks WHERE is_available = 1");
        $drinks = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Setup stock for next 7 days
        for ($i = 0; $i < 7; $i++) {
            $day = date('Y-m-d', strtotime("+$i days"));
            
            foreach ($drinks as $drink_id) {
                // Check if stock exists for this day
                $stmt = $db->prepare("SELECT id FROM stock WHERE drink_id = ? AND day = ?");
                $stmt->execute([$drink_id, $day]);
                
                if ($stmt->rowCount() == 0) {
                    // Insert new stock
                    $stmt = $db->prepare("INSERT INTO stock (drink_id, day, available_quantity) VALUES (?, ?, 20)");
                    $stmt->execute([$drink_id, $day]);
                }
            }
        }
        $success = "Stok harian untuk 7 hari ke depan berhasil disetup!";
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get statistics
$stats = [
    'total_users' => $db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'total_drinks' => $db->query("SELECT COUNT(*) FROM drinks")->fetchColumn(),
    'total_orders' => $db->query("SELECT SUM(20 - available_quantity) FROM stock WHERE day = CURDATE()")->fetchColumn(),
    'total_reviews' => $db->query("SELECT COUNT(*) FROM reviews")->fetchColumn()
];

// Get today's stock
$stmt = $db->query("
    SELECT d.id, d.name, d.type, s.available_quantity, s.day 
    FROM drinks d 
    LEFT JOIN stock s ON d.id = s.drink_id AND s.day = CURDATE() 
    WHERE d.is_available = 1
    ORDER BY d.name
");
$today_stock = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get low stock items
$stmt = $db->query("
    SELECT d.name, s.available_quantity, s.day 
    FROM drinks d 
    JOIN stock s ON d.id = s.drink_id 
    WHERE s.available_quantity <= 5 
    AND s.day >= CURDATE()
    ORDER BY s.available_quantity ASC
");
$low_stock = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent orders
$stmt = $db->query("
    SELECT u.username, d.name, s.day, (20 - s.available_quantity) as ordered 
    FROM stock s 
    JOIN drinks d ON s.drink_id = d.id 
    JOIN users u ON (SELECT user_id FROM recommendations WHERE drink_id = d.id ORDER BY recommended_at DESC LIMIT 1) = u.id
    WHERE s.available_quantity < 20 
    AND s.day = CURDATE()
    ORDER BY s.day DESC 
    LIMIT 10
");
$recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <!-- Admin Header -->
    <div class="admin-header mb-5">
        <h1><i class="fas fa-crown text-warning"></i> Admin Dashboard</h1>
        <p class="text-muted">Kelola sistem Savori dari sini</p>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-5">
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <h2><?php echo $stats['total_users']; ?></h2>
                    <h5>Total Users</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-success text-white">
                <div class="card-body">
                    <i class="fas fa-coffee fa-3x mb-3"></i>
                    <h2><?php echo $stats['total_drinks']; ?></h2>
                    <h5>Jenis Kopi</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-warning text-white">
                <div class="card-body">
                    <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                    <h2><?php echo $stats['total_orders'] ?: 0; ?></h2>
                    <h5>Pesanan Hari Ini</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card bg-info text-white">
                <div class="card-body">
                    <i class="fas fa-star fa-3x mb-3"></i>
                    <h2><?php echo $stats['total_reviews']; ?></h2>
                    <h5>Total Review</h5>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stock Management -->
    <div class="row">
        <div class="col-md-8">
            <!-- Today's Stock -->
            <div class="card mb-4">
                <div class="card-header bg-brown text-white">
                    <h4 class="mb-0"><i class="fas fa-boxes"></i> Manajemen Stok - Hari Ini (<?php echo date('d M Y'); ?>)</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama Kopi</th>
                                    <th>Tipe</th>
                                    <th>Stok Tersedia</th>
                                    <th>Update Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($today_stock as $stock): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($stock['name']); ?></td>
                                    <td><span class="badge bg-brown"><?php echo $stock['type']; ?></span></td>
                                    <td>
                                        <span class="<?php echo $stock['available_quantity'] > 10 ? 'text-success' : ($stock['available_quantity'] > 5 ? 'text-warning' : 'text-danger'); ?>">
                                            <strong><?php echo $stock['available_quantity'] ?: 0; ?></strong> cup
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" class="d-flex gap-2">
                                            <input type="hidden" name="drink_id" value="<?php echo $stock['id']; ?>">
                                            <input type="hidden" name="day" value="<?php echo date('Y-m-d'); ?>">
                                            <input type="number" name="quantity" value="<?php echo $stock['available_quantity'] ?: 0; ?>" 
                                                   min="0" max="100" class="form-control form-control-sm" style="width: 80px;">
                                            <button type="submit" name="update_stock" class="btn btn-sm btn-primary">
                                                <i class="fas fa-save"></i> Update
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Daily Setup Button -->
                    <div class="mt-3">
                        <form method="POST">
                            <button type="submit" name="setup_daily" class="btn btn-success">
                                <i class="fas fa-calendar-plus"></i> Setup Stok untuk 7 Hari ke Depan
                            </button>
                            <small class="text-muted ms-2">(Akan set stok default 20 untuk semua kopi)</small>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Low Stock Alert -->
            <?php if (!empty($low_stock)): ?>
            <div class="card mb-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Stok Menipis!</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama Kopi</th>
                                    <th>Tanggal</th>
                                    <th>Stok Tersisa</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($low_stock as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td><?php echo date('d M Y', strtotime($item['day'])); ?></td>
                                    <td><span class="badge bg-danger"><?php echo $item['available_quantity']; ?></span></td>
                                    <td>
                                        <?php if ($item['available_quantity'] == 0): ?>
                                            <span class="badge bg-dark">HABIS</span>
                                        <?php elseif ($item['available_quantity'] <= 2): ?>
                                            <span class="badge bg-danger">KRITIS</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">MENIPIS</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header bg-brown text-white">
                    <h4 class="mb-0"><i class="fas fa-bolt"></i> Aksi Cepat</h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="?page=stock" class="btn btn-outline-brown">
                            <i class="fas fa-chart-line"></i> Lihat Laporan Stok
                        </a>
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addDrinkModal">
                            <i class="fas fa-plus-circle"></i> Tambah Jenis Kopi Baru
                        </button>
                        <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewUsersModal">
                            <i class="fas fa-users"></i> Kelola Users
                        </button>
                        <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#viewReviewsModal">
                            <i class="fas fa-star"></i> Lihat Semua Review
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Recent Orders -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0"><i class="fas fa-history"></i> Pesanan Terbaru</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_orders)): ?>
                    <div class="list-group">
                        <?php foreach ($recent_orders as $order): ?>
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><?php echo htmlspecialchars($order['name']); ?></h6>
                                <small><?php echo $order['ordered']; ?>x</small>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($order['username']); ?>
                                • <?php echo date('H:i', strtotime($order['day'])); ?>
                            </small>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p class="text-muted text-center mb-0">Belum ada pesanan hari ini</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- System Info -->
            <div class="card mt-4">
                <div class="card-header bg-secondary text-white">
                    <h4 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Sistem</h4>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-server text-primary"></i>
                            <strong>Server:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-database text-success"></i>
                            <strong>Database:</strong> MySQL
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-code text-warning"></i>
                            <strong>PHP Version:</strong> <?php echo phpversion(); ?>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-calendar text-info"></i>
                            <strong>Tanggal:</strong> <?php echo date('d F Y H:i:s'); ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Drink Modal -->
<div class="modal fade" id="addDrinkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Tambah Kopi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addDrinkForm">
                    <div class="mb-3">
                        <label class="form-label">Nama Kopi</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="description" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipe</label>
                            <select class="form-control" name="type" required>
                                <option value="Espresso">Espresso</option>
                                <option value="Cappuccino">Cappuccino</option>
                                <option value="Latte">Latte</option>
                                <option value="Americano">Americano</option>
                                <option value="Mocha">Mocha</option>
                                <option value="Cold Brew">Cold Brew</option>
                                <option value="Macchiato">Macchiato</option>
                                <option value="Flat White">Flat White</option>
                                <option value="Turkish">Turkish</option>
                                <option value="Frappuccino">Frappuccino</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Waktu Penyajian (menit)</label>
                            <input type="number" class="form-control" name="preparation_time" min="1" max="60" value="5" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kesulitan</label>
                            <select class="form-control" name="difficulty" required>
                                <option value="Easy">Easy</option>
                                <option value="Medium">Medium</option>
                                <option value="Hard">Hard</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Level Kafein</label>
                            <select class="form-control" name="caffeine_level" required>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                                <option value="Very High">Very High</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bahan-bahan</label>
                        <textarea class="form-control" name="ingredients" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stok Awal</label>
                        <input type="number" class="form-control" name="initial_stock" min="1" max="100" value="20" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="addNewDrink()">Tambah Kopi</button>
            </div>
        </div>
    </div>
</div>

<!-- View Users Modal -->
<div class="modal fade" id="viewUsersModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-users"></i> Daftar Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Bergabung</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $db->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
                            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($users as $user):
                            ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="badge <?php echo $user['role'] == 'admin' ? 'bg-warning' : 'bg-info'; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Reviews Modal -->
<div class="modal fade" id="viewReviewsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-star"></i> Semua Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Kopi</th>
                                <th>Rating</th>
                                <th>Komentar</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $db->query("
                                SELECT u.username, d.name, r.rating, r.comment, r.review_date 
                                FROM reviews r 
                                JOIN users u ON r.user_id = u.id 
                                JOIN drinks d ON r.drink_id = d.id 
                                ORDER BY r.review_date DESC
                            ");
                            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($reviews as $review):
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($review['username']); ?></td>
                                <td><?php echo htmlspecialchars($review['name']); ?></td>
                                <td>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                    <?php endfor; ?>
                                </td>
                                <td><?php echo htmlspecialchars(substr($review['comment'], 0, 50)) . '...'; ?></td>
                                <td><?php echo date('d M Y', strtotime($review['review_date'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.admin-header {
    padding: 30px;
    background: linear-gradient(135deg, #2c3e50, #4a6491);
    color: white;
    border-radius: 15px;
}
.stat-card {
    border: none;
    border-radius: 15px;
    transition: transform 0.3s;
}
.stat-card:hover {
    transform: translateY(-5px);
}
.table-responsive {
    max-height: 400px;
    overflow-y: auto;
}
</style>

<script>
function addNewDrink() {
    const form = document.getElementById('addDrinkForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    
    fetch('api/add_drink.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('✅ Kopi berhasil ditambahkan!');
            location.reload();
        } else {
            alert('❌ Error: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}
</script>
<?php
// File: pages/login.php, register.php, admin.php, dll
ob_start(); // Tambahkan ini di baris pertama

// Kode PHP lainnya...

// Sebelum header() atau di akhir file:
ob_end_flush();
?>