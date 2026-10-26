<?php
// ============================================================
// admin/login.php - Login Admin
// POST /api/admin/login.php
// Body: { email, password }
// ============================================================

require_once '../config.php';

requireMethod('POST');

$input = getJsonInput();
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

if (empty($email) || empty($password)) {
    sendError('Email dan password wajib diisi', 400);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendError('Format email tidak valid', 400);
}

// Setelah validasi email & password, SEBELUM cek admin:
if (!isset($_SESSION['admin_captcha_verified']) || !$_SESSION['admin_captcha_verified']) {
    sendError('Silakan verifikasi CAPTCHA terlebih dahulu', 400);
}
// Reset flag setelah login berhasil
unset($_SESSION['admin_captcha_verified']);

// Cari admin di database (role = 'admin')
$stmt = mysqli_prepare($conn, 
    "SELECT id, email, password, nama, role 
     FROM users WHERE email = ? AND role = 'admin' LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$admin = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$admin) {
    sendError('Email admin tidak terdaftar', 401);
}

if (!password_verify($password, $admin['password'])) {
    sendError('Password salah', 401);
}

// Set session admin
$_SESSION['admin_id'] = (int)$admin['id'];
$_SESSION['admin_email'] = $admin['email'];
$_SESSION['admin_nama'] = $admin['nama'] ?? 'Admin';
$_SESSION['role'] = 'admin';

// Update last login
$stmt = mysqli_prepare($conn, "UPDATE users SET last_login = NOW() WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $admin['id']);
mysqli_stmt_execute($stmt);

sendResponse([
    'admin_id' => (int)$admin['id'],
    'email' => $admin['email'],
    'nama' => $admin['nama'] ?? 'Admin',
    'role' => 'admin',
    'redirect' => BASE_URL . '/Frontend/admin/index.html'
], 'Login admin berhasil');
?>