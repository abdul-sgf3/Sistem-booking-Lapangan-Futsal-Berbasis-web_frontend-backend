<?php
// ============================================================
// health.php - Cek kesehatan backend (untuk Railway healthcheck & debugging)
// GET /API/health.php
// Tidak membocorkan rahasia: hanya status true/false.
// ============================================================

define('DB_OPTIONAL', true);
require_once __DIR__ . '/config.php';

$cfg    = getDbConfig();
$tables = ['users', 'lapangan', 'booking', 'jadwal', 'makanan_minuman', 'pengaturan'];
$missing = $tables;
$db_ok  = false;

if ($conn) {
    $db_ok = true;
    $res = mysqli_query($conn, 'SHOW TABLES');
    $have = [];
    while ($r = mysqli_fetch_row($res)) $have[] = $r[0];
    $missing = array_values(array_diff($tables, $have));
}

$dirs = [
    'lapangan' => ABS_UPLOAD_LAPANGAN,
    'bukti'    => ABS_UPLOAD_BUKTI,
    'profil'   => ABS_UPLOAD_PROFIL
];

$writable = [];

foreach ($dirs as $k => $d) {
    $writable[$k] = is_dir($d) && is_writable($d);
}

$ok = $db_ok && empty($missing);

http_response_code($ok ? 200 : 503);

echo json_encode([
    'status'  => $ok ? 'success' : 'error',
    'message' => $ok ? 'Backend sehat' : 'Backend belum siap (lihat detail)',
    'data'    => [
        'php'                    => PHP_VERSION,
        'mode'                   => IS_HOSTED ? 'hosting' : 'lokal',
        'https'                  => isHttps(),

        // Database
        'db_connected'           => $db_ok,
        'db_config_source'       => $cfg['source'],
        'tables_missing'         => $missing,

        // Upload
        'uploads_writable'       => $writable,

        // Brevo
        'brevo_configured'       => BREVO_API_KEY !== '',
        'brevo_curl_available'   => function_exists('curl_init'),
        'brevo_sender_configured'=> trim(BREVO_FROM_EMAIL) !== '',

        // OTP
        'otp_debug'              => OTP_DEBUG,

        // CORS
        'origin_diterima'        => $_SERVER['HTTP_ORIGIN'] ?? null,
        'origin_diizinkan'       => $origin_allowed,
        'allowed_origins'        => $allowed_origins,
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);