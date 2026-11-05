<?php
// ============================================================
// auth/login.php - Login User
// POST /API/auth/login.php   (atau /API/auth/login)
// Body: { email, password }
// ============================================================

require_once '../config.php';

requireMethod('POST');

$input    = getJsonInput();
$email    = strtolower(trim($input['email'] ?? ''));
$password = $input['password'] ?? '';

// ===== VALIDASI =====
if ($email === '' || $password === '') {
    sendError('Email dan password wajib diisi', 400);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendError('Format email tidak valid', 400);
}

// ===== CARI USER =====
$stmt = mysqli_prepare($conn,
    "SELECT id, email, password, nama, whatsapp, foto_profil, role, is_verified, is_active, otp_expired_at
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

// ===== CEK AKUN AKTIF =====
if (isset($user['is_active']) && (int)$user['is_active'] === 0) {
    sendError('Akun Anda dinonaktifkan. Hubungi admin.', 403);
}

// ===== CEK VERIFIKASI EMAIL =====
if (!$user['is_verified']) {
    // Kirim OTP baru hanya jika OTP sebelumnya sudah lewat cooldown (cegah spam email)
    $otp        = null;
    $email_sent = false;
    if (otpCooldownRemaining($user['otp_expired_at']) === 0) {
        $otp = generateOtp();
        saveOtpToUser($conn, $user['id'], $otp, getOtpExpiredTime());
        $email_sent = sendOtpEmail($email, $otp, $user['nama'] ?: 'User', 'register');
    }

    $_SESSION['register_email']   = $email;
    $_SESSION['register_user_id'] = (int)$user['id'];

    sendErrorData('Email belum diverifikasi. Silakan verifikasi terlebih dahulu.', [
        'need_verify' => true,
        'email'       => $email,
        'email_sent'  => $email_sent,
        'redirect'    => 'verifikasi.html',
    ] + ($otp ? otpDebugData($otp) : []), 403);
}

// ===== CEK APAKAH PROFIL LENGKAP =====
$need_profile = empty($user['nama']) || empty($user['whatsapp']);

// ===== SET SESSION =====
session_regenerate_id(true);   // cegah session fixation
$_SESSION['user_id']     = (int)$user['id'];
$_SESSION['user_email']  = $user['email'];
$_SESSION['nama']        = $user['nama'] ?? '';
$_SESSION['whatsapp']    = $user['whatsapp'] ?? '';
$_SESSION['foto_profil'] = $user['foto_profil'] ?? '';
$_SESSION['role']        = $user['role'] ?? 'user';

// Update last login
$stmt = mysqli_prepare($conn, "UPDATE users SET last_login = NOW() WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user['id']);
mysqli_stmt_execute($stmt);

// ===== RESPONSE =====
sendResponse([
    'user_id'         => (int)$user['id'],
    'email'           => $user['email'],
    'nama'            => $user['nama'] ?? '',
    'whatsapp'        => $user['whatsapp'] ?? '',
    'foto_profil_url' => fotoProfilUrl($user['foto_profil'] ?? ''),
    'role'            => $user['role'],
    'need_profile'    => $need_profile,
    'redirect'        => $need_profile ? 'lengkapi_profil.html' : 'index.html',
], 'Login berhasil');
?>
