// api.js - Fungsi Fetch ke API Backend

async function fetchAPI(endpoint, method = 'GET', body = null) {
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

function getDummyData(endpoint) {
    if (endpoint.includes('/auth/login')) {
        return {
            status: 'success',
            data: {
                user_id: 1,
                nama: 'Demo User',
                username: 'demouser',
                email: 'demo@test.com'
            }
        };
    }
    
    if (endpoint.includes('/auth/register')) {
        return {
            status: 'success',
            message: 'Registrasi berhasil',
            data: { email: 'demo@test.com' }
        };
    }
    
    if (endpoint.includes('/lapangan')) {
        return {
            status: 'success',
            data: [
                { id: 1, nama_lapangan: 'Lapangan 1', harga_per_jam: 200000, foto_url: '/vincent-arena/Frontend/assets/uploads/lapangan/Lapangan1.jpeg' },
                { id: 2, nama_lapangan: 'Lapangan 2', harga_per_jam: 150000, foto_url: '/vincent-arena/Frontend/assets/uploads/lapangan/Lapangan2.jpeg' }
            ]
        };
    }
    
    if (endpoint.includes('/makanan')) {
        return {
            status: 'success',
            data: [
                { id: 1, nama: 'Nasi Goreng', kategori: 'makanan', harga: 15000, deskripsi: 'Nasi goreng spesial' },
                { id: 2, nama: 'Mie Ayam', kategori: 'makanan', harga: 12000, deskripsi: 'Mie ayam gurih' }
            ]
        };
    }
    
    return { status: 'success', data: [] };
}

function formatRupiah(angka) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
}

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