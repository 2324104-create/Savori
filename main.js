// Main JavaScript for Savori
document.addEventListener('DOMContentLoaded', function() {
    console.log('Savori Coffee App loaded ☕');
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
    
    // Mobile menu toggle
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            document.querySelector('.nav-menu').classList.toggle('show');
        });
    }
    
    // Back to top button
    const backToTop = document.getElementById('backToTop');
    if (backToTop) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTop.style.display = 'block';
            } else {
                backToTop.style.display = 'none';
            }
        });
        
        backToTop.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
    
    // Coffee animation
    createCoffeeAnimation();
    
    // Load user preferences if logged in
    if (typeof userId !== 'undefined') {
        loadUserPreferences();
    }
    
    // Add loading animation to all buttons
    document.querySelectorAll('button[type="submit"]').forEach(button => {
        button.addEventListener('click', function() {
            if (this.type === 'submit') {
                this.classList.add('loading');
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            }
        });
    });
});

// Coffee floating animation
function createCoffeeAnimation() {
    const container = document.querySelector('.floating-coffee-animation');
    if (!container) return;
    
    for (let i = 0; i < 10; i++) {
        const bean = document.createElement('div');
        bean.className = 'coffee-bean';
        bean.style.left = Math.random() * 100 + 'vw';
        bean.style.animationDelay = Math.random() * 5 + 's';
        bean.style.width = Math.random() * 10 + 5 + 'px';
        bean.style.height = Math.random() * 5 + 3 + 'px';
        container.appendChild(bean);
    }
}

// Load user preferences
function loadUserPreferences() {
    fetch('api/get_preferences.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.preferences) {
                // Auto-fill recommendation form with previous preferences
                const prefs = data.preferences;
                if (prefs.mood) {
                    const moodRadio = document.querySelector(`input[name="mood"][value="${prefs.mood}"]`);
                    if (moodRadio) moodRadio.checked = true;
                }
                if (prefs.weather) {
                    const weatherRadio = document.querySelector(`input[name="weather"][value="${prefs.weather}"]`);
                    if (weatherRadio) weatherRadio.checked = true;
                }
                if (prefs.time_of_day) {
                    const timeRadio = document.querySelector(`input[name="time_of_day"][value="${prefs.time_of_day}"]`);
                    if (timeRadio) timeRadio.checked = true;
                }
            }
        })
        .catch(error => console.log('No preferences found'));
}

// Global functions
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        ${message}
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

function showLoading(show = true) {
    const loading = document.getElementById('globalLoading');
    if (loading) {
        loading.style.display = show ? 'flex' : 'none';
    }
}

// Order drink function
window.orderDrink = function(drinkId) {
    if (!confirm('Pesan kopi ini? Stok akan berkurang.')) return;
    
    showLoading(true);
    
    fetch('api/order_drink.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({drink_id: drinkId})
    })
    .then(response => response.json())
    .then(data => {
        showLoading(false);
        if (data.success) {
            showNotification(`✅ Pesanan berhasil! Stok tersisa: ${data.remaining_stock}`);
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification(`⚠️ ${data.message}`, 'error');
        }
    })
    .catch(error => {
        showLoading(false);
        showNotification('❌ Terjadi kesalahan', 'error');
        console.error('Error:', error);
    });
}

// Add to favorite function
window.addToFavorite = function(drinkId) {
    fetch('api/save_favorite.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({drink_id: drinkId})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const action = data.action;
            const message = action === 'add' ? '✅ Ditambahkan ke favorit' : '❌ Dihapus dari favorit';
            showNotification(message);
            
            // Update button icon
            const btn = event.target.closest('button');
            if (btn) {
                const icon = btn.querySelector('i');
                if (icon) {
                    icon.className = action === 'add' ? 'fas fa-heart' : 'far fa-heart';
                }
            }
        } else {
            showNotification(`⚠️ ${data.message}`, 'error');
        }
    })
    .catch(error => {
        showNotification('❌ Terjadi kesalahan', 'error');
        console.error('Error:', error);
    });
}

// Confetti effect
window.showConfetti = function() {
    const colors = ['#ff0000', '#00ff00', '#0000ff', '#ffff00', '#ff00ff', '#00ffff'];
    
    for (let i = 0; i < 100; i++) {
        const confetti = document.createElement('div');
        confetti.className = 'confetti';
        confetti.style.left = Math.random() * 100 + 'vw';
        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.width = Math.random() * 10 + 5 + 'px';
        confetti.style.height = Math.random() * 10 + 5 + 'px';
        confetti.style.animationDuration = Math.random() * 3 + 2 + 's';
        
        document.body.appendChild(confetti);
        
        setTimeout(() => {
            confetti.remove();
        }, 5000);
    }
}

// Form auto-save
function setupAutoSave(formId, interval = 3000) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    let timeout;
    form.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            saveFormData(formId);
        }, interval);
    });
}

function saveFormData(formId) {
    const form = document.getElementById(formId);
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    
    localStorage.setItem(`savori_form_${formId}`, JSON.stringify(data));
    console.log('Form data saved:', data);
}

function loadFormData(formId) {
    const saved = localStorage.getItem(`savori_form_${formId}`);
    if (saved) {
        const data = JSON.parse(saved);
        const form = document.getElementById(formId);
        
        Object.keys(data).forEach(key => {
            const input = form.querySelector(`[name="${key}"]`);
            if (input) {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    if (Array.isArray(data[key])) {
                        data[key].forEach(val => {
                            const radio = form.querySelector(`[name="${key}"][value="${val}"]`);
                            if (radio) radio.checked = true;
                        });
                    } else {
                        const radio = form.querySelector(`[name="${key}"][value="${data[key]}"]`);
                        if (radio) radio.checked = true;
                    }
                } else {
                    input.value = data[key];
                }
            }
        });
    }
}

// Initialize auto-save for recommendation form
if (document.getElementById('recommendationForm')) {
    setupAutoSave('recommendationForm');
    loadFormData('recommendationForm');
}

// Add CSS for animations
const style = document.createElement('style');
style.textContent = `
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: #28a745;
        color: white;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        z-index: 9999;
        transform: translateX(150%);
        transition: transform 0.3s ease;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .notification.show {
        transform: translateX(0);
    }
    .notification-error {
        background: #dc3545;
    }
    .coffee-bean {
        position: fixed;
        background: #8B4513;
        border-radius: 50%;
        opacity: 0.3;
        pointer-events: none;
        z-index: -1;
        animation: float 10s linear infinite;
    }
    @keyframes float {
        0% { top: -50px; transform: rotate(0deg); }
        100% { top: 100vh; transform: rotate(360deg); }
    }
    .confetti {
        position: fixed;
        pointer-events: none;
        z-index: 9998;
        animation: confettiFall 5s linear forwards;
    }
    @keyframes confettiFall {
        0% { top: -100px; transform: rotate(0deg); }
        100% { top: 100vh; transform: rotate(360deg); }
    }
    .loading {
        position: relative;
        color: transparent !important;
    }
    .loading::after {
        content: '';
        position: absolute;
        left: 50%;
        top: 50%;
        width: 20px;
        height: 20px;
        margin: -10px 0 0 -10px;
        border: 2px solid #fff;
        border-top-color: transparent;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);