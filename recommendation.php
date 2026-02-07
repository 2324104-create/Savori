<?php
// File ini HANYA untuk form input
?>

<div class="container py-4">
    <!-- Page Header -->
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold text-brown">✨ Rekomendasi Kopi</h1>
        <p class="lead">Pilih preferensi Anda untuk mendapatkan rekomendasi kopi yang sempurna</p>
    </div>
    
    <!-- Recommendation Form -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-brown text-white">
                    <h4 class="mb-0"><i class="fas fa-sliders-h"></i> Pilih Preferensi Anda</h4>
                </div>
                
                <div class="card-body p-4">
                    <form method="POST" action="?page=recommendation_result" id="preferenceForm">
                        <!-- Mood Selection -->
                        <div class="mb-5">
                            <h5 class="mb-3"><i class="fas fa-smile text-warning"></i> 1. Bagaimana mood Anda hari ini?</h5>
                            <div class="row g-2" id="moodSelection">
                                <?php 
                                $moods = [
                                    'energik' => ['⚡', 'Energik'],
                                    'santai' => ['😌', 'Santai'],
                                    'stres' => ['😫', 'Stres'],
                                    'bahagia' => ['😊', 'Bahagia'],
                                    'sedih' => ['😢', 'Sedih']
                                ];
                                foreach ($moods as $key => $mood):
                                ?>
                                <div class="col">
                                    <input type="radio" name="mood" value="<?php echo $key; ?>" id="mood_<?php echo $key; ?>" class="d-none" required>
                                    <label for="mood_<?php echo $key; ?>" class="pref-option" data-value="<?php echo $key; ?>">
                                        <span class="pref-emoji"><?php echo $mood[0]; ?></span>
                                        <span class="pref-text"><?php echo $mood[1]; ?></span>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="form-text text-danger mt-2" id="moodError"></div>
                        </div>
                        
                        <!-- Weather Selection -->
                        <div class="mb-5">
                            <h5 class="mb-3"><i class="fas fa-cloud-sun text-info"></i> 2. Bagaimana cuaca di tempat Anda?</h5>
                            <div class="row g-2" id="weatherSelection">
                                <?php 
                                $weathers = [
                                    'panas' => ['🔥', 'Panas'],
                                    'dingin' => ['❄️', 'Dingin'],
                                    'hujan' => ['🌧️', 'Hujan'],
                                    'cerah' => ['☀️', 'Cerah']
                                ];
                                foreach ($weathers as $key => $weather):
                                ?>
                                <div class="col">
                                    <input type="radio" name="weather" value="<?php echo $key; ?>" id="weather_<?php echo $key; ?>" class="d-none" required>
                                    <label for="weather_<?php echo $key; ?>" class="pref-option" data-value="<?php echo $key; ?>">
                                        <span class="pref-emoji"><?php echo $weather[0]; ?></span>
                                        <span class="pref-text"><?php echo $weather[1]; ?></span>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="form-text text-danger mt-2" id="weatherError"></div>
                        </div>
                        
                        <!-- Time Selection -->
                        <div class="mb-5">
                            <h5 class="mb-3"><i class="fas fa-clock text-primary"></i> 3. Kapan Anda ingin menikmati kopi?</h5>
                            <div class="row g-2" id="timeSelection">
                                <?php 
                                $times = [
                                    'pagi' => ['🌅', 'Pagi'],
                                    'siang' => ['☀️', 'Siang'],
                                    'sore' => ['🌇', 'Sore'],
                                    'malam' => ['🌙', 'Malam']
                                ];
                                foreach ($times as $key => $time):
                                ?>
                                <div class="col">
                                    <input type="radio" name="time" value="<?php echo $key; ?>" id="time_<?php echo $key; ?>" class="d-none" required>
                                    <label for="time_<?php echo $key; ?>" class="pref-option" data-value="<?php echo $key; ?>">
                                        <span class="pref-emoji"><?php echo $time[0]; ?></span>
                                        <span class="pref-text"><?php echo $time[1]; ?></span>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="form-text text-danger mt-2" id="timeError"></div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-brown btn-lg px-5" id="submitBtn">
                                <i class="fas fa-magic me-2"></i> Dapatkan Rekomendasi
                            </button>
                            <p class="text-muted mt-2">
                                <small><i class="fas fa-info-circle"></i> Sistem akan menganalisis preferensi Anda</small>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="row mt-4">
                <div class="col-md-4 mb-3">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-coffee fa-2x text-brown mb-2"></i>
                            <h5>30+</h5>
                            <p class="text-muted mb-0">Jenis Kopi</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-bolt fa-2x text-warning mb-2"></i>
                            <h5>3 Detik</h5>
                            <p class="text-muted mb-0">Analisis Cepat</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-star fa-2x text-info mb-2"></i>
                            <h5>95%</h5>
                            <p class="text-muted mb-0">Kepuasan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.pref-option {
    display: block;
    padding: 20px 10px;
    border: 3px solid #dee2e6;
    border-radius: 15px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    height: 100%;
    background: white;
    user-select: none;
}

.pref-option:hover {
    border-color: #8B4513;
    background-color: rgba(139, 69, 19, 0.05);
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.pref-option.active {
    border-color: #8B4513;
    background-color: rgba(139, 69, 19, 0.1);
    transform: translateY(-3px);
    box-shadow: 0 3px 10px rgba(139, 69, 19, 0.2);
}

.pref-emoji {
    font-size: 2.5rem;
    display: block;
    margin-bottom: 10px;
    transition: transform 0.3s;
}

.pref-option:hover .pref-emoji {
    transform: scale(1.2);
}

.pref-text {
    font-weight: 600;
    color: #333;
    font-size: 1.1rem;
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

.text-brown {
    color: #8B4513 !important;
}

.bg-brown {
    background-color: #8B4513 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle preference selection
    const options = document.querySelectorAll('.pref-option');
    
    options.forEach(option => {
        // Set initial active state
        const radio = option.previousElementSibling;
        if (radio.checked) {
            option.classList.add('active');
        }
        
        // Add click event
        option.addEventListener('click', function() {
            const radio = this.previousElementSibling;
            const groupName = radio.name;
            
            // Uncheck all radios in this group
            document.querySelectorAll(`input[name="${groupName}"]`).forEach(r => {
                r.checked = false;
            });
            
            // Unactivate all options in this group
            const parent = this.closest('.row');
            parent.querySelectorAll('.pref-option').forEach(opt => {
                opt.classList.remove('active');
            });
            
            // Check this radio and activate this option
            radio.checked = true;
            this.classList.add('active');
            
            // Clear error
            clearError(groupName);
        });
    });
    
    // Form validation
    const form = document.getElementById('preferenceForm');
    form.addEventListener('submit', function(e) {
        const moodSelected = document.querySelector('input[name="mood"]:checked');
        const weatherSelected = document.querySelector('input[name="weather"]:checked');
        const timeSelected = document.querySelector('input[name="time"]:checked');
        
        let isValid = true;
        
        if (!moodSelected) {
            document.getElementById('moodError').textContent = 'Pilih mood Anda';
            isValid = false;
        } else {
            document.getElementById('moodError').textContent = '';
        }
        
        if (!weatherSelected) {
            document.getElementById('weatherError').textContent = 'Pilih cuaca di tempat Anda';
            isValid = false;
        } else {
            document.getElementById('weatherError').textContent = '';
        }
        
        if (!timeSelected) {
            document.getElementById('timeError').textContent = 'Pilih waktu menikmati kopi';
            isValid = false;
        } else {
            document.getElementById('timeError').textContent = '';
        }
        
        if (!isValid) {
            e.preventDefault();
            showAlert('Harap pilih semua preferensi terlebih dahulu!', 'danger');
        }
    });
    
    function clearError(fieldName) {
        const errorElement = document.getElementById(fieldName + 'Error');
        if (errorElement) {
            errorElement.textContent = '';
        }
    }
    
    function showAlert(message, type = 'success') {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
        alert.style.zIndex = '9999';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alert);
        
        setTimeout(() => {
            alert.remove();
        }, 3000);
    }
    
    // Auto-select demo data for testing
    setTimeout(() => {
        if (!document.querySelector('input[name="mood"]:checked')) {
            // Auto select first option in each group for demo
            const firstOptions = document.querySelectorAll('.pref-option:nth-child(2)');
            firstOptions.forEach(option => {
                option.click();
            });
        }
    }, 1000);
});
</script>