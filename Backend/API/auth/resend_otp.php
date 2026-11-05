<?php
// ============================================================
// auth/resend_otp.php - Kirim Ulang Kode OTP (verifikasi email)
// POST /API/auth/resend_otp.php
// Body: { email }
// ============================================================

require_once '../config.php';

requireMethod('POST');

$input = getJsonInput();
$email = strtolower(trim($input['email'] ?? ''));

if ($email === '') sendError('Email wajib diisi', 400);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendError('Format email tidak valid', 400);
}

$stmt = mysqli_prepare($conn,
    "SELECT id, nama, is_verified, otp_expired_at FROM users WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$user) sendError('Email tidak terdaftar', 404);
if ($user['is_verified']) sendError('Email sudah terverifikasi', 400);

// Cooldown anti-spam
$tunggu = otpCooldownRemaining($user['otp_expired_at']);
if ($tunggu > 0) {
    sendError("Tunggu $tunggu detik sebelum meminta kode baru.", 429);
}

$otp         = generateOtp();
$otp_expired = getOtpExpiredTime();

if (!saveOtpToUser($conn, $user['id'], $otp, $otp_expired)) {
    sendError('Gagal membuat OTP baru', 500);
}
otpAttemptReset('verify:' . $email);

$email_sent = sendOtpEmail($email, $otp, $user['nama'] ?: 'User', 'register');

sendResponse([
    'email'      => $email,
    'email_sent' => $email_sent,
    'expired_at' => $otp_expired,
] + otpDebugData($otp),
    $email_sent ? 'Kode OTP baru telah dikirim ke email Anda' : 'Kode OTP dibuat, tetapi email gagal dikirim. Coba lagi beberapa saat.');
?>
