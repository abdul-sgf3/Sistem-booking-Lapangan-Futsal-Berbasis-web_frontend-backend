<?php
// ============================================================
// auth/lupa_password.php - Minta OTP reset password
// POST /API/auth/lupa_password.php
// Body: { email }
// ============================================================

require_once '../config.php';

requireMethod('POST');

$input = getJsonInput();
$email = strtolower(trim($input['email'] ?? ''));

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendError('Email tidak valid', 400);
}

$stmt = mysqli_prepare($conn, "SELECT id, nama, otp_expired_at FROM users WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$user) sendError('Email tidak terdaftar', 404);

$tunggu = otpCooldownRemaining($user['otp_expired_at']);
if ($tunggu > 0) {
    sendError("Tunggu $tunggu detik sebelum meminta kode baru.", 429);
}

$otp         = generateOtp();
$otp_expired = getOtpExpiredTime();
saveOtpToUser($conn, $user['id'], $otp, $otp_expired);
$email_sent = sendOtpEmail($email, $otp, $user['nama'] ?: 'User', 'reset');

// Reset status verifikasi reset sebelumnya
unset($_SESSION['reset_verified']);
$_SESSION['reset_email'] = $email;
otpAttemptReset('reset:' . $email);

sendResponse([
    'email'      => $email,
    'email_sent' => $email_sent,
    'expired_at' => $otp_expired,
] + otpDebugData($otp),
    $email_sent ? 'Kode OTP telah dikirim ke email' : 'Kode OTP dibuat, tetapi email gagal dikirim. Coba lagi beberapa saat.');
?>
