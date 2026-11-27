<?php
// ============================================================
// auth/reset_password.php - Simpan password baru (setelah OTP valid)
// POST /API/auth/reset_password.php
// Body: { password, confirm_password (opsional, sama seperti register.php) }
// ============================================================

require_once '../config.php';

requireMethod('POST');

// ===== VALIDASI SESSION RESET OTP =====
if (empty($_SESSION['reset_verified']) || empty($_SESSION['reset_email'])) {
    sendError('Akses ditolak. Verifikasi OTP terlebih dahulu.', 403);
}

$input      = getJsonInput();
$email      = strtolower(trim((string)$_SESSION['reset_email']));
$password   = (string)($input['password'] ?? '');
$konfirmasi = (string)($input['confirm_password'] ?? $input['konfirmasi_password'] ?? $password);

// ===== VALIDASI INPUT (diselaraskan dengan register.php) =====
if ($email === '' || $password === '') {
    sendError('Semua field (Password, Konfirmasi) wajib diisi.', 400);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendError('Email session tidak valid. Silakan ulangi proses reset password.', 400);
}
if ($password !== $konfirmasi) {
    sendError('Password dan Konfirmasi Password tidak cocok.', 400);
}
if ($err = validatePasswordStrength($password)) {
    sendError($err, 400);
}

// ===== HASH PASSWORD (sama dengan register.php) =====
$hashed = password_hash($password, PASSWORD_DEFAULT);
if ($hashed === false) {
    sendError('Gagal memproses password. Silakan coba lagi.', 500);
}

// ===== PASTIKAN AKUN ADA =====
$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$user) {
    sendError('Akun tidak ditemukan. Silakan ulangi proses reset password.', 404);
}

// ===== UPDATE PASSWORD DAN HAPUS OTP =====
try {
    $stmt = mysqli_prepare($conn,
        "UPDATE users SET password = ?, otp_code = NULL, otp_expired_at = NULL WHERE email = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "ss", $hashed, $email);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) !== 1) {
        sendError('Password gagal diperbarui. Silakan coba lagi.', 500);
    }
} catch (mysqli_sql_exception $e) {
    error_log('[RESET_PASSWORD] Gagal memperbarui password: ' . $e->getMessage());
    sendError('Terjadi kesalahan saat memperbarui password. Silakan coba lagi.', 500);
}

// ===== HAPUS SESSION RESET SETELAH BERHASIL =====
unset($_SESSION['reset_verified'], $_SESSION['reset_email']);
session_regenerate_id(true);

sendResponse(
    ['redirect' => 'login.html'],
    'Password berhasil diubah. Silakan login.'
);
?>
