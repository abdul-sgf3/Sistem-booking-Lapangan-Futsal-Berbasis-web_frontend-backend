// api.js - Fungsi Fetch ke API Backend

async function fetchAPI(endpoint, method = 'GET', body = null) {
    // Kalau mode dummy, langsung return data dummy
    if (APP_CONFIG.DATA_MODE === 'dummy') {
        return getDummyData(endpoint);
    }
    
    const options = {
        method: method,
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include'
    };
    
    if (body && method !== 'GET') {
        options.body = JSON.stringify(body);
    }
    
    try {
        const response = await fetch(APP_CONFIG.API_BASE + endpoint, options);
        return await response.json();
    } catch (err) {
        console.error('API Error:', err);
        return { status: 'error', message: 'Gagal terhubung ke server' };
    }
}

// Data dummy untuk development
function getDummyData(endpoint) {
    if (endpoint.includes('/lapangan')) {
        return {
            status: 'success',
            data: [
                {
                    id: 1,
                    nama_lapangan: 'Lapangan 1',
                    harga_per_jam: 200000,
                    deskripsi: 'Lapangan premium dengan fasilitas lengkap',
                    foto_url: '/vincent-arena/Frontend/assets/uploads/lapangan/Lapangan1.jpeg'
                },
                {
                    id: 2,
                    nama_lapangan: 'Lapangan 2',
                    harga_per_jam: 150000,
                    deskripsi: 'Lapangan standar dengan harga terjangkau',
                    foto_url: '/vincent-arena/Frontend/assets/uploads/lapangan/Lapangan2.jpeg'
                }
            ]
        };
    }
    
    if (endpoint.includes('/makanan')) {
        return {
            status: 'success',
            data: [
                { id: 1, nama: 'Nasi Goreng', kategori: 'makanan', harga: 15000, deskripsi: 'Nasi goreng spesial', foto_url: '' },
                { id: 2, nama: 'Mie Ayam', kategori: 'makanan', harga: 12000, deskripsi: 'Mie ayam gurih', foto_url: '' },
                { id: 3, nama: 'Es Teh Manis', kategori: 'minuman', harga: 5000, deskripsi: 'Es teh segar', foto_url: '' },
                { id: 4, nama: 'Air Mineral', kategori: 'minuman', harga: 4000, deskripsi: 'Air mineral dingin', foto_url: '' }
            ]
        };
    }
    
    return { status: 'success', data: [] };
}

// Helper: Format Rupiah
function formatRupiah(angka) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
}

// Helper: Tampilkan Alert
function showAlert(containerId, tipe, pesan) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    const warna = tipe === 'success' 
        ? 'bg-green-50 text-green-600 border-green-200' 
        : 'bg-red-50 text-red-600 border-red-200';
    
    container.innerHTML = `
        <div class="${warna} text-sm font-semibold p-3 rounded-lg mb-4 text-center border">
            ${pesan}
        </div>
    `;
}