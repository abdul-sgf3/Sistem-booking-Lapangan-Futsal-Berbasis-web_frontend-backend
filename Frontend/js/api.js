// ===== api.js - helper API bersama (login, lupa password, verifikasi, password baru) =====
const API_BASE = (location.hostname.includes('localhost') || location.hostname.includes('127.0.0.1'))
    ? 'http://localhost/vincent-arena/Backend/API'
    : 'https://sistem-booking-lapangan-futsal-berbasis-webfron-production.up.railway.app/API';

// Selalu mengembalikan objek: { ok, status, message, data, networkError }
async function apiPost(path, body) {
    let res;
    try {
        res = await fetch(API_BASE + path, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            mode: 'cors',
            credentials: 'include', // wajib: BE memakai $_SESSION
            body: JSON.stringify(body || {})
        });
    } catch (e) {
        return { ok: false, status: 0, message: '', data: {}, networkError: true };
    }
    let json = {};
    try { json = await res.json(); } catch (e) { /* respons bukan JSON */ }
    return {
        ok: res.ok && json.status === 'success',
        status: res.status,
        message: json.message || '',
        data: json.data || {},
        networkError: false
    };
}
