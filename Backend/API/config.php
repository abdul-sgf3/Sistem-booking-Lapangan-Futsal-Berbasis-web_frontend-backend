<?php
// ============================================
// KONFIGURASI BREVO (Email OTP)
// ============================================
define('BREVO_API_KEY', getenv('BREVO_API_KEY') ?: '');
define('BREVO_FROM_EMAIL', 'abdullah.sgff3@gmail.com'); // GANTI dengan email Gmail Anda yang tadi didaftarkan di Brevo
define('BREVO_FROM_NAME', 'Vincent SQ Arena');

// ============================================
// Backend/API/config.php
// KONFIGURASI GLOBAL API VINCENT SQ ARENA
// (FINAL - SIAP HOSTING INFINITYFREE)
// ============================================

// --- CORS (Izinkan akses dari Front-End) ---
$allowed_origins = [
    'http://localhost',
    'http://localhost:8080', // Port Live Server (VS Code)
    'http://127.0.0.1',
//  'https://vincent-arena.vercel.app' // <-- Nanti aktifkan ini jika Vercel sudah jadi
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true");
}

header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight request (wajib untuk method POST)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// --- Session ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// KONEKSI DATABASE (OTOMATIS LOKAL & RAILWAY)
// ============================================
$is_localhost = ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1');

if ($is_localhost) {
    // --- KONFIGURASI LOKAL (LARAGON / XAMPP) ---
    $host = 'localhost';
    $user = 'root';
    $pass = ''; // Di XAMPP biasanya kosong
    $db   = 'vincent_sqdb';
    $port = 3306;
} else {
    // --- KONFIGURASI RAILWAY ---
    // Railway akan otomatis mengisi nilai getenv() ini
    $host = getenv('MYSQLHOST') ?: 'sql201.infinityfree.com'; 
    $user = getenv('MYSQLUSER') ?: 'if0_43234798';            
    $pass = getenv('MYSQLPASSWORD') ?: 'ProjectKel3';             
    $db   = getenv('MYSQLDATABASE') ?: 'if0_43234798_vincent_sqdb'; 
    $port = getenv('MYSQLPORT') ?: 3306;
}

// Tambahkan parameter $port di akhir mysqli_connect
$conn = mysqli_connect($host, $user, $pass, $db, (int)$port);
if (!$conn) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Koneksi database gagal: ' . mysqli_connect_error(),
        'data'    => null
    ]);
    exit();
}
mysqli_set_charset($conn, 'utf8mb4');

// ============================================
// KONSTANTA PATH (URL & FOLDER UPLOAD)
// ============================================
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host_url = $_SERVER['HTTP_HOST'] ?? 'localhost';

if ($is_localhost) {
    // Ganti 'vincent-arena' jika nama folder di htdocs XAMPP Anda berbeda
    define('BASE_URL', '/vincent-arena'); 
} else {
    define('BASE_URL', $protocol . '://' . $host_url);
}

// --- URL GAMBAR UNTUK FRONTEND ---
// Saat lokal, gambar ada di Frontend. Saat di Railway, gambar ada di Backend/uploads
if ($is_localhost) {
    define('UPLOAD_LAPANGAN', BASE_URL . '/Frontend/assets/uploads/lapangan/');
    define('UPLOAD_MAKANAN',  BASE_URL . '/Frontend/assets/uploads/makanan/');
    define('UPLOAD_BUKTI',    BASE_URL . '/Frontend/assets/uploads/bukti/');
    define('UPLOAD_PROFIL',   BASE_URL . '/Frontend/assets/uploads/profil/');
} else {
    define('UPLOAD_LAPANGAN', BASE_URL . '/uploads/lapangan/');
    define('UPLOAD_MAKANAN',  BASE_URL . '/uploads/makanan/');
    define('UPLOAD_BUKTI',    BASE_URL . '/uploads/bukti/');
    define('UPLOAD_PROFIL',   BASE_URL . '/uploads/profil/');
}

// --- ABSOLUTE PATH UNTUK PROSES UPLOAD ---
if ($is_localhost) {
    // Jika di lokal (XAMPP), simpan ke folder Frontend
    define('ABS_UPLOAD_PROFIL', __DIR__ . '/../../Frontend/assets/uploads/profil/');
    define('ABS_UPLOAD_BUKTI',  __DIR__ . '/../../Frontend/assets/uploads/bukti/');
} else {
    // Jika di Railway, simpan ke folder Backend/uploads
    define('ABS_UPLOAD_PROFIL', __DIR__ . '/../uploads/profil/');
    define('ABS_UPLOAD_BUKTI',  __DIR__ . '/../uploads/bukti/');
}

// ============================================
// HELPER: RESPONSE (JSON)
// ============================================
function sendResponse($data = null, $message = 'Data berhasil diproses', $code = 200) {
    http_response_code($code);
    echo json_encode([
        'status'  => 'success',
        'message' => $message,
        'data'    => $data
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
}

function sendError($message, $code = 400) {
    http_response_code($code);
    echo json_encode([
        'status'  => 'error',
        'message' => $message,
        'data'    => null
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
}

// ============================================
// HELPER: VALIDASI & AUTH
// ============================================
function requireMethod($method) {
    if ($_SERVER['REQUEST_METHOD'] !== $method) {
        sendError('Method tidak diizinkan. Gunakan ' . $method, 405);
    }
}

function requireUserLogin() {
    if (!isset($_SESSION['user_id'])) {
        sendError('Anda belum login', 401);
    }
    return $_SESSION['user_id'];
}

function requireAdminLogin() {
    if (!isset($_SESSION['admin_id'])) {
        sendError('Akses ditolak. Anda bukan admin.', 403);
    }
    return $_SESSION['admin_id'];
}

function getJsonInput() {
    $input = json_decode(file_get_contents('php://input'), true);
    return $input ?? [];
}

// ============================================
// HELPER: FORMAT URL GAMBAR
// ============================================
function fotoLapanganUrl($filename) {
    return empty($filename) ? '' : UPLOAD_LAPANGAN . rawurlencode($filename);
}

function fotoMakananUrl($filename) {
    return empty($filename) ? '' : UPLOAD_MAKANAN . rawurlencode($filename);
}

function buktiUrl($filename) {
    return empty($filename) ? '' : UPLOAD_BUKTI . rawurlencode($filename);
}

function fotoProfilUrl($filename) {
    return empty($filename) ? '' : UPLOAD_PROFIL . rawurlencode($filename);
}

// ============================================
// LOAD HELPER TAMBAHAN (Jika ada)
// ============================================
$helper_files = ['response.php', 'validator.php', 'otp_generator.php', 'uploader.php'];
foreach ($helper_files as $helper) {
    $path = __DIR__ . '/helpers/' . $helper;
    if (file_exists($path)) {
        require_once $path;
    }
}
?>