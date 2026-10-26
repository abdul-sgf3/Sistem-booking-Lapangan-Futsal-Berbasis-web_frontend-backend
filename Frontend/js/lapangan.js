// js/pages/lapangan.js

// IMPORT DEPENDENCY (SMART DEPENDENCY)
import { getDaftarLapangan } from '../services/lapanganService.js';

document.addEventListener('DOMContentLoaded', async function() {
    const container = document.getElementById('lapangan-container');
    if (!container) return;

    // 1. TAMPILKAN SKELETON LOADING
    // Kita tampilkan 3 kotak skeleton sebagai placeholder
    let skeletonHTML = '';
    for (let i = 0; i < 3; i++) {
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

    // 2. FETCH DATA DARI API BE
    const result = await getDaftarLapangan();

    // Jika API error atau data kosong
    if (result.status !== 'success' || !result.data) {
        container.innerHTML = '<p style="text-align:center; grid-column: 1/-1; color:#888;">Gagal memuat data lapangan.</p>';
        return;
    }

    const dataLapangan = result.data;

    if (dataLapangan.length === 0) {
        container.innerHTML = '<p style="text-align:center; grid-column: 1/-1; color:#888;">Belum ada lapangan tersedia.</p>';
        return;
    }

    // 3. GANTI SKELETON DENGAN DATA ASLI
    // Kita gunakan .map() untuk membuat HTML dari data JSON Backend
    container.innerHTML = dataLapangan.map(function(lapangan) {
        // Tentukan badge VIP / Reguler
        const isVIP = lapangan.harga_per_jam >= 200000;
        const badgeText = isVIP ? 'VIP' : 'Reguler';
        const badgeClass = isVIP ? 'badge-vip' : 'badge-regular';

        // Sesuaikan URL gambar. BE ngirim 'foto_url', pastikan path-nya benar.
        // Jika kosong, pakai gambar default.
        let imgUrl = lapangan.foto_url; 
        if (imgUrl) {
             // Karena di kode Anda sebelumnya ada masalah path, kita sesuaikan
             imgUrl = imgUrl.replace('/Futsal_Vincent_SQ/Frontend/', '/vincent-arena/Frontend/');
        } else {
             imgUrl = '../assets/images/default-lapangan.jpg';
        }

        // Format Rupiah
        const hargaFormatted = new Intl.NumberFormat('id-ID').format(lapangan.harga_per_jam);

        // Kembalikan HTML Card
        return `
            <article class="vsq-card">
                <div class="vsq-card-img" style="background-image: url('${imgUrl}');"></div>
                <div class="vsq-card-body">
                    <div class="vsq-card-header">
                        <h3>${lapangan.nama_lapangan}</h3>
                        <span class="vsq-card-badge ${badgeClass}">${badgeText}</span>
                    </div>
                    <div class="vsq-card-price">Rp ${hargaFormatted} <span>/ jam</span></div>
                    <div class="vsq-card-footer">
                        <a href="detail_lapangan.html?id=${lapangan.id}" class="vsq-btn-booking">Booking Sekarang</a>
                    </div>
                </div>
            </article>
        `;
    }).join('');
});