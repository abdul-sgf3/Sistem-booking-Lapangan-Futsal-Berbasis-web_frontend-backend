<?php
// ============================================================
// auth/check.php - Cek Status Login User
// GET /api/auth/check.php
// 
// Digunakan oleh frontend (header.php) untuk:
// - Menampilkan tombol Login/Register jika belum login
// - Menampilkan "Hai, {nama}!" jika sudah login
// ============================================================

require_once '../config.php';

// Hanya izinkan GET
requireMethod('GET');

// Cek session user
if (!isset($_SESSION['user_id'])) {
    // User belum login
    sendResponse([
        'logged_in' => false,
        'user' => null
    ], 'User belum login');
}

// User sudah login - ambil data dari session
// Opsional: ambil data terbaru dari database
$user_id = $_SESSION['user_id'];

$stmt = mysqli_prepare($conn, 
    "SELECT id, email, nama, whatsapp, foto_profil, role 
     FROM users WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    // Session ada tapi user tidak ditemukan di DB (mungkin dihapus)
    session_unset();
    session_destroy();
    sendResponse([
        'logged_in' => false,
        'user' => null
    ], 'Session tidak valid');
}

// Format response
sendResponse([
    'logged_in' => true,
    'user' => [
        'id' => (int)$user['id'],
        'email' => $user['email'],
        'nama' => $user['nama'] ?? '',
        'whatsapp' => $user['whatsapp'] ?? '',
        'foto_profil' => $user['foto_profil'] ?? '',
        'foto_profil_url' => !empty($user['foto_profil']) 
            ? BASE_URL . '/Frontend/assets/uploads/profil/' . rawurlencode($user['foto_profil'])
            : '',
        'role' => $user['role'] ?? 'user',
        'initial' => strtoupper(substr($user['nama'] ?? $user['email'], 0, 1))
    ]
], 'User sudah login');
?>