// js/services/lapanganService.js

// Deteksi otomatis localhost atau hosting
const API_BASE = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
    ? 'http://localhost/vincent-arena/Backend/API'
    : 'https://vincent-arena.infinityfreeapp.com/Backend/API';

// Fungsi mengambil daftar lapangan
export async function getDaftarLapangan() {
    try {
        const response = await fetch(API_BASE + '/public/lapangan.php', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        });
        
        const result = await response.json();
        return result;
    } catch (error) {
        console.error("Gagal mengambil data dari API:", error);
        return { status: 'error', message: 'Gagal terhubung ke server' };
    }
}