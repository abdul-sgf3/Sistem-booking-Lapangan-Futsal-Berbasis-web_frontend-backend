// ============================================
// js/auth.js
// Helper terpusat untuk Autentikasi & Role
// VincentSQ Arena
// ============================================

const USER_KEYS = ['vsq_user', 'user', 'userData', 'user_data', 'currentUser'];

// 1. Mengambil data user yang tersimpan di localStorage
export function getCachedUser() {
    for (const key of USER_KEYS) {
        try {
            const raw = localStorage.getItem(key);
            if (!raw) continue;
            const parsed = JSON.parse(raw);
            if (parsed && typeof parsed === 'object') return parsed;
        } catch (e) {}
    }
    return null;
}

// 2. Mengambil role user ('admin' atau 'user')
export function getUserRole() {
    const u = getCachedUser();
    if (!u) return null;
    return u.role || u.user_role || u.level || null;
}

// 3. Cek apakah user sudah login
export function isLoggedIn() {
    return !!getCachedUser();
}

// 4. Cek apakah yang login adalah Admin
export function isAdmin() {
    return getUserRole() === 'admin';
}

// 5. Cek apakah yang login adalah User biasa
export function isUser() {
    return getUserRole() === 'user';
}

// 6. Menyimpan data user (dipanggil saat login sukses)
export function saveUser(user) {
    if (!user) return;
    localStorage.setItem('vsq_user', JSON.stringify(user));
    if (user.id) localStorage.setItem('user_id', user.id);
    if (user.nama || user.name) localStorage.setItem('nama', user.nama || user.name);
    if (user.email) localStorage.setItem('user_email', user.email);
}

// 7. Menghapus data user (dipanggil saat logout)
export function clearUser() {
    USER_KEYS.forEach(k => localStorage.removeItem(k));
    localStorage.removeItem('user_id');
    localStorage.removeItem('nama');
    localStorage.removeItem('user_email');
}

// 8. Menentukan URL Redirect setelah login
export function getRedirectAfterLogin(user, savedRedirect = null) {
    if (!user) return 'login.html';
    const role = user.role || user.user_role || user.level || 'user';
    
    if (role === 'admin') {
        return 'admin/dashboard.html'; // Admin selalu ke dashboard admin
    }
    
    // User biasa: pakai saved redirect kalau ada (misal terpaksa login karena mau booking)
    if (savedRedirect) return savedRedirect;
    return 'index.html';
}

// 9. Proteksi Halaman: Wajib Login (untuk halaman user)
export function requireLogin() {
    if (!isLoggedIn()) {
        sessionStorage.setItem('vsq_redirect', location.pathname.split('/').pop() + location.search);
        window.location.href = 'login.html';
        return false;
    }
    return true;
}

// 10. Proteksi Halaman: Wajib Admin (untuk halaman admin)
export function requireAdmin() {
    if (!isLoggedIn()) {
        window.location.href = '../login.html';
        return false;
    }
    if (!isAdmin()) {
        alert('Akses ditolak. Halaman ini hanya untuk admin.');
        window.location.href = '../index.html';
        return false;
    }
    return true;
}