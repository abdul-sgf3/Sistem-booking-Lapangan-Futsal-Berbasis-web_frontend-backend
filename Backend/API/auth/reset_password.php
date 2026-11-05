<?php
// ============================================================
// auth/reset_password.php - Simpan password baru (setelah OTP valid)
// POST /API/auth/reset_password.php
// Body: { password, confirm_password (opsional) }
// ============================================================

require_once '../config.php';

requireMethod('POST');

if (empty($_SESSION['reset_verified']) || empty($_SESSION['reset_email'])) {
    sendError('Akses ditolak. Verifikasi OTP terlebih dahulu.', 403);
}

$input      = getJsonInput();
$email      = $_SESSION['reset_email'];
$password   = $input['password'] ?? '';
$konfirmasi = $input['confirm_password'] ?? $input['konfirmasi_password'] ?? $password;

if ($password !== $konfirmasi) sendError('Password dan Konfirmasi Password tidak cocok.', 400);
if ($err = validatePasswordStrength($password)) sendError($err, 400);

$hashed = password_hash($password, PASSWORD_DEFAULT);
$stmt = mysqli_prepare($conn,
    "UPDATE users SET password = ?, otp_code = NULL, otp_expired_at = NULL WHERE email = ?");
mysqli_stmt_bind_param($stmt, "ss", $hashed, $email);
mysqli_stmt_execute($stmt);

unset($_SESSION['reset_verified'], $_SESSION['reset_email']);
session_regenerate_id(true);

sendResponse(['redirect' => 'login.html'], 'Password berhasil diubah. Silakan login.');
?>
