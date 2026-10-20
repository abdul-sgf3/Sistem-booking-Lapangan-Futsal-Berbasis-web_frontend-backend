<?php
require_once '../config.php';
requireMethod('POST');

if (!isset($_SESSION['admin_id'])) sendError('Akses ditolak', 403);

$nama = trim($_POST['nama_lapangan'] ?? '');
$harga = (int)($_POST['harga_per_jam'] ?? 0);
$status = $_POST['status'] ?? 'aktif';
$deskripsi = trim($_POST['deskripsi'] ?? '');
$lokasi = trim($_POST['lokasi'] ?? '');

if (empty($nama) || $harga <= 0) {
    sendError('Nama dan Harga wajib diisi', 400);
}

// Upload foto (opsional)
$foto = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = __DIR__ . '/../../Frontend/assets/uploads/lapangan/';
    $upload = handleImageUpload($_FILES['foto'], $upload_dir, 'lpgn');
    if ($upload['success']) $foto = $upload['filename'];
}

$stmt = mysqli_prepare($conn, "INSERT INTO lapangan (nama_lapangan, harga_per_jam, status, deskripsi, lokasi, foto) VALUES (?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "sissss", $nama, $harga, $status, $deskripsi, $lokasi, $foto);

if (mysqli_stmt_execute($stmt)) {
    sendResponse(['id' => mysqli_insert_id($conn)], 'Lapangan berhasil ditambahkan');
} else {
    sendError('Gagal menambah lapangan', 500);
}
?>