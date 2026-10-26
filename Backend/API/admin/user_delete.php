<?php
require_once '../config.php';
requireMethod('POST');

if (!isset($_SESSION['admin_id'])) sendError('Akses ditolak', 403);

$input = getJsonInput();
$id = (int)($input['id'] ?? 0);

if ($id <= 0) sendError('ID tidak valid', 400);

// Cegah admin hapus dirinya sendiri
if ($id === (int)$_SESSION['admin_id']) {
    sendError('Tidak bisa menghapus akun sendiri', 400);
}

$stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ? AND role = 'user'");
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) sendResponse(null, 'User berhasil dihapus');
else sendError('Gagal hapus user', 500);
?>