// ============================================
// validasi_booking.js - Validasi Form Booking
// Mengambil pengaturan dari API dan validasi durasi
// Untuk Vincent SQ Arena
// ============================================

(function() {
    'use strict';
    
    // Default pengaturan (kalau API gagal)
    const DEFAULT_SETTINGS = {
        jam_buka: '08:00',
        jam_tutup: '22:00',
        maksimal_durasi: 2,
        biaya_booking: 0
    };
    
    let pengaturan = DEFAULT_SETTINGS;
    
    // ===== Ambil Pengaturan dari API =====
    async function loadPengaturan() {
        try {
            const response = await fetch('/vincent-arena/Backend/API/public/pengaturan.php');
            const result = await response.json();
            
            if (result.status === 'success' && result.data) {
                pengaturan.jam_buka = result.data.jam_buka.substring(0, 5);
                pengaturan.jam_tutup = result.data.jam_tutup.substring(0, 5);
                pengaturan.maksimal_durasi = parseInt(result.data.maksimal_durasi) || 2;
                pengaturan.biaya_booking = parseInt(result.data.biaya_booking) || 0;
                
                console.log('✅ Pengaturan dimuat:', pengaturan);
            }
        } catch (err) {
            console.warn('⚠️ Gagal load pengaturan, pakai default:', err);
        }
        
        return pengaturan;
    }
    
    // ===== Validasi Tanggal =====
    function validateTanggal(tanggal) {
        if (!tanggal) return 'Tanggal harus diisi';
        
        const tglPilih = new Date(tanggal);
        const hariIni = new Date();
        hariIni.setHours(0, 0, 0, 0);
        
        if (tglPilih < hariIni) {
            return 'Tanggal tidak boleh di masa lalu';
        }
        return null;
    }
    
    // ===== Validasi Jam =====
    function validateJam(jam) {
        if (!jam) return 'Jam harus diisi';
        
        if (jam < pengaturan.jam_buka || jam > pengaturan.jam_tutup) {
            return 'Jam booking: ' + pengaturan.jam_buka + ' - ' + pengaturan.jam_tutup;
        }
        return null;
    }
    
    // ===== Validasi Durasi =====
    function validateDurasi(durasi) {
        const dur = parseInt(durasi);
        
        if (isNaN(dur) || dur < 1) {
            return 'Durasi minimal 1 jam';
        }
        
        if (dur > pengaturan.maksimal_durasi) {
            return 'Maksimal durasi ' + pengaturan.maksimal_durasi + ' jam';
        }
        return null;
    }
    
    // ===== Setup Stepper Durasi =====
    function setupStepper() {
        const stepperContainer = document.querySelector('.durasi-stepper');
        if (!stepperContainer) return;
        
        const input = stepperContainer.querySelector('input[name="durasi"]');
        const btnMinus = stepperContainer.querySelector('.btn-minus');
        const btnPlus = stepperContainer.querySelector('.btn-plus');
        
        if (!input || !btnMinus || !btnPlus) return;
        
        let durasi = parseInt(input.value) || 1;
        
        function updateDisplay() {
            input.value = durasi;
            const total = durasi * (parseInt(input.dataset.harga) || 0);
            
            const totalEl = document.getElementById('total-harga');
            if (totalEl) {
                totalEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            }
            
            // Disable tombol kalau di batas
            btnMinus.disabled = (durasi <= 1);
            btnPlus.disabled = (durasi >= pengaturan.maksimal_durasi);
        }
        
        btnMinus.addEventListener('click', function(e) {
            e.preventDefault();
            if (durasi <= 1) {
                showToast('Minimal durasi 1 jam', 'warning');
                return;
            }
            durasi--;
            updateDisplay();
        });
        
        btnPlus.addEventListener('click', function(e) {
            e.preventDefault();
            if (durasi >= pengaturan.maksimal_durasi) {
                showToast('Maksimal durasi ' + pengaturan.maksimal_durasi + ' jam', 'warning');
                return;
            }
            durasi++;
            updateDisplay();
        });
        
        updateDisplay();
    }
    
    // ===== Setup Form Booking =====
    function setupFormBooking() {
        const form = document.querySelector('form[action*="proses_booking"]') 
                  || document.querySelector('#form-booking');
        if (!form) return;
        
        form.addEventListener('submit', function(e) {
            const tanggal = form.querySelector('[name="tanggal"]')?.value || '';
            const jam = form.querySelector('[name="jam_mulai"]')?.value || '';
            const durasi = form.querySelector('[name="durasi"]')?.value || '';
            
            // Validasi
            let error = validateTanggal(tanggal) 
                     || validateJam(jam) 
                     || validateDurasi(durasi);
            
            if (error) {
                e.preventDefault();
                showToast(error, 'error');
                return false;
            }
            
            // Konfirmasi
            if (!confirm('Yakin ingin booking lapangan ini?')) {
                e.preventDefault();
                return false;
            }
            
            showToast('Memproses booking...', 'info');
        });
    }
    
    // ===== INIT =====
    document.addEventListener('DOMContentLoaded', async function() {
        await loadPengaturan();
        setupStepper();
        setupFormBooking();
    });
    
})();