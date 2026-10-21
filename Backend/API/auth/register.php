<?php
// ============================================================
// auth/register.php - Registrasi User dengan OTP
// POST /api/auth/register.php
// Body: { email, password, konfirmasi_password }
// ============================================================

require_once '../config.php';

requireMethod('POST');

$input = getJsonInput();
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$konfirmasi = $input['konfirmasi_password'] ?? $input['confirm_password'] ?? $input['password'] ?? '';

// ===== VALIDASI =====
if (empty($email) || empty($password) || empty($konfirmasi)) {
    sendError('Semua field wajib diisi', 400);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendError('Format email tidak valid', 400);
}
if ($password !== $konfirmasi) {
    sendError('Password dan konfirmasi tidak cocok', 400);
}
if (strlen($password) < 6) {
    sendError('Password minimal 6 karakter', 400);
}

// ===== CEK EMAIL SUDAH TERDAFTAR =====
$stmt = mysqli_prepare($conn, 
    "SELECT id, is_verified FROM users WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$existing = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Generate OTP
$otp = generateOtp();
$otp_expired = getOtpExpiredTime(5); // 5 menit
$hashed = password_hash($password, PASSWORD_DEFAULT);

if ($existing) {
    if ($existing['is_verified']) {
        sendError('Email sudah terdaftar. Silakan login.', 409);
    }
    
    // Email terdaftar tapi belum verifikasi → update
    $stmt = mysqli_prepare($conn, 
        "UPDATE users SET password = ?, otp_code = ?, otp_expired_at = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sssi", 
        $hashed, $otp, $otp_expired, $existing['id']);
    mysqli_stmt_execute($stmt);
    
    $user_id = $existing['id'];
} else {
    // Insert user baru
        $nama_default = explode('@', $email)[0]; // Ambil teks sebelum '@'
    
    $stmt = mysqli_prepare($conn, 
        "INSERT INTO users (nama, email, password, role, otp_code, otp_expired_at, is_verified) 
         VALUES (?, ?, ?, 'user', ?, ?, 0)");
    
    // Perhatikan ada tambahan 's' pertama untuk $nama_default
    mysqli_stmt_bind_param($stmt, "sssss", 
        $nama_default, $email, $hashed, $otp, $otp_expired);
    
    if (!mysqli_stmt_execute($stmt)) {
        sendError('Gagal mendaftar: ' . mysqli_error($conn), 500);
    }
    
    $user_id = mysqli_insert_id($conn);
}

// Set session sementara untuk verifikasi
$_SESSION['register_email'] = $email;
$_SESSION['register_user_id'] = $user_id;

// Kirim OTP via email (placeholder)
sendOtpEmail($email, $otp);

sendResponse([
    'user_id' => (int)$user_id,
    'email' => $email,
    'otp_debug' => $otp, // HAPUS DI PRODUCTION!
    'expired_at' => $otp_expired,
    'redirect' => BASE_URL . '/Frontend/autentikasi/verifikasi_email.php'
], 'Registrasi berhasil. Cek email untuk kode OTP.');
?>