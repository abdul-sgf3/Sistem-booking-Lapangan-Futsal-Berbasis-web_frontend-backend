// components.js - Navbar & Footer Dinamis

document.addEventListener('DOMContentLoaded', function() {
    const navbarEl = document.getElementById('navbar');
    const footerEl = document.getElementById('footer');
    
    if (navbarEl) navbarEl.innerHTML = renderNavbar();
    if (footerEl) footerEl.innerHTML = renderFooter();
});

function renderNavbar() {
    const isLoggedIn = localStorage.getItem('user_id');
    const nama = localStorage.getItem('nama') || 'Pengguna';
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    
    // Helper: Cek menu aktif
    function navClass(page) {
        const base = 'px-4 py-2 rounded-lg font-medium text-[15px] transition-all';
        return currentPage === page 
            ? base + ' bg-navy text-white font-bold' 
            : base + ' text-slate-500 hover:text-navy';
    }
    
    return `
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-6 h-[72px] flex items-center justify-between">
            <div class="flex items-center gap-10">
                <a href="index.html" class="flex flex-col leading-none">
                    <span class="text-2xl font-extrabold text-navy">Vincent'SQ</span>
                    <span class="text-[10px] font-extrabold text-accent tracking-[0.18em] mt-1">SPORTS ARENA</span>
                </a>
                <nav class="hidden lg:flex items-center gap-1">
                    <a href="index.html" class="${navClass('index.html')}">Beranda</a>
                    <a href="lapangan.html" class="${navClass('lapangan.html')}">Booking Lapangan</a>
                    <a href="riwayat_booking.html" class="${navClass('riwayat_booking.html')}">Riwayat Booking</a>
                    <a href="makanan.html" class="${navClass('makanan.html')}">Makanan Minuman</a>
                </nav>
            </div>
            <div class="flex items-center gap-3">
                ${isLoggedIn ? `
                    <span class="hidden sm:inline text-sm font-semibold text-navy">Hai, ${nama}!</span>
                    <span class="w-9 h-9 rounded-full bg-navy text-white grid place-items-center text-sm font-bold">
                        ${nama.charAt(0).toUpperCase()}
                    </span>
                    <button onclick="logout()" class="bg-accent text-white px-5 py-2.5 rounded-lg text-sm font-bold hover:bg-red-700 transition">Logout</button>
                ` : `
                    <a href="login.html" class="bg-blue-500 text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-blue-700 transition">Login</a>
                    <a href="register.html" class="bg-accent text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-red-700 transition">Register</a>
                `}
            </div>
        </div>
    </header>`;
}

function renderFooter() {
    return `
    <footer class="bg-navy text-white mt-16">
        <div class="max-w-6xl mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <div>
                    <h2 class="text-2xl font-extrabold">Vincent'<span class="text-accent">SQ</span> Arena</h2>
                </div>
                <div>
                    <ul class="space-y-3 text-sm font-semibold">
                        <li><a href="index.html" class="hover:text-accent">Beranda</a></li>
                        <li><a href="lapangan.html" class="hover:text-accent">Booking Lapangan</a></li>
                        <li><a href="riwayat_booking.html" class="hover:text-accent">Riwayat Booking</a></li>
                        <li><a href="makanan.html" class="hover:text-accent">Makanan & Minuman</a></li>
                    </ul>
                </div>
                <div>
                    <ul class="space-y-4 text-sm font-semibold">
                        <li>📞 WA: 0893-3283-4839</li>
                        <li>📧 Email: vincenttt3@gmail.com</li>
                        <li>📍 Lokasi: Tuparev, Kota Cirebon</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-slate-700 mt-10 pt-6 text-center text-sm text-slate-400">
                @ 2026 Vincent'SQ Arena. All Right Reserved
            </div>
        </div>
    </footer>`;
}

function logout() {
    localStorage.removeItem('user_id');
    localStorage.removeItem('nama');
    localStorage.removeItem('email');
    window.location.href = 'login.html';
}