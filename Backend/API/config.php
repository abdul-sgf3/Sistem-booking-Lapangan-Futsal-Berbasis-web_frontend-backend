<?php
// ============================================
// Backend/API/config.php
// KONFIGURASI GLOBAL API VINCENT SQ ARENA
// (FINAL - SIAP HOSTING INFINITYFREE)
// ============================================

// --- CORS (Izinkan akses dari Front-End) ---
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
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
// KONEKSI DATABASE (OTOMATIS LOKAL & HOSTING)
// ============================================
$is_localhost = ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1');

if ($is_localhost) {
    // --- KONFIGURASI LOKAL (LARAGON/XAMPP) ---
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'vincent_sqdb';
} else {
    // --- KONFIGURASI HOSTING (INFINITYFREE) ---
    $host = 'sql201.infinityfree.com'; 
    $user = 'if0_43234798';            
    $pass = 'ProjectKel3';             
    $db   = 'if0_43234798_vincent_sqdb'; 
}

$conn = mysqli_connect($host, $user, $pass, $db);
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
if ($is_localhost) {
    // Ganti 'vincent-arena' sesuai nama folder Anda di Laragon
    define('BASE_URL', '/vincent-arena'); 
} else {
    define('BASE_URL', 'https://vincent-arena.infinityfreeapp.com');
}

// PASTIKAN TIDAK ADA LAGI define('BASE_URL', ...) DI BAWAH SINI

define('UPLOAD_LAPANGAN', BASE_URL . '/Frontend/assets/uploads/lapangan/');
define('UPLOAD_MAKANAN',  BASE_URL . '/Frontend/assets/uploads/makanan/');
define('UPLOAD_BUKTI',    BASE_URL . '/Frontend/assets/uploads/bukti/');
define('UPLOAD_PROFIL',   BASE_URL . '/Frontend/assets/uploads/profil/');

// Absolute Path (untuk proses upload file di server)
define('ABS_UPLOAD_PROFIL', __DIR__ . '/../../Frontend/assets/uploads/profil/');
define('ABS_UPLOAD_BUKTI',  __DIR__ . '/../../Frontend/assets/uploads/bukti/');

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