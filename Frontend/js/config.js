// ============================================
// CORS - IZINKAN AKSES DARI FRONTEND (LENGKAP)
// ============================================
$allowed_origins = [
    'http://localhost',
    'http://127.0.0.1',
    'http://localhost:5500',   // Jika pakai Live Server VS Code
    'http://127.0.0.1:5500',
    'https://sistem-booking-lapangan-futsal-berbasis-webfron-production.up.railway.app' // Domain Railway Anda
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    // Fallback: Izinkan semua sementara untuk testing (HAPUS saat production)
    header("Access-Control-Allow-Origin: *"); 
}

header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true"); // <--- WAJIB untuk Session
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}