<?php
// ============================================================
// customer/booking_cancel.php - Batalkan Booking
// POST /api/customer/booking_cancel.php
// Body: { booking_id } atau { id }
// ============================================================

require_once '../config.php';

requireMethod('POST');

if (!isset($_SESSION['user_id'])) {
    sendError('Anda belum login', 401);
}

$id_user = $_SESSION['user_id'];
$input = getJsonInput();

// Support 2 nama parameter
$booking_id = (int)($input['booking_id'] ?? $input['id'] ?? 0);

if ($booking_id <= 0) {
    sendError('ID booking tidak valid', 400);
}

// Cek booking milik user
$stmt = mysqli_prepare($conn, 
    "SELECT id, status, tanggal, jam_mulai FROM booking 
     WHERE id = ? AND id_user = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "ii", $booking_id, $id_user);
mysqli_stmt_execute($stmt);
$booking = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$booking) {
    sendError('Booking tidak ditemukan', 404);
}

if ($booking['status'] === 'canceled') {
    sendError('Booking sudah dibatalkan sebelumnya', 400);
}

if ($booking['status'] === 'completed') {
    sendError('Booking yang sudah selesai tidak bisa dibatalkan', 400);
}

if ($booking['status'] === 'confirmed') {
    // Booking yang sudah dikonfirmasi admin - hanya bisa dibatalkan 
    // jika belum lewat tanggal bermain
    $tgl_main = strtotime($booking['tanggal'] . ' ' . $booking['jam_mulai']);
    if ($tgl_main < time()) {
        sendError('Booking sudah lewat, tidak bisa dibatalkan', 400);
    }
}

// Update status
$stmt = mysqli_prepare($conn, 
    "UPDATE booking SET status = 'canceled', canceled_at = NOW() WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $booking_id);

if (!mysqli_stmt_execute($stmt)) {
    sendError('Gagal membatalkan booking: ' . mysqli_error($conn), 500);
}

sendResponse([
    'booking_id' => $booking_id,
    'status' => 'canceled'
], 'Booking berhasil dibatalkan');
?>