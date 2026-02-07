<?php
if (!isset($_SESSION['user_id'])) {
    echo '<script>window.location.href = "?page=login";</script>';
    exit;
}

// Get user favorites
$stmt = $db->prepare("
    SELECT d.*, f.added_at 
    FROM favorites f 
    JOIN drinks d ON f.drink_id = d.id 
    WHERE f.user_id = ? 
    ORDER BY f.added_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h1 class="mb-4"><i class="fas fa-heart text-danger"></i> Kopi Favorit Saya</h1>
    
    <?php if (empty($favorites)): ?>
    <div class="text-center py-5">
        <i class="fas fa-heart-broken fa-5x text-muted mb-4"></i>
        <h3>Belum ada kopi favorit</h3>
        <p class="text-muted">Tambahkan kopi favorit Anda dari halaman rekomendasi</p>
        <a href="?page=recommendation" class="btn btn-primary">
            <i class="fas fa-magic"></i> Dapatkan Rekomendasi
        </a>
    </div>
    <?php else: ?>
    <div class="row">
        <?php foreach ($favorites as $fav): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($fav['name']); ?></h5>
                    <p class="card-text"><?php echo substr($fav['description'], 0, 100); ?>...</p>
                    <div class="mb-3">
                        <span class="badge bg-brown"><?php echo $fav['type']; ?></span>
                        <span class="ms-2"><i class="fas fa-clock"></i> <?php echo $fav['preparation_time']; ?> min</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="far fa-calendar"></i> 
                            <?php echo date('d M Y', strtotime($fav['added_at'])); ?>
                        </small>
                        <div class="btn-group">
                            <a href="?page=recommendation" class="btn btn-sm btn-brown">
                                <i class="fas fa-redo"></i> Rekomendasikan
                            </a>
                            <button class="btn btn-sm btn-outline-danger" onclick="removeFavorite(<?php echo $fav['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script>
function removeFavorite(drinkId) {
    if (confirm('Hapus dari favorit?')) {
        fetch('api/save_favorite.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({drink_id: drinkId})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Dihapus dari favorit');
                location.reload();
            }
        });
    }
}
</script>