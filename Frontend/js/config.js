// ============================================
// CORS - IZINKAN AKSES DARI FRONTEND (DIPERBAIKI)
// ============================================
$allowed_origins = [
    'http://localhost',
    'http://127.0.0.1',
    'http://localhost:5500',   
    'http://127.0.0.1:5500',
    'http://localhost/vincent-arena', // Sesuaikan dengan folder lokal Anda jika ada
    'https://sistem-booking-lapangan-futsal-berbasis-webfron-production.up.railway.app' 
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowed_origins)) {
    // Jika origin diizinkan, kembalikan origin tersebut (BUKAN *)
    header("Access-Control-Allow-Origin: $origin");
} else {
    // FALLBACK: Jika origin tidak dikenal, JANGAN gunakan '*'.
    // Kita gunakan origin yang diminta agar browser tidak memblokir, 
    // tapi ini tetap aman karena kita hanya mengizinkan jika credentials dikirim.
    // Untuk production yang lebih ketat, Anda bisa menolak origin yang tidak dikenal.
    if ($origin) {
        header("Access-Control-Allow-Origin: $origin");
    } else {
        // Jika tidak ada origin (misal request dari Postman), izinkan *
        header("Access-Control-Allow-Origin: *");
    }
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