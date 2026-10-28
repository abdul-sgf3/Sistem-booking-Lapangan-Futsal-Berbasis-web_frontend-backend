<?php
require_once '../config.php';
requireMethod('POST');

$input = getJsonInput();
$email = trim($input['email'] ?? '');
$otp = trim($input['otp'] ?? '');

if (empty($email) || empty($otp)) sendError('Data tidak lengkap', 400);

$stmt = mysqli_prepare($conn, "SELECT id, otp_code, otp_expired_at FROM users WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$user) sendError('Email tidak terdaftar', 404);
if ($user['otp_code'] !== $otp) sendError('Kode OTP salah', 400);
if (strtotime($user['otp_expired_at']) < time()) sendError('Kode OTP kadaluarsa', 400);

$_SESSION['reset_verified'] = true;
$_SESSION['reset_email'] = $email;
sendResponse(['verified' => true], 'OTP Valid. Silakan ganti password.');
?>