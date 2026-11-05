<?php
// ============================================================
// customer/booking_cancel.php - Batalkan Booking
// POST /API/customer/booking_cancel.php   Body: { booking_id } atau { id }
// POST /API/bookings/{id}/cancel          (id dari URL)
// ============================================================

require_once '../config.php';

requireMethod('POST');

if (!isset($_SESSION['user_id'])) {
    sendError('Anda belum login', 401);
}
$id_user = (int)$_SESSION['user_id'];

$input      = getJsonInput();
$booking_id = getRequestId($input, ['booking_id', 'id']);
if ($booking_id <= 0) {
    sendError('ID booking tidak valid', 400);
}

$stmt = mysqli_prepare($conn,
    "SELECT id, status, tanggal, jam_mulai FROM booking WHERE id = ? AND id_user = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "ii", $booking_id, $id_user);
mysqli_stmt_execute($stmt);
$booking = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$booking) sendError('Booking tidak ditemukan', 404);
if ($booking['status'] === 'canceled')  sendError('Booking sudah dibatalkan sebelumnya', 400);
if ($booking['status'] === 'completed') sendError('Booking yang sudah selesai tidak bisa dibatalkan', 400);

if ($booking['status'] === 'confirmed') {
    // Sudah dikonfirmasi admin: hanya bisa dibatalkan sebelum jam main
    if (strtotime($booking['tanggal'] . ' ' . $booking['jam_mulai']) < time()) {
        sendError('Booking sudah lewat, tidak bisa dibatalkan', 400);
    }
}

mysqli_begin_transaction($conn);
try {
    $stmt = mysqli_prepare($conn, "UPDATE booking SET status = 'canceled', canceled_at = NOW() WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $booking_id);
    mysqli_stmt_execute($stmt);

    // Bebaskan slot jadwal
    $stmt = mysqli_prepare($conn, "DELETE FROM jadwal WHERE booking_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $booking_id);
    mysqli_stmt_execute($stmt);

    mysqli_commit($conn);
} catch (Throwable $e) {
    mysqli_rollback($conn);
    throw $e;
}

sendResponse(['booking_id' => $booking_id, 'status' => 'canceled'], 'Booking berhasil dibatalkan');
?>
