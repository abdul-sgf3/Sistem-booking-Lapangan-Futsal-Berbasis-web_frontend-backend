// lapangan.js - Load Kartu Lapangan

document.addEventListener('DOMContentLoaded', async function() {
    const container = document.getElementById('lapangan-container');
    if (!container) return;
    
    const result = await fetchAPI('/public/lapangan.php');
    const data = result.data || [];
    
    if (data.length === 0) {
        container.innerHTML = '<p class="loading-text">Belum ada lapangan tersedia.</p>';
        return;
    }
    
    container.innerHTML = data.map(function(l) {
        const isVIP = l.harga_per_jam >= 200000;
        const badge = isVIP ? 'VIP' : 'Regular';
        const badgeClass = isVIP ? 'badge-vip' : 'badge-regular';
        
        const fasilitas = isVIP 
            ? ['Lapangan indoor', 'Area luas', 'Area penonton', 'Sirkulasi baik']
            : ['Lapangan indoor', 'Area standar'];
        
        const fasilitasHTML = fasilitas.map(function(f) {
            return '<li>' + f + '</li>';
        }).join('');
        
        return `
        <article class="vsq-card">
            <div class="vsq-card-img" style="background-image: url('${l.foto_url}');"></div>
            <div class="vsq-card-body">
                <div class="vsq-card-header">
                    <h3>${l.nama_lapangan}</h3>
                    <span class="vsq-card-badge ${badgeClass}">${badge}</span>
                </div>
                <ul class="vsq-fasilitas">${fasilitasHTML}</ul>
                <div class="vsq-card-footer">
                    <a href="detail_lapangan.html?id=${l.id}" class="vsq-btn-booking">Booking Sekarang</a>
                </div>
            </div>
        </article>`;
    }).join('');
});