<?php
// ============================================================
// customer/upload_bukti.php - Upload Bukti Pembayaran
// POST /API/customer/upload_bukti.php   (multipart/form-data: booking_id, bukti)
// POST /API/bookings/{id}/payment-proof (id dari URL)
// ============================================================

require_once '../config.php';

requireMethod('POST');

if (!isset($_SESSION['user_id'])) {
    sendError('Anda belum login', 401);
}
$id_user = (int)$_SESSION['user_id'];

$booking_id = getRequestId($_POST, ['booking_id', 'id']);
if ($booking_id <= 0) {
    sendError('ID booking tidak valid', 400);
}

$stmt = mysqli_prepare($conn,
    "SELECT id, status, bukti_pembayaran FROM booking WHERE id = ? AND id_user = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "ii", $booking_id, $id_user);
mysqli_stmt_execute($stmt);
$booking = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$booking) sendError('Booking tidak ditemukan', 404);
if (in_array($booking['status'], ['canceled', 'completed'], true)) {
    sendError('Booking yang sudah selesai/dibatalkan tidak bisa upload bukti', 400);
}

if (!isset($_FILES['bukti']) || $_FILES['bukti']['error'] === UPLOAD_ERR_NO_FILE) {
    sendError('File bukti wajib diupload', 400);
}

$up = handleDocumentUpload($_FILES['bukti'], ABS_UPLOAD_BUKTI, 'bukti_' . $booking_id);
if (!$up['success']) {
    sendError($up['error'], 400);
}
$filename = $up['filename'];

try {
    $stmt = mysqli_prepare($conn, "UPDATE booking SET bukti_pembayaran = ?, uploaded_at = NOW() WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $filename, $booking_id);
    mysqli_stmt_execute($stmt);
} catch (Throwable $e) {
    deleteFile(ABS_UPLOAD_BUKTI . $filename);
    throw $e;
}

// Hapus bukti lama setelah yang baru tersimpan
if (!empty($booking['bukti_pembayaran'])) {
    deleteFile(ABS_UPLOAD_BUKTI . basename($booking['bukti_pembayaran']));
}

sendResponse([
    'booking_id' => $booking_id,
    'filename'   => $filename,
    'url'        => buktiUrl($filename),
], 'Bukti pembayaran berhasil diupload');
?>
