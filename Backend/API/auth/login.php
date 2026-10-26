<?php
// ============================================================
// auth/login.php - Login User
// POST /api/auth/login.php
// Body: { email, password }
// ============================================================

require_once '../config.php';

requireMethod('POST');

$input = getJsonInput();
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

// ===== VALIDASI =====
if (empty($email) || empty($password)) {
    sendError('Email dan password wajib diisi', 400);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendError('Format email tidak valid', 400);
}

// ===== CARI USER =====
$stmt = mysqli_prepare($conn, 
    "SELECT id, email, password, nama, whatsapp, foto_profil, role, is_verified 
     FROM users WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$user) {
    sendError('Email tidak terdaftar', 401);
}

// ===== CEK PASSWORD =====

if (!password_verify($password, $user['password'])) {
    sendError('Password salah', 401);
}


// ===== CEK VERIFIKASI EMAIL =====
if (!$user['is_verified']) {
    // Generate OTP baru untuk verifikasi
    $otp = generateOtp();
    $otp_expired = getOtpExpiredTime(5);
    
    saveOtpToUser($conn, $user['id'], $otp, $otp_expired);
    sendOtpEmail($email, $otp, $user['nama'] ?? 'User');
    
    $_SESSION['register_email'] = $email;
    $_SESSION['register_user_id'] = $user['id'];
    
    sendResponse([
        'need_verify' => true,
        'email' => $email,
        'otp_debug' => $otp, // HAPUS DI PRODUCTION
        'redirect' => BASE_URL . '/Frontend/autentikasi/verifikasi_email.php'
    ], 'Email belum diverifikasi. Silakan verifikasi terlebih dahulu.', 403);
}

// ===== CEK APAKAH PROFIL LENGKAP =====
$need_profile = empty($user['nama']) || empty($user['whatsapp']);

// ===== SET SESSION =====
$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['nama'] = $user['nama'] ?? '';
$_SESSION['whatsapp'] = $user['whatsapp'] ?? '';
$_SESSION['foto_profil'] = $user['foto_profil'] ?? '';
$_SESSION['role'] = $user['role'] ?? 'user';

// Update last login
$stmt = mysqli_prepare($conn, "UPDATE users SET last_login = NOW() WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user['id']);
mysqli_stmt_execute($stmt);

// ===== RESPONSE =====
sendResponse([
    'user_id' => (int)$user['id'],
    'email' => $user['email'],
    'nama' => $user['nama'] ?? '',
    'role' => $user['role'],
    'need_profile' => $need_profile,
    'redirect' => $need_profile 
        ? BASE_URL . '/Frontend/autentikasi/lengkapi_profil.html'
        : BASE_URL . '/Frontend/pages/index.html'
], 'Login berhasil');
?>