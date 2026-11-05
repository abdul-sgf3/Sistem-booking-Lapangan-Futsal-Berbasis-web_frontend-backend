<?php
// ============================================================
// admin/login.php - Login Admin
// POST /API/admin/login.php   Body: { email, password }
// Wajib verifikasi CAPTCHA dulu (GET/POST /API/admin/captcha.php)
// ============================================================

require_once '../config.php';

requireMethod('POST');

$input    = getJsonInput();
$email    = strtolower(trim($input['email'] ?? ''));
$password = $input['password'] ?? '';

if ($email === '' || $password === '') sendError('Email dan password wajib diisi', 400);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) sendError('Format email tidak valid', 400);

// CAPTCHA wajib sudah diverifikasi (sekali pakai)
if (empty($_SESSION['admin_captcha_verified'])) {
    sendError('Silakan verifikasi CAPTCHA terlebih dahulu', 400);
}
unset($_SESSION['admin_captcha_verified']);

$stmt = mysqli_prepare($conn,
    "SELECT id, email, password, nama, role, is_active FROM users WHERE email = ? AND role = 'admin' LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$admin = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$admin) sendError('Email admin tidak terdaftar', 401);
if (!password_verify($password, $admin['password'])) sendError('Password salah', 401);
if (isset($admin['is_active']) && (int)$admin['is_active'] === 0) sendError('Akun admin dinonaktifkan', 403);

session_regenerate_id(true);
$_SESSION['admin_id']    = (int)$admin['id'];
$_SESSION['admin_email'] = $admin['email'];
$_SESSION['admin_nama']  = $admin['nama'] ?? 'Admin';

$stmt = mysqli_prepare($conn, "UPDATE users SET last_login = NOW() WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $admin['id']);
mysqli_stmt_execute($stmt);

sendResponse([
    'admin_id' => (int)$admin['id'],
    'email'    => $admin['email'],
    'nama'     => $admin['nama'] ?? 'Admin',
    'role'     => 'admin',
    'redirect' => 'admin/index.html',
], 'Login admin berhasil');
?>
