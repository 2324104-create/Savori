<?php
// Admin check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo '<script>alert("Akses ditolak!"); window.location.href = "?page=home";</script>';
    exit;
}

// Date range
$start_date = $_GET['start_date'] ?? date('Y-m-d');
$end_date = $_GET['end_date'] ?? date('Y-m-d', strtotime('+7 days'));

// Get stock report
$stmt = $db->prepare("
    SELECT d.name, d.type, s.day, s.available_quantity,
           (20 - s.available_quantity) as ordered_today,
           (SELECT SUM(20 - available_quantity) FROM stock s2 WHERE s2.drink_id = d.id AND s2.day BETWEEN ? AND ?) as total_ordered
    FROM drinks d 
    LEFT JOIN stock s ON d.id = s.drink_id 
    WHERE s.day BETWEEN ? AND ?
    ORDER BY s.day DESC, d.name
");
$stmt->execute([$start_date, $end_date, $start_date, $end_date]);
$stock_report = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group by date for display
$grouped_report = [];
foreach ($stock_report as $item) {
    $date = $item['day'];
    if (!isset($grouped_report[$date])) {
        $grouped_report[$date] = [];
    }
    $grouped_report[$date][] = $item;
}

// Get popular drinks
$stmt = $db->query("
    SELECT d.name, 
           SUM(20 - s.available_quantity) as total_ordered,
           AVG(r.rating) as avg_rating
    FROM drinks d 
    LEFT JOIN stock s ON d.id = s.drink_id 
    LEFT JOIN reviews r ON d.id = r.drink_id 
    GROUP BY d.id 
    ORDER BY total_ordered DESC 
    LIMIT 10
");
$popular_drinks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-chart-line"></i> Laporan Stok</h1>
        <a href="?page=admin" class="btn btn-brown">
            <i class="fas fa-arrow-left"></i> Kembali ke Admin
        </a>
    </div>
    
    <!-- Date Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <input type="hidden" name="page" value="stock">
                <div class="col-md-4">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" name="start_date" value="<?php echo $start_date; ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" name="end_date" value="<?php echo $end_date; ?>" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="?page=stock" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Stock Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center bg-primary text-white">
                <div class="card-body">
                    <i class="fas fa-calendar fa-2x mb-2"></i>
                    <h5>Periode</h5>
                    <p class="mb-0"><?php echo date('d M Y', strtotime($start_date)); ?> - <?php echo date('d M Y', strtotime($end_date)); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center bg-success text-white">
                <div class="card-body">
                    <i class="fas fa-coffee fa-2x mb-2"></i>
                    <h5>Total Kopi</h5>
                    <p class="mb-0"><?php echo count(array_unique(array_column($stock_report, 'name'))); ?> jenis</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center bg-warning text-white">
                <div class="card-body">
                    <i class="fas fa-boxes fa-2x mb-2"></i>
                    <h5>Total Stok</h5>
                    <p class="mb-0"><?php echo array_sum(array_column($stock_report, 'available_quantity')); ?> cup</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center bg-info text-white">
                <div class="card-body">
                    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                    <h5>Total Terjual</h5>
                    <p class="mb-0"><?php echo array_sum(array_column($stock_report, 'ordered_today')); ?> cup</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stock Report by Date -->
    <div class="card mb-4">
        <div class="card-header bg-brown text-white">
            <h4 class="mb-0"><i class="fas fa-table"></i> Detail Stok per Tanggal</h4>
        </div>
        <div class="card-body">
            <?php if (empty($grouped_report)): ?>
                <p class="text-center text-muted py-4">Tidak ada data stok untuk periode ini</p>
            <?php else: ?>
                <?php foreach ($grouped_report as $date => $items): ?>
                <div class="mb-4">
                    <h5 class="border-bottom pb-2">
                        <i class="fas fa-calendar-day"></i> 
                        <?php echo date('l, d F Y', strtotime($date)); ?>
                        <span class="badge bg-secondary float-end"><?php echo count($items); ?> item</span>
                    </h5>
                    
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Nama Kopi</th>
                                    <th>Tipe</th>
                                    <th>Stok Awal</th>
                                    <th>Stok Tersisa</th>
                                    <th>Terjual</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td><span class="badge bg-brown"><?php echo $item['type']; ?></span></td>
                                    <td>20</td>
                                    <td>
                                        <span class="<?php echo $item['available_quantity'] > 10 ? 'text-success' : ($item['available_quantity'] > 5 ? 'text-warning' : 'text-danger'); ?>">
                                            <strong><?php echo $item['available_quantity']; ?></strong>
                                        </span>
                                    </td>
                                    <td><?php echo $item['ordered_today']; ?></td>
                                    <td>
                                        <?php if ($item['available_quantity'] == 0): ?>
                                            <span class="badge bg-danger">HABIS</span>
                                        <?php elseif ($item['available_quantity'] <= 3): ?>
                                            <span class="badge bg-warning">MENIPIS</span>
                                        <?php elseif ($item['available_quantity'] <= 10): ?>
                                            <span class="badge bg-info">CUKUP</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">AMAN</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Popular Drinks -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="fas fa-chart-bar"></i> Kopi Terpopuler (Berdasarkan Penjualan)</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Peringkat</th>
                            <th>Nama Kopi</th>
                            <th>Total Terjual</th>
                            <th>Rating Rata-rata</th>
                            <th>Popularitas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($popular_drinks as $index => $drink): ?>
                        <tr>
                            <td>
                                <?php if ($index == 0): ?>
                                    <span class="badge bg-warning">🥇 #1</span>
                                <?php elseif ($index == 1): ?>
                                    <span class="badge bg-secondary">🥈 #2</span>
                                <?php elseif ($index == 2): ?>
                                    <span class="badge bg-brown">🥉 #3</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark">#<?php echo $index + 1; ?></span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars($drink['name']); ?></strong></td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-success" 
                                         style="width: <?php echo min(100, ($drink['total_ordered'] ?: 0) * 10); ?>%">
                                        <?php echo $drink['total_ordered'] ?: 0; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if ($drink['avg_rating']): ?>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= round($drink['avg_rating']) ? 'text-warning' : 'text-muted'; ?>"></i>
                                    <?php endfor; ?>
                                    (<?php echo number_format($drink['avg_rating'], 1); ?>)
                                <?php else: ?>
                                    <span class="text-muted">Belum ada rating</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                $popularity = ($drink['total_ordered'] ?: 0) * 5 + ($drink['avg_rating'] ?: 0) * 10;
                                $popularity = min(100, $popularity);
                                ?>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-info" style="width: <?php echo $popularity; ?>%">
                                        <?php echo round($popularity); ?>%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Export Button -->
    <div class="text-center mt-4">
        <button class="btn btn-primary" onclick="exportToPDF()">
            <i class="fas fa-file-pdf"></i> Export ke PDF
        </button>
        <button class="btn btn-success" onclick="exportToExcel()">
            <i class="fas fa-file-excel"></i> Export ke Excel
        </button>
        <button class="btn btn-dark" onclick="window.print()">
            <i class="fas fa-print"></i> Print Laporan
        </button>
    </div>
</div>

<style>
.table-responsive {
    max-height: 500px;
    overflow-y: auto;
}
.progress {
    border-radius: 10px;
}
</style>

<script>
function exportToPDF() {
    alert('Fitur export PDF akan segera hadir!');
}

function exportToExcel() {
    alert('Fitur export Excel akan segera hadir!');
}
</script>