<?php
require_once '../config.php';
requireMethod('POST');

if (!isset($_SESSION['reset_verified']) || $_SESSION['reset_verified'] !== true) {
    sendError('Akses ditolak. Verifikasi OTP terlebih dahulu.', 403);
}

$input = getJsonInput();
$email = $_SESSION['reset_email'];
$password = $input['password'] ?? '';

if (strlen($password) < 6) sendError('Password minimal 6 karakter', 400);

$hashed = password_hash($password, PASSWORD_DEFAULT);
$stmt = mysqli_prepare($conn, "UPDATE users SET password = ?, otp_code = NULL, otp_expired_at = NULL WHERE email = ?");
mysqli_stmt_bind_param($stmt, "ss", $hashed, $email);

if (mysqli_stmt_execute($stmt)) {
    unset($_SESSION['reset_verified'], $_SESSION['reset_email']);
    sendResponse(null, 'Password berhasil diubah. Silakan login.');
} else {
    sendError('Gagal mengubah password', 500);
}
?>