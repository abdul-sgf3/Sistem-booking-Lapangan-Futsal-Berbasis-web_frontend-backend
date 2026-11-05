<?php
// ============================================================
// scripts/init_db.php — dijalankan entrypoint.sh saat container start (CLI saja)
// 1) menunggu MySQL siap  2) membuat tabel jika belum ada
// 3) mengisi data awal HANYA jika database masih kosong
// 4) membuat akun admin dari ADMIN_EMAIL + ADMIN_PASSWORD (jika diisi & belum ada admin)
// ============================================================
if (php_sapi_name() !== 'cli') { http_response_code(403); exit('Forbidden'); }

$root = is_dir('/var/www/html/API') ? '/var/www/html' : dirname(__DIR__);
require_once $root . '/API/helpers/env.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$cfg  = getDbConfig();
$dir  = dirname(__DIR__) . '/database';
$conn = null;

for ($i = 1; $i <= 30; $i++) {
    try {
        $conn = mysqli_connect($cfg['host'], $cfg['user'], $cfg['pass'], $cfg['name'], $cfg['port']);
        break;
    } catch (Throwable $e) {
        echo "[init_db] menunggu database ($i/30): " . $e->getMessage() . "\n";
        sleep(2);
    }
}
if (!$conn) { echo "[init_db] database tidak terjangkau (sumber config: {$cfg['source']}). Dilewati.\n"; exit(0); }
mysqli_set_charset($conn, 'utf8mb4');

function runSqlFile($conn, $file) {
    $sql = file_get_contents($file);
    mysqli_multi_query($conn, $sql);
    do { if ($r = mysqli_store_result($conn)) mysqli_free_result($r); } while (mysqli_more_results($conn) && mysqli_next_result($conn));
}

$fresh = (mysqli_num_rows(mysqli_query($conn, "SHOW TABLES LIKE 'users'")) === 0);
runSqlFile($conn, "$dir/schema.sql");
echo "[init_db] skema dipastikan ada.\n";
if ($fresh) { runSqlFile($conn, "$dir/seed.sql"); echo "[init_db] data awal (lapangan, makanan, pengaturan) dimasukkan.\n"; }

$email = strtolower(trim((string)env('ADMIN_EMAIL', '')));
$pass  = (string)env('ADMIN_PASSWORD', '');
if ($email && $pass) {
    $q = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($q, "s", $email); mysqli_stmt_execute($q);
    if (!mysqli_fetch_assoc(mysqli_stmt_get_result($q))) {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $nama = 'Administrator'; $uname = 'admin';
        $ins = mysqli_prepare($conn, "INSERT INTO users (nama, username, email, password, role, is_verified, is_active, verified_at) VALUES (?, ?, ?, ?, 'admin', 1, 1, NOW())");
        mysqli_stmt_bind_param($ins, "ssss", $nama, $uname, $email, $hash); mysqli_stmt_execute($ins);
        echo "[init_db] akun admin dibuat: $email\n";
    } else { echo "[init_db] akun $email sudah ada (tidak diubah).\n"; }
} elseif ($fresh) {
    echo "[init_db] CATATAN: isi ADMIN_EMAIL & ADMIN_PASSWORD di Variables agar akun admin dibuat otomatis.\n";
}
