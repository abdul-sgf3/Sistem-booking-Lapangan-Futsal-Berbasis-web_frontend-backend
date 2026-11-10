<?php
// ============================================================
// auth/register.php - Registrasi User
// POST /API/auth/register.php   (atau /API/auth/register)
// Body: { email, password, confirm_password }
// ============================================================

require_once '../config.php';   // CORS, session, DB, preflight ditangani di config.php

requireMethod('POST');

$input      = getJsonInput();
$email      = strtolower(trim($input['email'] ?? ''));
$password   = $input['password'] ?? '';
$konfirmasi = $input['confirm_password'] ?? $input['konfirmasi_password'] ?? $password;

// ===== VALIDASI INPUT =====
if ($email === '' || $password === '') {
    sendError('Semua field (Email, Password, Konfirmasi) wajib diisi.', 400);
}
if (strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendError('Format email tidak valid.', 400);
}
if ($password !== $konfirmasi) {
    sendError('Password dan Konfirmasi Password tidak cocok.', 400);
}
if ($err = validatePasswordStrength($password)) {
    sendError($err, 400);
}

// ===== CEK APAKAH EMAIL SUDAH ADA =====
$stmt = mysqli_prepare($conn, "SELECT id, nama, is_verified FROM users WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$existing = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if ($existing && $existing['is_verified']) {
    sendError('Email sudah terdaftar dan terverifikasi. Silakan login.', 409);
}

$otp         = generateOtp();
$otp_expired = getOtpExpiredTime();
$hashed      = password_hash($password, PASSWORD_DEFAULT);

try {
    if ($existing) {
        // Email terdaftar tapi BELUM verifikasi -> perbarui password & OTP
        otpCooldownRemaining($existing['otp_expired_at']);
        $stmt = mysqli_prepare($conn, "UPDATE users SET password = ?, otp_code = ?, otp_expired_at = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "sssi", $hashed, $otp, $otp_expired, $existing['id']);
        mysqli_stmt_execute($stmt);
        $user_id = (int)$existing['id'];
        $nama    = $existing['nama'] ?: explode('@', $email)[0];
    } else {
        // Insert user BARU
        $nama = explode('@', $email)[0];
        $stmt = mysqli_prepare($conn,
            "INSERT INTO users (nama, email, password, role, otp_code, otp_expired_at, is_verified)
             VALUES (?, ?, ?, 'user', ?, ?, 0)");
        mysqli_stmt_bind_param($stmt, "sssss", $nama, $email, $hashed, $otp, $otp_expired);
        mysqli_stmt_execute($stmt);
        $user_id = (int)mysqli_insert_id($conn);
    }
} catch (mysqli_sql_exception $e) {
    if ((int)$e->getCode() === 1062) {   // duplicate entry (dua request bersamaan)
        sendError('Email sudah terdaftar dan terverifikasi. Silakan login.', 409);
    }
    throw $e;   // ditangani handler global -> JSON 500 + tercatat di log
}

// ===== SESSION SEMENTARA =====
$_SESSION['register_email']   = $email;
$_SESSION['register_user_id'] = $user_id;

// ===== KIRIM OTP VIA EMAIL =====
$email_sent = sendOtpEmail($email, $otp, $nama, 'register');

$data = [
    'user_id'    => $user_id,
    'email'      => $email,
    'email_sent' => $email_sent,
    'expired_at' => $otp_expired,
    'redirect'   => 'verifikasi.html',
] + otpDebugData($otp);

sendResponse($data, $email_sent
    ? 'Registrasi berhasil. Kode OTP telah dikirim ke email Anda.'
    : 'Registrasi berhasil, tetapi email OTP gagal dikirim. Silakan gunakan tombol Kirim Ulang.', 201);
?>
