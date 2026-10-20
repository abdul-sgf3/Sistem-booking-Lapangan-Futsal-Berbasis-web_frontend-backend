<?php
require_once '../config.php';
requireMethod('POST');

if (!isset($_SESSION['admin_id'])) sendError('Akses ditolak', 403);

$id = (int)($_POST['id'] ?? 0);
$nama = trim($_POST['nama_lapangan'] ?? '');
$harga = (int)($_POST['harga_per_jam'] ?? 0);
$status = $_POST['status'] ?? 'aktif';

if ($id <= 0 || empty($nama)) sendError('Data tidak valid', 400);

$stmt = mysqli_prepare($conn, "UPDATE lapangan SET nama_lapangan = ?, harga_per_jam = ?, status = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, "sisi", $nama, $harga, $status, $id);

if (mysqli_stmt_execute($stmt)) sendResponse(null, 'Lapangan diperbarui');
else sendError('Gagal update', 500);
?>