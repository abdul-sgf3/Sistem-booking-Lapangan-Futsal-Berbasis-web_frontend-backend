// ===== api.js - helper API bersama =====
const API_BASE = (location.hostname.includes('localhost') || location.hostname.includes('127.0.0.1'))
    ? 'http://localhost/vincent-arena/Backend/API'
    : 'https://sistem-booking-lapangan-futsal-berbasis-webfron-production.up.railway.app/API';

// Selalu mengembalikan: { ok, status, message, data, networkError, timeout }
async function apiPost(path, body, timeoutMs) {
    const ctrl = new AbortController();
    const timer = setTimeout(() => ctrl.abort(), timeoutMs || 25000);
    let res;
    try {
        res = await fetch(API_BASE + path, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            mode: 'cors',
            credentials: 'include', // wajib: BE memakai $_SESSION
            body: JSON.stringify(body || {}),
            signal: ctrl.signal
        });
    } catch (e) {
        clearTimeout(timer);
        const isTimeout = e && e.name === 'AbortError';
        return { ok: false, status: 0, message: '', data: {}, networkError: !isTimeout, timeout: isTimeout };
    }
    clearTimeout(timer);
    let json = {};
    try { json = await res.json(); } catch (e) { /* bukan JSON */ }
    return { ok: res.ok && json.status === 'success', status: res.status,
             message: json.message || '', data: json.data || {}, networkError: false, timeout: false };
}

// Pesan error umum untuk kegagalan koneksi
function netMsg(r) {
    if (r.timeout) return 'Server terlalu lama merespons. Silakan coba lagi.';
    if (r.networkError) return 'Tidak dapat terhubung ke server. Periksa koneksi Anda.';
    return '';
}
