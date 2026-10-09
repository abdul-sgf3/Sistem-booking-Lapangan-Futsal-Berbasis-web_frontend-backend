<?php
// ============================================
// Backend/API/config.php
// Konfigurasi API - Vincent SQ Arena
// ============================================

// --- CORS (Izinkan akses dari frontend) ---
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight request (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// --- Session untuk autentikasi ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Koneksi Database ---
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'vincent_sqdb_sql'; // <-- GANTI: hapus '.sql'

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

// Set charset
mysqli_set_charset($conn, 'utf8mb4');

// --- Helper: Response Sukses ---
function sendResponse($data = null, $message = 'Data berhasil diproses', $code = 200) {
    http_response_code($code);
    echo json_encode([
        'status'  => 'success',
        'message' => $message,
        'data'    => $data
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
}

// --- Helper: Response Error ---
function sendError($message, $code = 400) {
    http_response_code($code);
    echo json_encode([
        'status'  => 'error',
        'message' => $message,
        'data'    => null
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
}

// --- Helper: Cek Method ---
function requireMethod($method) {
    if ($_SERVER['REQUEST_METHOD'] !== $method) {
        sendError('Method tidak diizinkan. Gunakan ' . $method, 405);
    }
}

// --- Helper: Cek Login User ---
function requireUserLogin() {
    if (!isset($_SESSION['user_id'])) {
        sendError('Anda belum login', 401);
    }
    return $_SESSION['user_id'];
}

// --- Helper: Cek Login Admin ---
function requireAdminLogin() {
    if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
        sendError('Akses ditolak. Anda bukan admin.', 403);
    }
    return $_SESSION['admin_id'];
}

// --- Helper: Ambil input JSON ---
function getJsonInput() {
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? [];
}

// --- Helper: Format URL Foto Lapangan ---
function fotoLapanganUrl($filename) {
    if (empty($filename)) return '';
    return '/Futsal_VincentSQ/Frontend/assets/uploads/lapangan/' . rawurlencode($filename);
}

// --- Helper: Format URL Foto Makanan ---
function fotoMakananUrl($filename) {
    if (empty($filename)) return '';
    return '/Futsal_VincentSQ/Frontend/assets/uploads/makanan/' . rawurlencode($filename);
}

// --- Helper: Format URL Bukti Pembayaran ---
function buktiUrl($filename) {
    if (empty($filename)) return '';
    return '/Futsal_VincentSQ/Frontend/assets/uploads/bukti/' . rawurlencode($filename);
}
?>