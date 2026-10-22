<?php
require_once '../config.php';
requireMethod('POST');

if (!isset($_SESSION['admin_id'])) sendError('Akses ditolak', 403);

$input = getJsonInput();
$id = (int)($input['id'] ?? 0);

if ($id <= 0) sendError('ID tidak valid', 400);

$stmt = mysqli_prepare($conn, "DELETE FROM lapangan WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) sendResponse(null, 'Lapangan dihapus');
else sendError('Gagal hapus', 500);
?>