<?php
// ============================================================
// auth/lengkapi_profil.php - Lengkapi Profil User
// POST /api/auth/lengkapi_profil.php (multipart/form-data)
// Form: nama, whatsapp, foto_profil (opsional)
// ============================================================

require_once '../config.php';

// Hanya izinkan POST
requireMethod('POST');

// Cek login
if (!isset($_SESSION['user_id'])) {
    sendError('Anda belum login. Silakan login terlebih dahulu.', 401);
}

$user_id = $_SESSION['user_id'];
$nama = trim($_POST['nama'] ?? '');
$whatsapp = trim($_POST['whatsapp'] ?? '');

// ===== VALIDASI =====
if (empty($nama)) {
    sendError('Nama wajib diisi', 400);
}
if (strlen($nama) < 2 || strlen($nama) > 100) {
    sendError('Nama harus antara 2-100 karakter', 400);
}
if (empty($whatsapp)) {
    sendError('Nomor WhatsApp wajib diisi', 400);
}

// Normalisasi WhatsApp: hapus karakter non-digit
$whatsapp_clean = preg_replace('/[^0-9]/', '', $whatsapp);
if (strlen($whatsapp_clean) < 10 || strlen($whatsapp_clean) > 15) {
    sendError('Nomor WhatsApp harus 10-15 digit', 400);
}
$whatsapp = $whatsapp_clean;

// ===== CEK APAKAH USER ADA =====
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
if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
    $allowed_exts  = ['jpg', 'jpeg', 'png'];
    $max_size      = 2 * 1024 * 1024; // 2MB
    
    // Cek MIME type
    if (!in_array($_FILES['foto_profil']['type'], $allowed_types)) {
        sendError('Format foto harus JPG atau PNG', 400);
    }
    
    // Cek ekstensi
    $ext = strtolower(pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_exts)) {
        sendError('Ekstensi foto harus .jpg, .jpeg, atau .png', 400);
    }
    
    // Cek ukuran
    if ($_FILES['foto_profil']['size'] > $max_size) {
        sendError('Ukuran foto maksimal 2MB', 400);
    }
    
    // Cek apakah file benar-benar gambar
    $image_info = @getimagesize($_FILES['foto_profil']['tmp_name']);
    if ($image_info === false) {
        sendError('File yang diupload bukan gambar valid', 400);
    }
    
    // Tentukan folder upload
    // File ini di: Backend/API/auth/
    // Target:       Frontend/assets/uploads/profil/
    // Naik 3 level: auth/ → API/ → Backend/ → root
    $upload_dir = __DIR__ . '/../../../Frontend/assets/uploads/profil/';
    
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            sendError('Gagal membuat folder upload', 500);
        }
    }
    
    // Generate nama file unik
    $foto_baru = 'user_' . $user_id . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $target_path = $upload_dir . $foto_baru;
    
    // Upload file
    if (!move_uploaded_file($_FILES['foto_profil']['tmp_name'], $target_path)) {
        sendError('Gagal mengupload foto', 500);
    }
    
    // Hapus foto lama jika ada
    if (!empty($foto_lama) && file_exists($upload_dir . $foto_lama)) {
        @unlink($upload_dir . $foto_lama);
    }
}

// ===== UPDATE DATABASE =====
if ($foto_baru) {
    // Update dengan foto baru
    $stmt = mysqli_prepare($conn, 
        "UPDATE users SET nama = ?, whatsapp = ?, foto_profil = ?, updated_at = NOW() 
         WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sssi", $nama, $whatsapp, $foto_baru, $user_id);
} else {
    // Update tanpa foto
    $stmt = mysqli_prepare($conn, 
        "UPDATE users SET nama = ?, whatsapp = ?, updated_at = NOW() 
         WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $nama, $whatsapp, $user_id);
}

if (!mysqli_stmt_execute($stmt)) {
    // Jika gagal update, hapus foto yang baru diupload
    if ($foto_baru && file_exists($upload_dir . $foto_baru)) {
        @unlink($upload_dir . $foto_baru);
    }
    sendError('Gagal menyimpan data: ' . mysqli_error($conn), 500);
}

// ===== UPDATE SESSION =====
$_SESSION['nama'] = $nama;
$_SESSION['whatsapp'] = $whatsapp;
if ($foto_baru) {
    $_SESSION['foto_profil'] = $foto_baru;
}

// ===== RESPONSE =====
$foto_final = $foto_baru ?? $foto_lama;
$foto_url = !empty($foto_final) 
    ? BASE_URL . '/Frontend/assets/uploads/profil/' . rawurlencode($foto_final)
    : '';

sendResponse([
    'user' => [
        'id' => $user_id,
        'nama' => $nama,
        'whatsapp' => $whatsapp,
        'foto_profil' => $foto_final,
        'foto_profil_url' => $foto_url,
        'initial' => strtoupper(substr($nama, 0, 1))
    ],
    'redirect' => BASE_URL . '/Frontend/pages/index.php'
], 'Profil berhasil dilengkapi');
?>