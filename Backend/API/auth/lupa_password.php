<?php
// ============================================================
// auth/lupa_password.php - Minta OTP reset password
// POST /API/auth/lupa_password.php
// Body: { email }
// ============================================================

require_once '../config.php';

requireMethod('POST');

$input = getJsonInput();
$email = strtolower(trim((string)($input['email'] ?? '')));

// ===== VALIDASI EMAIL =====
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendError('Email tidak valid', 400);
}

// ===== CARI AKUN =====
$stmt = mysqli_prepare($conn, "SELECT id, nama, is_verified, otp_expired_at FROM users WHERE email = ? LIMIT 1");

mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$user || !(int)$user['is_verified']) {
    sendError('Akun tidak ditemukan atau belum terverifikasi.', 404);
}

// ===== RESET SESSION RESET PASSWORD LAMA =====
unset($_SESSION['reset_verified'], $_SESSION['reset_email']);

// ===== RESET PERCOBAAN OTP =====
otpAttemptReset('reset:' . $email);

// ===== CEK COOLDOWN =====
$tunggu = otpCooldownRemaining($user['otp_expired_at']);

if ($tunggu > 0) {
    sendError("Tunggu $tunggu detik sebelum meminta kode baru.", 429);
}

// ===== GENERATE OTP =====
$otp         = generateOtp();
$otp_expired = getOtpExpiredTime();

// ===== SIMPAN OTP =====
if (!saveOtpToUser($conn, $user['id'], $otp, $otp_expired)) {
    sendError('Gagal membuat OTP. Silakan coba lagi.', 500);
}

// ===== KIRIM EMAIL BREVO =====
$email_sent = sendOtpEmail(
    $email,
    $otp,
    $user['nama'] ?: 'User',
    'reset'
);

// Jika gagal dikirim, hapus OTP
if (!$email_sent) {
    clearOtpFromUser($conn, (int)$user['id']);

    sendError('Email OTP gagal dikirim. Silakan coba kembali.', 502);
}

// ===== RESPONSE =====
sendResponse([
    'email'      => $email,
    'email_sent' => true,
    'expired_at' => $otp_expired,
], 'Kode OTP reset password berhasil dikirim ke email Anda.');

?>
