async function fetchAPI(endpoint, method = 'GET', body = null) {
    const options = {
        method: method,
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include' // Penting untuk handle session login
    };
    
    if (body && method !== 'GET') {
        options.body = JSON.stringify(body);
    }
    
    try {
        const response = await fetch(APP_CONFIG.API_BASE + endpoint, options);
        const data = await response.json();
        return data;
    } catch (err) {
        console.error('API Error:', err);
        return { status: 'error', message: 'Gagal terhubung ke server' };
    }
}

function formatRupiah(angka) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
}