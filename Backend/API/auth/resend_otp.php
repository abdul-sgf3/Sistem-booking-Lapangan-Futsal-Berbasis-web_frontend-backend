<?php
// ============================================================
// auth/resend_otp.php - Kirim Ulang Kode OTP
// POST /api/auth/resend_otp.php
// Body: { email }
// ============================================================

require_once '../config.php';

requireMethod('POST');

$input = getJsonInput();
$email = trim($input['email'] ?? '');

if (empty($email)) sendError('Email wajib diisi', 400);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendError('Format email tidak valid', 400);
}

// Cek user
$stmt = mysqli_prepare($conn, 
    "SELECT id, is_verified FROM users WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$user) sendError('Email tidak terdaftar', 404);
if ($user['is_verified']) sendError('Email sudah terverifikasi', 400);

// Generate OTP baru
$otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
$otp_expired = date('Y-m-d H:i:s', strtotime('+5 minutes'));

$stmt = mysqli_prepare($conn,
    "UPDATE users SET otp_code = ?, otp_expired_at = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, "ssi", $otp, $otp_expired, $user['id']);

if (!mysqli_stmt_execute($stmt)) {
    sendError('Gagal mengirim ulang OTP', 500);
}

// TODO: Kirim OTP via email
// Untuk development, tampilkan OTP di response
sendResponse([
    'email' => $email,
    'otp_debug' => $otp, // HAPUS DI PRODUCTION!
    'expired_at' => $otp_expired
], 'Kode OTP baru telah dikirim ke email Anda');
?>