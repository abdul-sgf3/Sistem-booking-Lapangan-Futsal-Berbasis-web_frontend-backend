// ==========================================
// js/network-skeleton.js
// Mengatur Skeleton Loading & Deteksi Jaringan
// ==========================================

// 1. Fungsi Memunculkan Skeleton
window.showSkeleton = function(containerId, type = 'card') {
    const container = document.getElementById(containerId);
    if (!container) return;

    let html = '';
    
    if (type === 'card') {
        // Template untuk List Lapangan / Riwayat
        for (let i = 0; i < 3; i++) {
            html += `
            <div class="sk-card">
                <div class="skeleton-box sk-card-img"></div>
                <div class="sk-card-content">
                    <div class="skeleton-box sk-title"></div>
                    <div class="skeleton-box sk-text"></div>
                    <div class="skeleton-box sk-text short"></div>
                    <div class="skeleton-box sk-btn"></div>
                </div>
            </div>`;
        }
    } else if (type === 'grid') {
        // Template untuk Makanan / Galeri
        for (let i = 0; i < 4; i++) {
            html += `
            <div class="sk-grid-item">
                <div class="skeleton-box sk-grid-img"></div>
                <div class="skeleton-box sk-grid-title"></div>
                <div class="skeleton-box sk-grid-price"></div>
            </div>`;
        }
    }

    container.innerHTML = html;
    container.style.display = 'block';
};

// 2. Fungsi Menghilangkan Skeleton & Memunculkan Konten Asli
window.hideSkeleton = function(skeletonId, contentId) {
    const skeleton = document.getElementById(skeletonId);
    const content = document.getElementById(contentId);
    
    if (skeleton) skeleton.style.display = 'none';
    if (content) {
        content.classList.remove('hidden-loading');
        content.style.display = 'flex'; // Atau 'grid' tergantung class aslinya
    }
};

// 3. Deteksi Koneksi Lambat (Slow Connection)
window.showSlowConnectionWarning = function() {
    // Buat toast notifikasi
    const toast = document.createElement('div');
    toast.id = 'slow-connection-toast';
    toast.style.cssText = `
        position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%);
        background: #FEF3C7; color: #92400E; padding: 12px 24px; border-radius: 50px;
        font-size: 14px; font-weight: 600; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000; display: flex; align-items: center; gap: 8px;
    `;
    toast.innerHTML = `
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        Koneksi Internet Lambat...
    `;
    
    // Hapus jika sudah ada
    const existing = document.getElementById('slow-connection-toast');
    if (existing) existing.remove();
    
    document.body.appendChild(toast);
};

window.hideSlowConnectionWarning = function() {
    const existing = document.getElementById('slow-connection-toast');
    if (existing) existing.remove();
};