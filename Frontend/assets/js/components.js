// assets/js/components.js
document.addEventListener('DOMContentLoaded', function() {
    const navbarEl = document.getElementById('navbar');
    const footerEl = document.getElementById('footer');
    
    if (navbarEl) navbarEl.innerHTML = renderNavbar();
    if (footerEl) footerEl.innerHTML = renderFooter();
});

function renderNavbar() {
    const isLoggedIn = localStorage.getItem('user_id');
    const nama = localStorage.getItem('nama') || 'Mifa'; 
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    
    function navClass(page) {
        return currentPage === page ? 'vsq-nav-link active' : 'vsq-nav-link';
    }
    
    return `
    <header class="vsq-header">
        <div class="vsq-header-inner">
            <a href="index.html" class="vsq-logo">
                <span class="vsq-logo-title">Vincent'SQ</span>
                <span class="vsq-logo-sub">SPORTS ARENA</span>
            </a>
            <nav class="vsq-nav-menu">
                <a href="index.html" class="${navClass('index.html')}">Beranda</a>
                <a href="lapangan.html" class="${navClass('lapangan.html')}">Booking Lapangan</a>
                <a href="riwayat_booking.html" class="${navClass('riwayat_booking.html')}">Riwayat Booking</a>
                <a href="makanan.html" class="${navClass('makanan.html')}">Makanan Minuman</a>
            </nav>
            <div class="vsq-nav-right">
                <button class="vsq-notif-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                </button>
                ${isLoggedIn ? `
                    <div onclick="logout()" class="vsq-profile-box">
                        <span class="vsq-profile-name">Hai ${nama}</span>
                        <div class="vsq-profile-avatar">${nama.charAt(0).toUpperCase()}</div>
                    </div>
                ` : `
                    <a href="login.html" class="vsq-btn-login">Login</a>
                `}
            </div>
        </div>
    </header>`;
}

function renderFooter() {
    return `<footer class="vsq-footer" style="background:#131B2E; color:white; padding:40px; text-align:center; margin-top:40px;">
        <p>&copy; 2026 Vincent'SQ Arena. All Right Reserved</p>
    </footer>`;
}

function logout() {
    localStorage.clear();
    window.location.href = 'login.html';
}