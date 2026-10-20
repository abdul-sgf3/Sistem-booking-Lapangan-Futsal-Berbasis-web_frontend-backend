document.addEventListener('DOMContentLoaded', async function() {
    const container = document.getElementById('lapangan-container');
    if (!container) return;
    
    // 1. TAMPILKAN SKELETON LOADING
    container.innerHTML = `
        <div class="sl-card">
            <div class="sl-skeleton sl-img"></div>
            <div class="sl-content">
                <div class="sl-skeleton sl-title"></div>
                <div class="sl-skeleton sl-text"></div>
                <div class="sl-skeleton sl-text short"></div>
                <div class="sl-skeleton sl-btn"></div>
            </div>
        </div>
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

    // 2. FETCH DATA DARI API BE
    try {
        const result = await fetchAPI('/public/lapangan.php');
        
        // Jika API error atau data kosong
        if (result.status !== 'success' || !result.data) {
            container.innerHTML = '<p class="loading-text">Gagal memuat data lapangan.</p>';
            return;
        }

        const data = result.data;
        
        if (data.length === 0) {
            container.innerHTML = '<p class="loading-text">Belum ada lapangan tersedia.</p>';
            return;
        }
        
        // 3. GANTI SKELETON DENGAN DATA ASLI
        container.innerHTML = data.map(function(l) {
            const isVIP = l.harga_per_jam >= 200000;
            const badge = isVIP ? 'VIP' : 'Regular';
            const badgeClass = isVIP ? 'badge-vip' : 'badge-regular';
            
            // Sesuaikan URL gambar dengan folder assets Anda
            // Perhatikan BE mengirim format path: "/Futsal_Vincent_SQ/Frontend/assets/uploads/lapangan/Lapangan1.jpeg"
            // Kita sesuaikan agar sesuai dengan folder Laragon lokal Anda
          let imgUrl = l.foto_url; 
if(imgUrl) {
    // Menyesuaikan path dari BE agar sesuai dengan folder lokal Laragon Anda
    imgUrl = imgUrl.replace('/Futsal_Vincent_SQ/Frontend/', '/vincent-arena/Frontend/');
}
            }

            return `
            <article class="vsq-card">
                <div class="vsq-card-img" style="background-image: url('${imgUrl}');"></div>
                <div class="vsq-card-body">
                    <div class="vsq-card-header">
                        <h3>${l.nama_lapangan}</h3>
                        <span class="vsq-card-badge ${badgeClass}">${badge}</span>
                    </div>
                    <ul class="vsq-fasilitas">
                        <li>Lapangan indoor</li>
                        <li>Sirkulasi udara baik</li>
                    </ul>
                    <div class="vsq-card-footer">
                        <a href="detail_lapangan.html?id=${l.id}" class="vsq-btn-booking">Booking Sekarang</a>
                    </div>
                </div>
            </article>`;
        }).join('');

    } catch (error) {
        console.error("Gagal memuat data:", error);
        container.innerHTML = '<p class="loading-text">Terjadi kesalahan koneksi.</p>';
    }
});