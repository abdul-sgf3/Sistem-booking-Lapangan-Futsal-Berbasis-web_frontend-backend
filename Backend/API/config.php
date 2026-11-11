<?php
// ============================================================
// Backend/API/config.php
// KONFIGURASI GLOBAL API VINCENT SQ ARENA
// Berjalan otomatis di: Lokal (XAMPP/Laragon) & Railway (Docker)
//
// Environment variable yang dikenali (semua opsional di lokal):
//   APP_ENV          production | local   (default: auto-detect)
//   APP_DEBUG        1 = tampilkan detail error di JSON (jangan di production)
//   APP_URL          URL publik backend, mis. https://xxx.up.railway.app
//   APP_TIMEZONE     default Asia/Jakarta
//   ALLOWED_ORIGINS  daftar origin Frontend, pisahkan koma
//   FRONTEND_URL     1 origin Frontend (alternatif ALLOWED_ORIGINS)
//   MYSQL_URL / MYSQLHOST,MYSQLUSER,MYSQLPASSWORD,MYSQLDATABASE,MYSQLPORT
//   BREVO_API_KEY, BREVO_FROM_EMAIL, BREVO_FROM_NAME
//   OTP_DEBUG        1 = sertakan kode OTP di response (hanya untuk testing)
// ============================================================

require_once __DIR__ . '/helpers/env.php';

define('IS_HOSTED', isHosted());
define('APP_DEBUG', envBool('APP_DEBUG', !IS_HOSTED));          // lokal: true, hosting: false
define('OTP_DEBUG', envBool('OTP_DEBUG', !IS_HOSTED));          // lokal: true, hosting: false

// ---------- Error handling: JANGAN pernah cetak HTML ke response ----------
// Semua error dicatat ke log (Railway: tab Logs), response tetap JSON.
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// mysqli melempar exception (konsisten di semua versi PHP)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set(env('APP_TIMEZONE', 'Asia/Jakarta'));

set_exception_handler(function ($e) {
    error_log('[API] ' . get_class($e) . ': ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json; charset=UTF-8');
    }
    $payload = ['status' => 'error', 'message' => 'Terjadi kesalahan pada server. Silakan coba lagi.', 'data' => null];
    if (APP_DEBUG) {
        $payload['debug'] = [
            'type'    => get_class($e),
            'message' => $e->getMessage(),
            'file'    => basename($e->getFile()) . ':' . $e->getLine(),
        ];
    }
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
});

// ---------- Deteksi HTTPS (Railway berada di belakang proxy) ----------
function isHttps() {
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') return true;
    if (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') return true;
    if ((int)($_SERVER['SERVER_PORT'] ?? 0) === 443) return true;
    return false;
}

// ============================================
// CORS
// ============================================
$allowed_origins = [
    'http://localhost',
    'http://127.0.0.1',
    'http://localhost:3000',
    'http://localhost:5173',
    'http://localhost:5500',
    'http://127.0.0.1:5500',
    'http://localhost:8080',
    'http://127.0.0.1:8080',
    'https://vincent-arena.vercel.app',
];
foreach (array_merge(explode(',', (string)env('ALLOWED_ORIGINS', '')), [(string)env('FRONTEND_URL', '')]) as $o) {
    $o = rtrim(trim($o), '/');
    if ($o !== '') $allowed_origins[] = $o;
}
$allowed_origins = array_values(array_unique($allowed_origins));

$origin = rtrim($_SERVER['HTTP_ORIGIN'] ?? '', '/');
$origin_allowed = ($origin !== '' && in_array($origin, $allowed_origins, true));

header('Vary: Origin');
if ($origin_allowed) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Credentials: true');
}
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept');
header('Access-Control-Max-Age: 86400');
header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');

// Preflight dijawab SEBELUM koneksi database (tetap jalan walau DB bermasalah)
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(204);
    exit();
}

// ============================================
// SESSION (cookie lintas-domain: Vercel -> Railway)
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    $https = isHttps();
    ini_set('session.gc_maxlifetime', (string)(60 * 60 * 24 * 7));
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'lifetime' => 60 * 60 * 24 * 7,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $https,                    // wajib TRUE jika SameSite=None
        'httponly' => true,
        'samesite' => $https ? 'None' : 'Lax',   // https (Railway) = None, lokal http = Lax
    ]);
    session_start();
}

// ============================================
// HELPER: RESPONSE (JSON)  — didefinisikan SEBELUM koneksi DB
// ============================================
function sendResponse($data = null, $message = 'Data berhasil diproses', $code = 200) {
    http_response_code($code);
    echo json_encode([
        'status'  => 'success',
        'message' => $message,
        'data'    => $data
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

function sendError($message, $code = 400) {
    http_response_code($code);
    echo json_encode([
        'status'  => 'error',
        'message' => $message,
        'data'    => null
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

/** Error + data tambahan (mis. need_verify, daftar bentrok jadwal) */
function sendErrorData($message, $data, $code = 400) {
    http_response_code($code);
    echo json_encode([
        'status'  => 'error',
        'message' => $message,
        'data'    => $data
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

// ============================================
// KONEKSI DATABASE
// ============================================
$db_cfg   = getDbConfig();
$conn     = null;
$db_error = null;

try {
    $conn = mysqli_connect($db_cfg['host'], $db_cfg['user'], $db_cfg['pass'], $db_cfg['name'], (int)$db_cfg['port']);
    mysqli_set_charset($conn, 'utf8mb4');
    mysqli_query($conn, "SET time_zone = '" . date('P') . "'"); // samakan jam MySQL dengan PHP
} catch (Throwable $e) {
    $conn     = null;
    $db_error = $e->getMessage();
    error_log('[API] Koneksi database gagal (sumber config: ' . $db_cfg['source'] . '): ' . $db_error);

    if (!defined('DB_OPTIONAL')) {   // health.php boleh lanjut tanpa DB
        $msg = 'Koneksi database gagal. Hubungi administrator.';
        if (APP_DEBUG) {
            $msg .= ' [' . $db_error . ' | sumber config: ' . $db_cfg['source'] . ']';
        }
        sendError($msg, 503);
    }
}

// ============================================
// KONSTANTA PATH (URL & FOLDER UPLOAD)
// ============================================
$scheme    = isHttps() ? 'https' : 'http';
$host_http = $_SERVER['HTTP_HOST'] ?? 'localhost';

if (IS_HOSTED) {
    // Hosting: Backend adalah root web server. Foto disimpan di Backend/uploads
    define('BASE_URL', rtrim((string)env('APP_URL', $scheme . '://' . $host_http), '/'));

    define('UPLOAD_LAPANGAN', BASE_URL . '/uploads/lapangan/');
    define('UPLOAD_MAKANAN',  BASE_URL . '/uploads/makanan/');
    define('UPLOAD_BUKTI',    BASE_URL . '/uploads/bukti/');
    define('UPLOAD_PROFIL',   BASE_URL . '/uploads/profil/');

    $upload_root = dirname(__DIR__) . '/uploads/';
} else {
    // Lokal: nama folder project dideteksi otomatis dari URL (tidak perlu hardcode 'vincent-arena')
    $script    = $_SERVER['SCRIPT_NAME'] ?? '';
    $pos       = stripos($script, '/Backend/');
    $base_path = ($pos !== false) ? substr($script, 0, $pos) : '';
    define('BASE_URL', $scheme . '://' . $host_http . $base_path);

    define('UPLOAD_LAPANGAN', BASE_URL . '/Frontend/assets/uploads/lapangan/');
    define('UPLOAD_MAKANAN',  BASE_URL . '/Frontend/assets/uploads/makanan/');
    define('UPLOAD_BUKTI',    BASE_URL . '/Frontend/assets/uploads/bukti/');
    define('UPLOAD_PROFIL',   BASE_URL . '/Frontend/assets/uploads/profil/');

    $upload_root = dirname(__DIR__, 2) . '/Frontend/assets/uploads/';
}

define('ABS_UPLOAD_LAPANGAN', $upload_root . 'lapangan/');
define('ABS_UPLOAD_MAKANAN',  $upload_root . 'makanan/');
define('ABS_UPLOAD_BUKTI',    $upload_root . 'bukti/');
define('ABS_UPLOAD_PROFIL',   $upload_root . 'profil/');

// ============================================
// KONFIGURASI BREVO (Email OTP)
// ============================================
define('BREVO_API_KEY',    (string)env('BREVO_API_KEY', ''));
define('BREVO_FROM_EMAIL', (string)env('BREVO_FROM_EMAIL', 'abdullah.sgff3@gmail.com')); // harus sender terverifikasi di Brevo
define('BREVO_FROM_NAME',  (string)env('BREVO_FROM_NAME', 'Vincent SQ Arena'));

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
    return (int)$_SESSION['user_id'];
}

function requireAdminLogin() {
    if (!isset($_SESSION['admin_id'])) {
        sendError('Akses ditolak. Anda bukan admin.', 403);
    }
    return (int)$_SESSION['admin_id'];
}

/** Baca body request: JSON, atau fallback ke form-data ($_POST) */
function getJsonInput() {
    $raw   = file_get_contents('php://input');
    $input = json_decode($raw, true);
    if (is_array($input)) return $input;
    if (!empty($_POST))   return $_POST;
    return [];
}

/** Ambil ID dari route (?id=) atau body. Dipakai endpoint yang di-rewrite: /bookings/5/cancel dst. */
function getRequestId($input = [], $keys = ['id']) {
    if (isset($_GET['id'])) return (int)$_GET['id'];
    foreach ($keys as $k) {
        if (isset($input[$k])) return (int)$input[$k];
    }
    return 0;
}

/** Hanya kirim kode OTP di response bila mode debug aktif (testing) */
function otpDebugData($otp) {
    return OTP_DEBUG ? ['otp_debug' => $otp] : [];
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
// LOAD HELPER TAMBAHAN
// ============================================
foreach (['response.php', 'validator.php', 'otp_generator.php', 'uploader.php', 'booking_helper.php'] as $helper) {
    $path = __DIR__ . '/helpers/' . $helper;
    if (file_exists($path)) {
        require_once $path;
    }
}
?>
