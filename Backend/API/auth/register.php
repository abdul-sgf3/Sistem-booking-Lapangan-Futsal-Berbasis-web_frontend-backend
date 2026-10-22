<?php
// ============================================================
// auth/register.php - Registrasi User (FINAL FIX)
// POST /api/auth/register.php
// Body: { email, password, confirm_password }
// ============================================================

// Panggil config utama
require_once '../config.php';

// Hanya izinkan POST
requireMethod('POST');

// Ambil input JSON dari Front-End
$input = getJsonInput();
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$konfirmasi = $input['confirm_password'] ?? $input['konfirmasi_password'] ?? $input['password'] ?? '';

// ===== VALIDASI INPUT =====
if (empty($email) || empty($password) || empty($konfirmasi)) {
    sendError('Semua field (Email, Password, Konfirmasi) wajib diisi.', 400);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendError('Format email tidak valid.', 400);
}
if ($password !== $konfirmasi) {
    sendError('Password dan Konfirmasi Password tidak cocok.', 400);
}
if (strlen($password) < 6) {
    sendError('Password minimal 6 karakter.', 400);
}

// ===== CEK APAKAH EMAIL SUDAH ADA =====
$stmt = mysqli_prepare($conn, "SELECT id, is_verified FROM users WHERE email = ? LIMIT 1");
if (!$stmt) {
    sendError('Kesalahan sistem (prepare check email): ' . mysqli_error($conn), 500);
}
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$existing = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// ===== GENERATE OTP =====
// Pastikan fungsi helper OTP tersedia (dari otp_generator.php)
if (!function_exists('generateOtp')) {
    // Fallback jika helper gagal dimuat
    function generateOtp() { return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT); }
    function getOtpExpiredTime($m = 5) { return date('Y-m-d H:i:s', strtotime("+$m minutes")); }
}

$otp = generateOtp();
$otp_expired = getOtpExpiredTime(5);
$hashed = password_hash($password, PASSWORD_DEFAULT);

// ===== PROSES SIMPAN KE DATABASE =====
if ($existing) {
    // Jika email sudah terdaftar tapi BELUM verifikasi
    if ($existing['is_verified']) {
        sendError('Email sudah terdaftar dan terverifikasi. Silakan login.', 409);
    }
    
    // Update user yang ada (reset OTP dan password baru)
    $stmt = mysqli_prepare($conn, "UPDATE users SET password = ?, otp_code = ?, otp_expired_at = ? WHERE id = ?");
    if (!$stmt) {
        sendError('Kesalahan sistem (prepare update): ' . mysqli_error($conn), 500);
    }
    mysqli_stmt_bind_param($stmt, "sssi", $hashed, $otp, $otp_expired, $existing['id']);
    
    if (!mysqli_stmt_execute($stmt)) {
        sendError('Gagal memperbarui data user: ' . mysqli_error($conn), 500);
    }
    $user_id = $existing['id'];

} else {
    // Insert user BARU
    // PERBAIKAN: Tambahkan kolom 'nama' dengan mengambil nama depan dari email
    $nama_default = explode('@', $email)[0]; 
    
    $stmt = mysqli_prepare($conn, 
        "INSERT INTO users (nama, email, password, role, otp_code, otp_expired_at, is_verified) 
         VALUES (?, ?, ?, 'user', ?, ?, 0)");
         
    if (!$stmt) {
        sendError('Kesalahan sistem (prepare insert): ' . mysqli_error($conn), 500);
    }
    
    // Bind parameter: nama_default(s), email(s), hashed(s), otp(s), otp_expired(s)
    mysqli_stmt_bind_param($stmt, "sssss", $nama_default, $email, $hashed, $otp, $otp_expired);
    
    if (!mysqli_stmt_execute($stmt)) {
        sendError('Gagal mendaftarkan akun baru: ' . mysqli_error($conn), 500);
    }
    $user_id = mysqli_insert_id($conn);
}

// ===== SET SESSION SEMENTARA =====
$_SESSION['register_email'] = $email;
$_SESSION['register_user_id'] = $user_id;

// ===== KIRIM OTP (SIMULASI) =====
// Karena BE belum terhubung ke SMTP, kita simpan OTP di log file
// (File otp_log.txt akan muncul di folder Backend/API/)
$log_msg = date('Y-m-d H:i:s') . " | OTP untuk $email: $otp\n";
@file_put_contents(__DIR__ . '/../otp_log.txt', $log_msg, FILE_APPEND);

// ===== RESPONSE SUKSES =====
sendResponse([
    'user_id' => (int)$user_id,
    'email' => $email,
    'otp_debug' => $otp, // HAPUS baris ini di production asli
    'expired_at' => $otp_expired,
    'redirect' => BASE_URL . '/Frontend/verifikasi.html'
], 'Registrasi berhasil. Silakan cek email/log untuk kode OTP.');
?>