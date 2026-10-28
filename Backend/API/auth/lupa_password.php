<?php
require_once '../config.php';
requireMethod('POST');

$input = getJsonInput();
$email = trim($input['email'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendError('Email tidak valid', 400);
}

$stmt = mysqli_prepare($conn, "SELECT id, nama FROM users WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$user) sendError('Email tidak terdaftar', 404);

$otp = generateOtp();
$otp_expired = getOtpExpiredTime(5);
saveOtpToUser($conn, $user['id'], $otp, $otp_expired);
sendOtpEmail($email, $otp, $user['nama'] ?? 'User');

$_SESSION['reset_email'] = $email;
sendResponse(['otp_debug' => $otp], 'Kode OTP telah dikirim ke email');
?>