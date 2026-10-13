// ============================================
// network.js - Deteksi Status Koneksi Internet
// Untuk Vincent SQ Arena
// ============================================

(function() {
    'use strict';
    
    // ===== FUNGSI: Cek Koneksi =====
    function checkConnection() {
        return navigator.onLine;
    }
    
    // ===== FUNGSI: Tampilkan Overlay Offline =====
    function showOfflineOverlay() {
        const overlay = document.getElementById('network-overlay');
        if (overlay) {
            overlay.style.display = 'flex';
            setTimeout(function() {
                overlay.style.opacity = '1';
            }, 10);
        }
    }
    
    // ===== FUNGSI: Sembunyikan Overlay =====
    function hideOfflineOverlay() {
        const overlay = document.getElementById('network-overlay');
        if (overlay) {
            overlay.style.opacity = '0';
            setTimeout(function() {
                overlay.style.display = 'none';
            }, 300);
        }
    }
    
    // ===== FUNGSI: Toast Notifikasi (Global) =====
    window.showToast = function(pesan, tipe, durasi) {
        tipe = tipe || 'info';
        durasi = durasi || 3000;
        
        const container = document.getElementById('toast-container');
        if (!container) return;
        
        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };
        
        const toast = document.createElement('div');
        toast.className = 'toast ' + tipe;
        toast.innerHTML = 
            '<span class="toast-icon">' + (icons[tipe] || '') + '</span>' +
            '<span class="toast-message">' + pesan + '</span>' +
            '<button class="toast-close" onclick="this.parentElement.remove()">×</button>';
        
        container.appendChild(toast);
        setTimeout(function() { toast.classList.add('show'); }, 10);
        
        setTimeout(function() {
            toast.classList.remove('show');
            setTimeout(function() { toast.remove(); }, 300);
        }, durasi);
    };
    
    // ===== FUNGSI: Retry Koneksi (Global) =====
    window.retryConnection = function() {
        if (navigator.onLine) {
            hideOfflineOverlay();
            showToast('Koneksi berhasil dipulihkan!', 'success');
        } else {
            showToast('Masih offline. Cek koneksi Anda.', 'warning');
        }
    };
    
    // ===== EVENT: Saat Offline =====
    window.addEventListener('offline', function() {
        console.log('🔴 Offline terdeteksi');
        showOfflineOverlay();
    });
    
    // ===== EVENT: Saat Online Kembali =====
    window.addEventListener('online', function() {
        console.log('🟢 Online kembali');
        hideOfflineOverlay();
        showToast('Koneksi kembali!', 'success');
    });
    
    // ===== CEK SAAT HALAMAN DIMUAT =====
    document.addEventListener('DOMContentLoaded', function() {
        if (!navigator.onLine) {
            showOfflineOverlay();
        }
    });
    
})();