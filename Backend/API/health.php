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

$dirs = ['lapangan' => ABS_UPLOAD_LAPANGAN, 'bukti' => ABS_UPLOAD_BUKTI, 'profil' => ABS_UPLOAD_PROFIL];
$writable = [];
foreach ($dirs as $k => $d) $writable[$k] = is_dir($d) && is_writable($d);

$ok = $db_ok && empty($missing);
http_response_code($ok ? 200 : 503);
echo json_encode([
    'status'  => $ok ? 'success' : 'error',
    'message' => $ok ? 'Backend sehat' : 'Backend belum siap (lihat detail)',
    'data'    => [
        'php'               => PHP_VERSION,
        'mode'              => IS_HOSTED ? 'hosting' : 'lokal',
        'https'             => isHttps(),
        'db_connected'      => $db_ok,
        'db_config_source'  => $cfg['source'],   // jika 'default-lokal' di Railway = variabel MySQL belum terpasang
        'tables_missing'    => $missing,
        'uploads_writable'  => $writable,
        'brevo_configured'  => BREVO_API_KEY !== '',
        'otp_debug'         => OTP_DEBUG,
        'origin_diterima'   => $_SERVER['HTTP_ORIGIN'] ?? null,
        'origin_diizinkan'  => $origin_allowed,
        'allowed_origins'   => $allowed_origins,
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
