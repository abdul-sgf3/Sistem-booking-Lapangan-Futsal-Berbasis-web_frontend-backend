<?php
// ============================================================
// admin/lapangan_create.php - Tambah lapangan (multipart/form-data)
// POST /API/admin/lapangan_create.php  atau POST /API/admin/lapangan
// Form: nama_lapangan, harga_per_jam, status, deskripsi, lokasi, foto (opsional)
// ============================================================

require_once '../config.php';

// Route /admin/lapangan dipakai GET (list) & POST (tambah)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    require __DIR__ . '/lapangan_list.php';
    exit();
}

requireMethod('POST');
requireAdminLogin();

$nama      = trim($_POST['nama_lapangan'] ?? '');
$harga     = (int)($_POST['harga_per_jam'] ?? 0);
$status    = $_POST['status'] ?? 'aktif';
$deskripsi = trim($_POST['deskripsi'] ?? '');
$lokasi    = trim($_POST['lokasi'] ?? '');

if ($nama === '' || $harga <= 0) sendError('Nama dan Harga wajib diisi', 400);
if (!in_array($status, ['aktif', 'nonaktif'], true)) sendError('Status harus aktif atau nonaktif', 400);

$foto = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
    $up = handleImageUpload($_FILES['foto'], ABS_UPLOAD_LAPANGAN, 'lpgn', 5 * 1024 * 1024);
    if (!$up['success']) sendError('Upload foto gagal: ' . $up['error'], 400);
    $foto = $up['filename'];
}

try {
    $stmt = mysqli_prepare($conn,
        "INSERT INTO lapangan (nama_lapangan, harga_per_jam, status, deskripsi, lokasi, foto) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sissss", $nama, $harga, $status, $deskripsi, $lokasi, $foto);
    mysqli_stmt_execute($stmt);
} catch (Throwable $e) {
    if ($foto) deleteFile(ABS_UPLOAD_LAPANGAN . $foto);
    throw $e;
}

sendResponse(['id' => (int)mysqli_insert_id($conn), 'foto' => $foto, 'foto_url' => fotoLapanganUrl($foto)],
    'Lapangan berhasil ditambahkan', 201);
?>
