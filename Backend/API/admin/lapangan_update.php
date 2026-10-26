<?php
require_once '../config.php';
requireMethod('POST');

if (!isset($_SESSION['admin_id'])) sendError('Akses ditolak', 403);

$id = (int)($_POST['id'] ?? 0);
$nama = trim($_POST['nama_lapangan'] ?? '');
$harga = (int)($_POST['harga_per_jam'] ?? 0);
$status = $_POST['status'] ?? 'aktif';
$deskripsi = trim($_POST['deskripsi'] ?? '');
$lokasi = trim($_POST['lokasi'] ?? '');

if ($id <= 0 || empty($nama) || $harga <= 0) {
    sendError('Data tidak valid', 400);
}

// Cek lapangan ada atau tidak
$stmt = mysqli_prepare($conn, "SELECT foto FROM lapangan WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$lapangan = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$lapangan) {
    sendError('Lapangan tidak ditemukan', 404);
}

$foto_lama = $lapangan['foto'];
$foto_baru = $foto_lama;

// Upload foto baru kalau ada
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = __DIR__ . '/../../Frontend/assets/uploads/lapangan/';
    $upload = handleImageUpload($_FILES['foto'], $upload_dir, 'lpgn');
    
    if ($upload['success']) {
        $foto_baru = $upload['filename'];
        // Hapus foto lama
        if (!empty($foto_lama) && file_exists($upload_dir . $foto_lama)) {
            @unlink($upload_dir . $foto_lama);
        }
    } else {
        sendError('Upload foto gagal: ' . $upload['error'], 400);
    }
}

// Update semua field
$stmt = mysqli_prepare($conn, 
    "UPDATE lapangan SET nama_lapangan=?, harga_per_jam=?, status=?, deskripsi=?, lokasi=?, foto=? 
     WHERE id=?");
mysqli_stmt_bind_param($stmt, "sissssi", 
    $nama, $harga, $status, $deskripsi, $lokasi, $foto_baru, $id);

if (mysqli_stmt_execute($stmt)) {
    sendResponse([
        'id' => $id,
        'foto' => $foto_baru,
        'foto_url' => fotoLapanganUrl($foto_baru)
    ], 'Lapangan berhasil diperbarui');
} else {
    sendError('Gagal update lapangan', 500);
}
?>