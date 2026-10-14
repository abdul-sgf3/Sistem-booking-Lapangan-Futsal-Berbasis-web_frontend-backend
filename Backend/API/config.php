<?php
// ============================================
// Backend/API/config.php
// Konfigurasi Global API Vincent SQ Arena
// ============================================

// --- CORS ---
$allowed_origins = [
    'http://localhost',
    'http://127.0.0.1',
    'http://localhost:3000',
    'http://localhost:8080'
];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header("Access-Control-Allow-Origin: http://localhost");
}
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// --- Session ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Koneksi Database ---
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'vincent_sqdb';

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

// --- Konstanta Path ---
define('BASE_URL', '/Futsal_Vincent_SQ');
define('UPLOAD_LAPANGAN', BASE_URL . '/Frontend/assets/uploads/lapangan/');
define('UPLOAD_MAKANAN',  BASE_URL . '/Frontend/assets/uploads/makanan/');
define('UPLOAD_BUKTI',    BASE_URL . '/Frontend/assets/uploads/bukti/');
define('UPLOAD_PROFIL',   BASE_URL . '/Frontend/assets/uploads/profil/');

// --- Absolute Path untuk Upload ---
define('ABS_UPLOAD_PROFIL', __DIR__ . '/../../Frontend/assets/uploads/profil/');
define('ABS_UPLOAD_BUKTI',  __DIR__ . '/../../Frontend/assets/uploads/bukti/');

// ============================================
// HELPER: RESPONSE
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
// HELPER: VALIDASI
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
// HELPER: FORMAT URL
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
// LOAD HELPERS (jika ada)
// ============================================

$helper_files = ['response.php', 'validator.php', 'otp_generator.php', 'uploader.php'];
foreach ($helper_files as $helper) {
    $path = __DIR__ . '/helpers/' . $helper;
    if (file_exists($path)) {
        require_once $path;
    }
}
?>