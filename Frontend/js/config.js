const APP_CONFIG = {
    // Deteksi otomatis: Jika di localhost pakai local, jika di Vercel pakai URL Railway
    API_BASE: (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1')
        ? 'http://localhost/vincent-arena/Backend/API'
        : 'https://URL-RAILWAY-BE-ANDA.up.railway.app', // <--- Pastikan nanti diganti URL dari BE!
        
    ASSETS_BASE: (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1')
        ? '/vincent-arena/Frontend/assets'
        : 'https://URL-VERCEL-FE-ANDA.vercel.app/assets', // Sesuaikan nanti
        
    DATA_MODE: 'live'
};