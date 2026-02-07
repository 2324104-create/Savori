</main>
        
        <!-- Footer -->
        <footer class="footer bg-dark text-white mt-auto">
            <div class="container py-4">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <h5><i class="fas fa-coffee"></i> Savori</h5>
                        <p>Aplikasi rekomendasi kopi terbaik untuk menemukan minuman yang cocok dengan mood Anda.</p>
                    </div>
                    <div class="col-md-4 mb-4">
                        <h5>Menu Utama</h5>
                        <ul class="list-unstyled">
                            <li><a href="?page=home" class="text-light"><i class="fas fa-home me-2"></i> Home</a></li>
                            <li><a href="?page=recommendation" class="text-light"><i class="fas fa-magic me-2"></i> Rekomendasi</a></li>
                            <li><a href="?page=coffee_list" class="text-light"><i class="fas fa-list me-2"></i> Daftar Kopi</a></li>
                            <!-- Menu untuk pengguna login -->
                            <?php if (isset($_SESSION['user_id'])): ?>
                            <li><a href="?page=favorites" class="text-light"><i class="fas fa-heart me-2"></i> Favorit Saya</a></li>
                            <li><a href="?page=reviews" class="text-light"><i class="fas fa-star me-2"></i> Reviews</a></li>
                            
                            <!-- Menu untuk admin -->
                            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                            <li><a href="?page=admin" class="text-light"><i class="fas fa-crown me-2"></i> Admin Panel</a></li>
                            <li><a href="?page=stock" class="text-light"><i class="fas fa-boxes me-2"></i> Manajemen Stok</a></li>
                            <?php endif; ?>
                            
                            <li><a href="?page=logout" class="text-light"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                            
                            <!-- Menu untuk pengunjung -->
                            <?php else: ?>
                            <li><a href="?page=login" class="text-light"><i class="fas fa-sign-in-alt me-2"></i> Login</a></li>
                            <li><a href="?page=register" class="text-light"><i class="fas fa-user-plus me-2"></i> Register</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="col-md-4 mb-4">
                        <h5>Kontak</h5>
                        <p><i class="fas fa-envelope"></i> info@savori.com</p>
                        <p><i class="fas fa-phone"></i> (021) 1234-5678</p>
                        <p><i class="fas fa-map-marker-alt"></i> Jakarta, Indonesia</p>
                        
                        <h5 class="mt-4">Ikuti Kami</h5>
                        <div class="d-flex gap-3">
                            <a href="#" class="text-light"><i class="fab fa-facebook fa-lg"></i></a>
                            <a href="#" class="text-light"><i class="fab fa-instagram fa-lg"></i></a>
                            <a href="#" class="text-light"><i class="fab fa-twitter fa-lg"></i></a>
                        </div>
                    </div>
                </div>
                <hr class="bg-light">
                <div class="text-center">
                    <p>&copy; <?php echo date('Y'); ?> Savori - Coffee Recommendation App. All rights reserved.</p>
                    <p class="text-muted small">Dibuat dengan ❤️ untuk para pecinta kopi</p>
                </div>
            </div>
        </footer>
        
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- Font Awesome -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
        
        <!-- Custom JS -->
        <script src="assets/js/main.js"></script>
        
        <script>
            // Simple alert system
            function showAlert(message, type = 'success') {
                const alert = document.createElement('div');
                alert.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
                alert.style.zIndex = '9999';
                alert.innerHTML = `
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(alert);
                
                setTimeout(() => {
                    alert.remove();
                }, 3000);
            }
            
            // Handle recommendation form
            document.addEventListener('DOMContentLoaded', function() {
                // Mood selection
                const moodOptions = document.querySelectorAll('.mood-option');
                if (moodOptions.length > 0) {
                    moodOptions.forEach(option => {
                        option.addEventListener('click', function() {
                            moodOptions.forEach(opt => opt.classList.remove('active'));
                            this.classList.add('active');
                            document.getElementById('moodInput').value = this.dataset.value;
                        });
                    });
                }
                
                // Form validation
                const forms = document.querySelectorAll('form');
                forms.forEach(form => {
                    form.addEventListener('submit', function(e) {
                        const required = form.querySelectorAll('[required]');
                        let valid = true;
                        
                        required.forEach(input => {
                            if (!input.value.trim()) {
                                valid = false;
                                input.classList.add('is-invalid');
                            }
                        });
                        
                        if (!valid) {
                            e.preventDefault();
                            showAlert('Harap isi semua field yang wajib diisi!', 'danger');
                        }
                    });
                });
                
                // Check login status untuk update menu
                updateFooterMenu();
            });
            
            // Update footer menu berdasarkan login status
            function updateFooterMenu() {
                // Menu akan diupdate oleh PHP di server-side
                // Fungsi ini untuk handle client-side updates jika diperlukan
            }
            
            // Add to favorites
            function addToFavorite(drinkId) {
                fetch('api/save_favorite.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({drink_id: drinkId})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message);
                    } else {
                        showAlert(data.message, 'danger');
                    }
                });
            }
            
            // Order drink
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
                            showAlert(data.message);
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showAlert(data.message, 'danger');
                        }
                    });
                }
            }
            
            // Submit review
            function submitReview(drinkId) {
                const rating = document.getElementById('rating_' + drinkId).value;
                const comment = document.getElementById('comment_' + drinkId).value;
                
                if (!rating || !comment.trim()) {
                    showAlert('Harap isi rating dan komentar!', 'danger');
                    return;
                }
                
                fetch('api/submit_review.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        drink_id: drinkId,
                        rating: rating,
                        comment: comment
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Review berhasil disimpan!');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showAlert(data.message, 'danger');
                    }
                });
            }
        </script>
    </body>
</html>