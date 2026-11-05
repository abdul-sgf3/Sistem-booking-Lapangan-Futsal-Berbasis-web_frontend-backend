<?php
// ============================================================
// admin/lapangan_delete.php - Hapus lapangan
// POST /API/admin/lapangan_delete.php  Body: { id }
// POST /API/admin/lapangan/{id}/delete (id dari URL)
//
// PENTING: tabel booking memakai ON DELETE CASCADE, sehingga menghapus lapangan
// akan MENGHAPUS SELURUH riwayat booking-nya. Karena itu lapangan yang sudah
// punya riwayat booking hanya di-NONAKTIFKAN, tidak dihapus.
// ============================================================

require_once '../config.php';

requireMethod('POST');
requireAdminLogin();

$input = getJsonInput();
$id    = getRequestId($input, ['id']);
if ($id <= 0) sendError('ID tidak valid', 400);

$stmt = mysqli_prepare($conn, "SELECT id FROM lapangan WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
if (!mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))) sendError('Lapangan tidak ditemukan', 404);

$stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM booking WHERE id_lapangan = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$total_booking = (int)mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

if ($total_booking > 0) {
    $stmt = mysqli_prepare($conn, "UPDATE lapangan SET status = 'nonaktif' WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    sendResponse(['id' => $id, 'deleted' => false, 'status' => 'nonaktif', 'total_booking' => $total_booking],
        "Lapangan memiliki $total_booking riwayat booking sehingga dinonaktifkan (tidak dihapus permanen).");
}

$stmt = mysqli_prepare($conn, "DELETE FROM lapangan WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

sendResponse(['id' => $id, 'deleted' => true], 'Lapangan dihapus');
?>
