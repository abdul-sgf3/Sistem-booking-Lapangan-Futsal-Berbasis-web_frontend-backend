<?php
// ============================================================
// admin/user_delete.php - Hapus user
// POST /API/admin/user_delete.php   Body: { id }
// POST /API/admin/users/{id}        (id dari URL)
//
// User yang sudah punya riwayat booking TIDAK dihapus (FK CASCADE akan ikut
// menghapus booking & merusak laporan pendapatan) — gunakan user_status untuk menonaktifkan.
// ============================================================

require_once '../config.php';

requireMethod('POST');
$admin_id = requireAdminLogin();

$input = getJsonInput();
$id    = getRequestId($input, ['id']);

if ($id <= 0) sendError('ID tidak valid', 400);
if ($id === $admin_id) sendError('Tidak bisa menghapus akun sendiri', 400);

$stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM booking WHERE id_user = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$total_booking = (int)mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];
if ($total_booking > 0) {
    sendError("User memiliki $total_booking riwayat booking. Nonaktifkan saja akunnya, jangan dihapus.", 409);
}

$stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ? AND role = 'user'");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) === 0) sendError('User tidak ditemukan', 404);

sendResponse(null, 'User berhasil dihapus');
?>
