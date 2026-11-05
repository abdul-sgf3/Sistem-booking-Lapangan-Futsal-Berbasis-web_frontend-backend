<?php
// ============================================================
// admin/lapangan_update.php - Edit lapangan (multipart/form-data)
// POST /API/admin/lapangan_update.php  (field id di form)
// POST /API/admin/lapangan/{id}        (id dari URL)
// ============================================================

require_once '../config.php';

requireMethod('POST');
requireAdminLogin();

$id        = getRequestId($_POST, ['id']);
$nama      = trim($_POST['nama_lapangan'] ?? '');
$harga     = (int)($_POST['harga_per_jam'] ?? 0);
$status    = $_POST['status'] ?? 'aktif';
$deskripsi = trim($_POST['deskripsi'] ?? '');
$lokasi    = trim($_POST['lokasi'] ?? '');

if ($id <= 0 || $nama === '' || $harga <= 0) sendError('Data tidak valid', 400);
if (!in_array($status, ['aktif', 'nonaktif'], true)) sendError('Status harus aktif atau nonaktif', 400);

$stmt = mysqli_prepare($conn, "SELECT foto FROM lapangan WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$lapangan = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$lapangan) sendError('Lapangan tidak ditemukan', 404);

$foto_lama = $lapangan['foto'];
$foto_baru = $foto_lama;
$upload_baru = false;

if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
    $up = handleImageUpload($_FILES['foto'], ABS_UPLOAD_LAPANGAN, 'lpgn', 5 * 1024 * 1024);
    if (!$up['success']) sendError('Upload foto gagal: ' . $up['error'], 400);
    $foto_baru   = $up['filename'];
    $upload_baru = true;
}

try {
    $stmt = mysqli_prepare($conn,
        "UPDATE lapangan SET nama_lapangan=?, harga_per_jam=?, status=?, deskripsi=?, lokasi=?, foto=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "sissssi", $nama, $harga, $status, $deskripsi, $lokasi, $foto_baru, $id);
    mysqli_stmt_execute($stmt);
} catch (Throwable $e) {
    if ($upload_baru) deleteFile(ABS_UPLOAD_LAPANGAN . $foto_baru);
    throw $e;
}

if ($upload_baru && !empty($foto_lama)) {
    deleteFile(ABS_UPLOAD_LAPANGAN . basename($foto_lama));
}

sendResponse(['id' => $id, 'foto' => $foto_baru, 'foto_url' => fotoLapanganUrl($foto_baru)],
    'Lapangan berhasil diperbarui');
?>
