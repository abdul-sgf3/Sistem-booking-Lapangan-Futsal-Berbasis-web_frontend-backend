<?php
require_once '../config.php';
requireMethod('POST');

if (!isset($_SESSION['admin_id'])) sendError('Akses ditolak', 403);

$input = getJsonInput();
$id = (int)($input['id'] ?? 0);
$is_active = (int)($input['is_active'] ?? 1);

$stmt = mysqli_prepare($conn, "UPDATE users SET is_active = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, "ii", $is_active, $id);

if (mysqli_stmt_execute($stmt)) sendResponse(null, 'Status user diperbarui');
else sendError('Gagal update', 500);
?>