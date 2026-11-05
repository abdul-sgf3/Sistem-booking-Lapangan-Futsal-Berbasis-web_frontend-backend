<?php
// ============================================================
// auth/lengkapi_profil.php - Lengkapi Profil User
// POST /API/auth/lengkapi_profil.php  (multipart/form-data atau JSON)
// Form: nama, whatsapp, foto_profil (opsional)
// ============================================================

require_once '../config.php';

requireMethod('POST');

if (!isset($_SESSION['user_id'])) {
    sendError('Anda belum login. Silakan login terlebih dahulu.', 401);
}
$user_id = (int)$_SESSION['user_id'];

$input    = getJsonInput();            // JSON atau form-data ($_POST)
$nama     = trim($input['nama'] ?? '');
$whatsapp = trim($input['whatsapp'] ?? '');

// ===== VALIDASI =====
if ($nama === '') {
    sendError('Nama wajib diisi', 400);
}
if (mb_strlen($nama) < 2 || mb_strlen($nama) > 100) {
    sendError('Nama harus antara 2-100 karakter', 400);
}
if ($whatsapp === '') {
    sendError('Nomor WhatsApp wajib diisi', 400);
}
$whatsapp = preg_replace('/[^0-9]/', '', $whatsapp);
if (strlen($whatsapp) < 10 || strlen($whatsapp) > 15) {
    sendError('Nomor WhatsApp harus 10-15 digit', 400);
}

// ===== CEK USER =====
$stmt = mysqli_prepare($conn, "SELECT id, foto_profil FROM users WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$user) {
    sendError('User tidak ditemukan', 404);
}
$foto_lama = $user['foto_profil'] ?? null;

// ===== UPLOAD FOTO (OPSIONAL) =====
$foto_baru = null;
if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] !== UPLOAD_ERR_NO_FILE) {
    $up = handleImageUpload($_FILES['foto_profil'], ABS_UPLOAD_PROFIL, 'user_' . $user_id, 2 * 1024 * 1024);
    if (!$up['success']) {
        sendError($up['error'], 400);
    }
    $foto_baru = $up['filename'];
}

// ===== UPDATE DATABASE =====
if ($foto_baru) {
    $stmt = mysqli_prepare($conn, "UPDATE users SET nama = ?, whatsapp = ?, foto_profil = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sssi", $nama, $whatsapp, $foto_baru, $user_id);
} else {
    $stmt = mysqli_prepare($conn, "UPDATE users SET nama = ?, whatsapp = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $nama, $whatsapp, $user_id);
}

try {
    mysqli_stmt_execute($stmt);
} catch (Throwable $e) {
    if ($foto_baru) deleteFile(ABS_UPLOAD_PROFIL . $foto_baru);
    throw $e;
}

// Hapus foto lama setelah update sukses
if ($foto_baru && !empty($foto_lama)) {
    deleteFile(ABS_UPLOAD_PROFIL . basename($foto_lama));
}

// ===== UPDATE SESSION =====
$_SESSION['nama']     = $nama;
$_SESSION['whatsapp'] = $whatsapp;
if ($foto_baru) $_SESSION['foto_profil'] = $foto_baru;

$foto_final = $foto_baru ?? $foto_lama;

sendResponse([
    'user' => [
        'id'              => $user_id,
        'nama'            => $nama,
        'whatsapp'        => $whatsapp,
        'foto_profil'     => $foto_final,
        'foto_profil_url' => fotoProfilUrl($foto_final),
        'initial'         => strtoupper(mb_substr($nama, 0, 1)),
    ],
    'redirect' => 'index.html',
], 'Profil berhasil dilengkapi');
?>
