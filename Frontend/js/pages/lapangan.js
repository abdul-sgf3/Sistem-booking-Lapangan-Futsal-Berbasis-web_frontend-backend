// js/pages/lapangan.js
import { getDaftarLapangan } from '../services/lapanganService.js';

document.addEventListener('DOMContentLoaded', async function() {
    const container = document.getElementById('lapangan-container');
    if (!container) return;

    // 1. TAMPILKAN SKELETON LOADING (Hanya ini yang akan tampil)
    let skeletonHTML = '';
    for (let i = 0; i < 3; i++) { // Tampilkan 3 kotak skeleton
        skeletonHTML += `
            <div class="sl-card">
                <div class="sl-skeleton sl-img"></div>
                <div class="sl-content">
                    <div class="sl-skeleton sl-title"></div>
                    <div class="sl-skeleton sl-text"></div>
                    <div class="sl-skeleton sl-text short"></div>
                    <div class="sl-skeleton sl-btn"></div>
                </div>
            </div>
        `;
    }
    container.innerHTML = skeletonHTML;

    // 2. FETCH DATA (Hanya untuk mengecek API, tapi tidak dirender ke Card)
    const result = await getDaftarLapangan();
    console.log("Data API berhasil diambil:", result); // Cek di F12 Console

    /* 
       ======================================================
       CATATAN UNTUK DEVELOPER:
       Kode untuk merender Card di bawah ini sengaja di-comment
       karena menunggu desain final (Figma) dari tim UI/UX.
       ======================================================
    */

    /*
    if (result.status === 'success' && result.data.length > 0) {
        container.innerHTML = result.data.map(function(lapangan) {
            // ... (Logika render card)
        }).join('');
    }
    */
});