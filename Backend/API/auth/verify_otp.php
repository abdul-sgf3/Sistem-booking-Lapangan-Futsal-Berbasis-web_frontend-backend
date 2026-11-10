<?php
// ============================================================
// auth/verify_otp.php - Verifikasi Kode OTP (6 digit)
// POST /API/auth/verify_otp.php
// Body: { email, otp }   (juga menerima "kode")
// ============================================================

require_once '../config.php';

requireMethod('POST');

$input = getJsonInput();
$email = strtolower(trim($input['email'] ?? ''));
$otp   = trim((string)($input['otp'] ?? $input['kode'] ?? ''));

if ($email === '') sendError('Email wajib diisi', 400);
if ($otp === '')   sendError('Kode OTP wajib diisi', 400);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendError('Format email tidak valid', 400);
}
if (!preg_match('/^\d{' . OTP_LENGTH . '}$/', $otp)) {
    sendError('Kode OTP harus berupa ' . OTP_LENGTH . ' digit angka', 400);
}
if (!otpAttemptAllowed('verify:' . $email)) {
    sendError('Terlalu banyak percobaan salah. Tunggu beberapa menit atau minta kode baru.', 429);
}

// Cari user
$stmt = mysqli_prepare($conn,
    "SELECT id, email, nama, whatsapp, role, otp_code, otp_expired_at, is_verified
       FROM users WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$user) {
    sendError('Email tidak terdaftar', 404);
}
if ($user['is_verified']) {
    sendError('Email sudah terverifikasi. Silakan login.', 400);
}
if (empty($user['otp_code'])) {
    sendError('Kode OTP tidak ditemukan. Minta kirim ulang.', 400);
}
// Periksa masa berlaku terlebih dahulu
if (isOtpExpired($user['otp_expired_at'])) {
    sendError('Kode OTP sudah kadaluarsa. Minta kirim ulang.', 400);
}

// Periksa kecocokan OTP
if (!hash_equals((string)$user['otp_code'], $otp)) {
    otpAttemptFail('verify:' . $email);
    sendError('Kode OTP salah', 400);
}


// Tandai terverifikasi
$stmt = mysqli_prepare($conn,
    "UPDATE users SET is_verified = 1, otp_code = NULL, otp_expired_at = NULL, verified_at = NOW() WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user['id']);
mysqli_stmt_execute($stmt);
otpAttemptReset('verify:' . $email);

// Login otomatis agar halaman Lengkapi Profil (butuh session) bisa dipakai
session_regenerate_id(true);
$_SESSION['user_id']        = (int)$user['id'];
$_SESSION['user_email']     = $user['email'];
$_SESSION['nama']           = $user['nama'] ?? '';
$_SESSION['role']           = $user['role'] ?? 'user';
$_SESSION['register_email'] = $user['email'];

// "nama" otomatis terisi dari email saat register, jadi yang menentukan profil lengkap adalah WhatsApp
$need_profile = empty($user['whatsapp']);

sendResponse([
    'user_id'      => (int)$user['id'],
    'email'        => $user['email'],
    'need_profile' => $need_profile,
    'redirect'     => $need_profile ? 'lengkapi_profil.html' : 'index.html',
], 'Verifikasi berhasil');
?>
