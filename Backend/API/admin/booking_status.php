<?php
require_once '../config.php';
requireMethod('POST');

if (!isset($_SESSION['admin_id'])) sendError('Akses ditolak', 403);

$input = getJsonInput();
$id = (int)($input['id'] ?? 0);
$status = $input['status'] ?? '';

if ($id <= 0 || !in_array($status, ['pending', 'confirmed', 'completed', 'canceled'])) {
    sendError('Data tidak valid', 400);
}

$stmt = mysqli_prepare($conn, "UPDATE booking SET status = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, "si", $status, $id);

if (mysqli_stmt_execute($stmt)) sendResponse(['status' => $status], 'Status berhasil diubah');
else sendError('Gagal mengubah status', 500);
?>