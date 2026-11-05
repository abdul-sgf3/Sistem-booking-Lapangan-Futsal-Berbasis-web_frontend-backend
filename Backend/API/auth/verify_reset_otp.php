<?php
// ============================================================
// auth/verify_reset_otp.php - Verifikasi OTP reset password
// POST /API/auth/verify_reset_otp.php
// Body: { email, otp }
// ============================================================

require_once '../config.php';

requireMethod('POST');

$input = getJsonInput();
$email = strtolower(trim($input['email'] ?? ''));
$otp   = trim((string)($input['otp'] ?? $input['kode'] ?? ''));

if ($email === '' || $otp === '') sendError('Data tidak lengkap', 400);
if (!preg_match('/^\d{' . OTP_LENGTH . '}$/', $otp)) {
    sendError('Kode OTP harus berupa ' . OTP_LENGTH . ' digit angka', 400);
}
if (!otpAttemptAllowed('reset:' . $email)) {
    sendError('Terlalu banyak percobaan salah. Minta kode baru.', 429);
}

$stmt = mysqli_prepare($conn, "SELECT id, otp_code, otp_expired_at FROM users WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$user) sendError('Email tidak terdaftar', 404);
if (empty($user['otp_code']) || !hash_equals((string)$user['otp_code'], $otp)) {
    otpAttemptFail('reset:' . $email);
    sendError('Kode OTP salah', 400);
}
if (isOtpExpired($user['otp_expired_at'])) sendError('Kode OTP kadaluarsa', 400);

otpAttemptReset('reset:' . $email);
$_SESSION['reset_verified'] = true;
$_SESSION['reset_email']    = $email;

sendResponse(['verified' => true], 'OTP Valid. Silakan ganti password.');
?>
