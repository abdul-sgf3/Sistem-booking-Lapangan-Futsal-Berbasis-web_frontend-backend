// KONFIGURASI URL API OTOMATIS
const API_BASE = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
    ? 'http://localhost/vincent-arena/Backend/API'
    : 'https://vincent-arena.infinityfreeapp.com/Backend/API';

// Fungsi ini diekspor agar bisa dipanggil oleh file register.js (UI)
export async function daftarAkun(dataRegister) {
    const response = await fetch(API_BASE + '/auth/register.php', {
        method: 'POST', 
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(dataRegister)
    });
    
    const responseText = await response.text();
    return responseText;
}