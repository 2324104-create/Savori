<?php
// Get all reviews with user and drink info
$stmt = $db->query("
    SELECT r.*, u.username, d.name as drink_name, d.type 
    FROM reviews r 
    JOIN users u ON r.user_id = u.id 
    JOIN drinks d ON r.drink_id = d.id 
    ORDER BY r.review_date DESC
");
$all_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get user's reviews if logged in
$user_reviews = [];
if (isset($_SESSION['user_id'])) {
    $stmt = $db->prepare("
        SELECT r.*, d.name as drink_name, d.type 
        FROM reviews r 
        JOIN drinks d ON r.drink_id = d.id 
        WHERE r.user_id = ? 
        ORDER BY r.review_date DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $user_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get top rated drinks
$stmt = $db->query("
    SELECT d.*, AVG(r.rating) as avg_rating, COUNT(r.id) as review_count 
    FROM drinks d 
    LEFT JOIN reviews r ON d.id = r.drink_id 
    GROUP BY d.id 
    HAVING review_count > 0 
    ORDER BY avg_rating DESC 
    LIMIT 5
");
$top_rated_drinks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h1 class="mb-4"><i class="fas fa-star text-warning"></i> Review & Rating Kopi</h1>
    
    <!-- User Reviews (if logged in) -->
    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="card mb-5">
        <div class="card-header bg-brown text-white">
            <h4 class="mb-0"><i class="fas fa-user"></i> Review Saya</h4>
        </div>
        <div class="card-body">
            <?php if (!empty($user_reviews)): ?>
            <div class="row">
                <?php foreach ($user_reviews as $review): ?>
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0"><?php echo htmlspecialchars($review['drink_name']); ?></h5>
                                <span class="badge bg-brown"><?php echo $review['type']; ?></span>
                            </div>
                            
                            <div class="rating mb-2">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                <?php endfor; ?>
                                <span class="ms-2"><?php echo $review['rating']; ?>/5</span>
                            </div>
                            
                            <p class="card-text"><?php echo htmlspecialchars($review['comment']); ?></p>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="far fa-calendar"></i> 
                                    <?php echo date('d M Y', strtotime($review['review_date'])); ?>
                                </small>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" 
                                            onclick="editReview(<?php echo $review['drink_id']; ?>, <?php echo $review['rating']; ?>, '<?php echo addslashes($review['comment']); ?>')">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-outline-danger" 
                                            onclick="deleteReview(<?php echo $review['id']; ?>)">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                <h5>Belum ada review</h5>
                <p class="text-muted">Mulailah dengan memberikan review untuk kopi favorit Anda</p>
                <a href="?page=recommendation" class="btn btn-primary">
                    <i class="fas fa-magic"></i> Dapatkan Rekomendasi
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- All Reviews -->
    <div class="card mb-5">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-comments"></i> Semua Review Pengguna</h4>
        </div>
        <div class="card-body">
            <?php if (!empty($all_reviews)): ?>
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
                        <?php foreach ($all_reviews as $review): ?>
                        <tr>
                            <td>
                                <i class="fas fa-user-circle"></i>
                                <?php echo htmlspecialchars($review['username']); ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($review['drink_name']); ?></strong>
                                <br><small class="text-muted"><?php echo $review['type']; ?></small>
                            </td>
                            <td>
                                <div class="rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                    <?php endfor; ?>
                                    <br>
                                    <small class="text-muted"><?php echo $review['rating']; ?>/5</small>
                                </div>
                            </td>
                            <td>
                                <?php 
                                $comment = htmlspecialchars($review['comment']);
                                echo strlen($comment) > 50 ? substr($comment, 0, 50) . '...' : $comment;
                                ?>
                            </td>
                            <td>
                                <small>
                                    <?php echo date('d M Y', strtotime($review['review_date'])); ?>
                                    <br>
                                    <span class="text-muted"><?php echo date('H:i', strtotime($review['review_date'])); ?></span>
                                </small>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="text-center text-muted py-4">Belum ada review dari pengguna</p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Top Rated Drinks -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="fas fa-trophy"></i> Kopi dengan Rating Tertinggi</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($top_rated_drinks as $index => $drink): ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <?php if ($index == 0): ?>
                                <span class="badge bg-warning position-absolute top-0 start-0 m-2">🏆 TOP 1</span>
                            <?php elseif ($index == 1): ?>
                                <span class="badge bg-secondary position-absolute top-0 start-0 m-2">🥈 #2</span>
                            <?php elseif ($index == 2): ?>
                                <span class="badge bg-brown position-absolute top-0 start-0 m-2">🥉 #3</span>
                            <?php endif; ?>
                            
                            <div class="drink-icon mb-3">
                                <i class="fas fa-coffee fa-3x text-brown"></i>
                            </div>
                            
                            <h5><?php echo htmlspecialchars($drink['name']); ?></h5>
                            <p class="text-muted"><?php echo $drink['type']; ?></p>
                            
                            <div class="rating mb-2">
                                <?php 
                                $avg_rating = round($drink['avg_rating'], 1);
                                for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?php echo $i <= round($avg_rating) ? 'text-warning' : 'text-muted'; ?>"></i>
                                <?php endfor; ?>
                                <br>
                                <strong><?php echo $avg_rating; ?></strong>/5
                                <small class="text-muted">(<?php echo $drink['review_count']; ?> review)</small>
                            </div>
                            
                            <a href="?page=recommendation" class="btn btn-sm btn-brown">
                                <i class="fas fa-magic"></i> Rekomendasikan
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalTitle">Beri Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="reviewForm">
                    <input type="hidden" id="reviewDrinkId" name="drink_id">
                    <input type="hidden" id="reviewId" name="review_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Pilih Kopi:</label>
                        <select class="form-control" id="drinkSelect" name="drink_id" required>
                            <option value="">-- Pilih Kopi --</option>
                            <?php
                            $stmt = $db->query("SELECT id, name, type FROM drinks ORDER BY name");
                            $drinks = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($drinks as $drink):
                            ?>
                            <option value="<?php echo $drink['id']; ?>">
                                <?php echo htmlspecialchars($drink['name']); ?> (<?php echo $drink['type']; ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Rating:</label>
                        <div class="rating-stars-large">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="far fa-star star-large" data-value="<?php echo $i; ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" id="reviewRating" name="rating" value="5" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Komentar:</label>
                        <textarea name="comment" class="form-control" rows="4" 
                                  placeholder="Bagikan pengalaman Anda dengan kopi ini..." required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="submitReview()">Kirim Review</button>
            </div>
        </div>
    </div>
</div>

<style>
.rating-stars-large {
    font-size: 40px;
    color: #ddd;
    cursor: pointer;
    margin: 10px 0;
}
.rating-stars-large .star-large:hover,
.rating-stars-large .star-large.active {
    color: #ffc107;
}
.drink-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #8B4513, #D2691E);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    color: white;
}
</style>

<script>
// Initialize star rating
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star-large');
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const value = parseInt(this.getAttribute('data-value'));
            document.getElementById('reviewRating').value = value;
            
            stars.forEach((s, index) => {
                if (index < value) {
                    s.classList.remove('far');
                    s.classList.add('fas', 'active');
                } else {
                    s.classList.remove('fas', 'active');
                    s.classList.add('far');
                }
            });
        });
    });
});

function editReview(drinkId, rating, comment) {
    document.getElementById('reviewModalTitle').textContent = 'Edit Review';
    document.getElementById('reviewDrinkId').value = drinkId;
    document.getElementById('reviewRating').value = rating;
    document.querySelector('textarea[name="comment"]').value = comment;
    
    // Set star rating
    const stars = document.querySelectorAll('.star-large');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('far');
            star.classList.add('fas', 'active');
        } else {
            star.classList.remove('fas', 'active');
            star.classList.add('far');
        }
    });
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
    modal.show();
}

function submitReview() {
    const form = document.getElementById('reviewForm');
    const formData = new FormData(form);
    
    fetch('api/submit_review.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Review berhasil ' + (data.action === 'added' ? 'ditambahkan' : 'diupdate'));
            location.reload();
        } else {
            alert('❌ Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

function deleteReview(reviewId) {
    if (!confirm('Hapus review ini?')) return;
    
    fetch('api/delete_review.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({review_id: reviewId})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Review berhasil dihapus');
            location.reload();
        } else {
            alert('❌ Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

// Add new review button
document.addEventListener('DOMContentLoaded', function() {
    const addReviewBtn = document.getElementById('addReviewBtn');
    if (addReviewBtn) {
        addReviewBtn.addEventListener('click', function() {
            document.getElementById('reviewModalTitle').textContent = 'Beri Review Baru';
            document.getElementById('reviewForm').reset();
            
            // Reset stars
            const stars = document.querySelectorAll('.star-large');
            stars.forEach(star => {
                star.classList.remove('fas', 'active');
                star.classList.add('far');
            });
            
            const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
            modal.show();
        });
    }
});
</script>