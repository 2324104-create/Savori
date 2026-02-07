// Recommendation System
document.addEventListener('DOMContentLoaded', function() {
    // Mood buttons
    const moodButtons = document.querySelectorAll('.mood-btn');
    const moodInput = document.getElementById('mood');
    
    moodButtons.forEach(button => {
        button.addEventListener('click', function() {
            moodButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            moodInput.value = this.dataset.value;
        });
    });
    
    // Weather buttons
    const weatherButtons = document.querySelectorAll('.weather-btn');
    const weatherInput = document.getElementById('weather');
    
    weatherButtons.forEach(button => {
        button.addEventListener('click', function() {
            weatherButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            weatherInput.value = this.dataset.value;
        });
    });
    
    // Time buttons
    const timeButtons = document.querySelectorAll('.time-btn');
    const timeInput = document.getElementById('time_of_day');
    
    timeButtons.forEach(button => {
        button.addEventListener('click', function() {
            timeButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            timeInput.value = this.dataset.value;
        });
    });
    
    // Recommendation form submission
    const recommendationForm = document.getElementById('recommendationForm');
    const loadingElement = document.getElementById('loading');
    const resultsContainer = document.getElementById('resultsContainer');
    
    recommendationForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validate form
        if (!moodInput.value || !weatherInput.value || !timeInput.value) {
            alert('Silakan pilih mood, cuaca, dan waktu terlebih dahulu!');
            return;
        }
        
        // Show loading animation
        loadingElement.style.display = 'block';
        resultsContainer.innerHTML = '';
        
        // Prepare data
        const formData = {
            mood: moodInput.value,
            weather: weatherInput.value,
            time_of_day: timeInput.value,
            coffee_type: Array.from(document.getElementById('coffee_type').selectedOptions)
                .map(option => option.value)
        };
        
        try {
            // Send request to API
            const response = await fetch('<?php echo BASE_URL; ?>api/recommendation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            
            // Hide loading
            loadingElement.style.display = 'none';
            
            if (data.success) {
                displayRecommendations(data.recommendations, data.total_available);
                showConfetti();
            } else {
                resultsContainer.innerHTML = `
                    <div class="card">
                        <h3>😔 ${data.message}</h3>
                        <p>Coba lagi dengan preferensi yang berbeda, atau hubungi admin untuk informasi stok.</p>
                    </div>
                `;
            }
            
        } catch (error) {
            console.error('Error:', error);
            loadingElement.style.display = 'none';
            resultsContainer.innerHTML = `
                <div class="card">
                    <h3>⚠️ Terjadi Kesalahan</h3>
                    <p>Silakan coba lagi nanti.</p>
                </div>
            `;
        }
    });
    
    // Display recommendations
    function displayRecommendations(recommendations, totalAvailable) {
        if (recommendations.length === 0) {
            resultsContainer.innerHTML = `
                <div class="card">
                    <h3>😔 Tidak ada rekomendasi yang cocok</h3>
                    <p>Coba dengan preferensi yang berbeda.</p>
                </div>
            `;
            return;
        }
        
        let html = `
            <div class="recommendation-header card">
                <h2>🎉 Ditemukan ${totalAvailable} kopi tersedia!</h2>
                <p>Berikut 5 rekomendasi terbaik untukmu:</p>
            </div>
            <div class="stagger-animation">
        `;
        
        recommendations.forEach((rec, index) => {
            const drink = rec.drink;
            const matchScore = Math.round(rec.score * 10);
            
            html += `
                <div class="drink-card" style="animation-delay: ${index * 0.1}s">
                    <div class="drink-image" style="background-image: url('${drink.image_url || 'assets/images/coffee-bg.jpg'}')">
                        ${index === 0 ? '<div class="best-choice">🏆 Pilihan Terbaik</div>' : ''}
                    </div>
                    <div class="drink-info">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <h3>${drink.name}</h3>
                            <span class="match-score" style="background: ${getScoreColor(matchScore)}; color: white; padding: 5px 15px; border-radius: 15px; font-weight: bold;">
                                ${matchScore}% Cocok
                            </span>
                        </div>
                        <p class="drink-type">${drink.type}</p>
                        <p class="drink-desc">${drink.description}</p>
                        
                        <div class="match-reason" style="margin: 10px 0; padding: 10px; background: #f0f8ff; border-radius: 10px; font-size: 0.9rem;">
                            <strong>💡 ${rec.match_reason}</strong>
                        </div>
                        
                        <div class="drink-meta">
                            <span><i class="fas fa-clock"></i> ${drink.preparation_time} min</span>
                            <span><i class="fas fa-bolt"></i> ${drink.caffeine_level}</span>
                            <span><i class="fas fa-star"></i> ${drink.popularity_score}/100</span>
                        </div>
                        
                        <div style="margin-top: 15px; display: flex; gap: 10px;">
                            <button class="btn-secondary" onclick="saveFavorite(${drink.id})">
                                <i class="fas fa-heart"></i> Simpan
                            </button>
                            <button class="btn-primary" onclick="orderDrink(${drink.id})">
                                <i class="fas fa-shopping-cart"></i> Pesan Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        resultsContainer.innerHTML = html;
    }
    
    function getScoreColor(score) {
        if (score >= 80) return '#4CAF50'; // Green
        if (score >= 60) return '#FF9800'; // Orange
        return '#f44336'; // Red
    }
    
    // Confetti animation
    function showConfetti() {
        for (let i = 0; i < 100; i++) {
            createConfetti();
        }
    }
    
    function createConfetti() {
        const confetti = document.createElement('div');
        confetti.className = 'confetti';
        confetti.style.left = Math.random() * 100 + 'vw';
        confetti.style.backgroundColor = getRandomColor();
        confetti.style.width = Math.random() * 10 + 5 + 'px';
        confetti.style.height = Math.random() * 10 + 5 + 'px';
        
        document.body.appendChild(confetti);
        
        setTimeout(() => {
            confetti.remove();
        }, 5000);
    }
    
    function getRandomColor() {
        const colors = ['#FFD700', '#FF6B6B', '#4ECDC4', '#FF8E53', '#9B59B6'];
        return colors[Math.floor(Math.random() * colors.length)];
    }
});

// Global functions
async function saveFavorite(drinkId) {
    try {
        const response = await fetch('<?php echo BASE_URL; ?>api/save_preference.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ drink_id: drinkId, action: 'save' })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('✅ Berhasil disimpan ke favorit!');
        } else {
            alert('⚠️ ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

async function orderDrink(drinkId) {
    if (!confirm('Apakah Anda yakin ingin memesan minuman ini? Stok akan berkurang.')) {
        return;
    }
    
    try {
        const response = await fetch('<?php echo BASE_URL; ?>api/update_stock.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ drink_id: drinkId, action: 'order' })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('✅ Pesanan berhasil! Stok tersisa: ' + data.remaining_stock);
        } else {
            alert('⚠️ ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}