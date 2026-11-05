<?php
// ============================================================
// auth/check.php - Cek Status Login User
// GET /API/auth/check.php   (atau /API/auth/check)
// Dipakai Frontend untuk navbar: tombol Login/Register atau "Hai, {nama}"
// ============================================================

require_once '../config.php';

requireMethod('GET');

if (!isset($_SESSION['user_id'])) {
    sendResponse(['logged_in' => false, 'user' => null], 'User belum login');
}

$user_id = (int)$_SESSION['user_id'];

$stmt = mysqli_prepare($conn,
    "SELECT id, email, nama, whatsapp, foto_profil, role, is_active
       FROM users WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Session ada tapi user sudah dihapus / dinonaktifkan admin
if (!$user || (isset($user['is_active']) && (int)$user['is_active'] === 0)) {
    unset($_SESSION['user_id'], $_SESSION['user_email'], $_SESSION['nama'],
          $_SESSION['whatsapp'], $_SESSION['foto_profil']);
    sendResponse(['logged_in' => false, 'user' => null], 'Session tidak valid');
}

sendResponse([
    'logged_in' => true,
    'user' => [
        'id'              => (int)$user['id'],
        'email'           => $user['email'],
        'nama'            => $user['nama'] ?? '',
        'whatsapp'        => $user['whatsapp'] ?? '',
        'foto_profil'     => $user['foto_profil'] ?? '',
        'foto_profil_url' => fotoProfilUrl($user['foto_profil'] ?? ''),
        'role'            => $user['role'] ?? 'user',
        'need_profile'    => empty($user['whatsapp']),
        'initial'         => strtoupper(substr($user['nama'] ?: $user['email'], 0, 1)),
    ]
], 'User sudah login');
?>
