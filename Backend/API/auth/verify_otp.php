<?php
// ============================================================
// auth/verify_otp.php - Verifikasi Kode OTP
// POST /api/auth/verify_otp.php
// Body: { email, otp }
// ============================================================

require_once '../config.php';

requireMethod('POST');

$input = getJsonInput();

// Support 2 format: otp atau kode
$email = trim($input['email'] ?? '');
$otp = trim($input['otp'] ?? $input['kode'] ?? '');

// Validasi
if (empty($email)) sendError('Email wajib diisi', 400);
if (empty($otp)) sendError('Kode OTP wajib diisi', 400);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendError('Format email tidak valid', 400);
}
if (!preg_match('/^\d{6}$/', $otp)) {
    sendError('Kode OTP harus 6 digit angka', 400);
}

// Cari user
$stmt = mysqli_prepare($conn,
    "SELECT id, email, nama, otp_code, otp_expired_at, is_verified 
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

if ($user['otp_code'] !== $otp) {
    sendError('Kode OTP salah', 400);
}

if (strtotime($user['otp_expired_at']) < time()) {
    sendError('Kode OTP sudah kadaluarsa. Minta kirim ulang.', 400);
}

// Update user
$stmt = mysqli_prepare($conn,
    "UPDATE users 
     SET is_verified = 1, otp_code = NULL, otp_expired_at = NULL, verified_at = NOW() 
     WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user['id']);
mysqli_stmt_execute($stmt);

// Set session
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['register_email'] = $user['email'];

// Cek apakah profil sudah lengkap
$need_profile = empty($user['nama']);

sendResponse([
    'user_id' => (int)$user['id'],
    'email' => $user['email'],
    'need_profile' => $need_profile,
    'redirect' => $need_profile 
        ? '/Frontend/autentikasi/lengkapi_profil.php' 
        : '/Frontend/pages/index.php'
], 'Verifikasi berhasil');
?>