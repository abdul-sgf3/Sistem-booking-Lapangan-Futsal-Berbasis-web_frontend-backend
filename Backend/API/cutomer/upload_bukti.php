<?php
// ============================================================
// customer/upload_bukti.php - Upload Bukti Pembayaran
// POST /api/customer/upload_bukti.php (multipart/form-data)
// Form: booking_id, bukti (file)
// ============================================================

require_once '../config.php';

requireMethod('POST');

if (!isset($_SESSION['user_id'])) {
    sendError('Anda belum login', 401);
}

$id_user = $_SESSION['user_id'];
$booking_id = (int)($_POST['booking_id'] ?? $_POST['id'] ?? 0);

if ($booking_id <= 0) {
    sendError('ID booking tidak valid', 400);
}

// Cek booking milik user
$stmt = mysqli_prepare($conn, 
    "SELECT id, status FROM booking WHERE id = ? AND id_user = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "ii", $booking_id, $id_user);
mysqli_stmt_execute($stmt);
$booking = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$booking) {
    sendError('Booking tidak ditemukan', 404);
}

if (in_array($booking['status'], ['canceled', 'completed'])) {
    sendError('Booking yang sudah selesai/dibatalkan tidak bisa upload bukti', 400);
}

// Cek file
if (!isset($_FILES['bukti']) || $_FILES['bukti']['error'] !== UPLOAD_ERR_OK) {
    sendError('File bukti wajib diupload', 400);
}

$allowed = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
$max_size = 5 * 1024 * 1024; // 5MB

if (!in_array($_FILES['bukti']['type'], $allowed)) {
    sendError('Format file harus JPG, PNG, atau PDF', 400);
}
if ($_FILES['bukti']['size'] > $max_size) {
    sendError('Ukuran file maksimal 5MB', 400);
}

// Upload
$upload_dir = __DIR__ . '/../../Frontend/assets/uploads/bukti/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$ext = strtolower(pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION));
$filename = 'bukti_' . $booking_id . '_' . time() . '.' . $ext;

if (!move_uploaded_file($_FILES['bukti']['tmp_name'], $upload_dir . $filename)) {
    sendError('Gagal upload file', 500);
}

// Update database
$stmt = mysqli_prepare($conn, 
    "UPDATE booking SET bukti_pembayaran = ?, uploaded_at = NOW() WHERE id = ?");
mysqli_stmt_bind_param($stmt, "si", $filename, $booking_id);
mysqli_stmt_execute($stmt);

sendResponse([
    'booking_id' => $booking_id,
    'filename' => $filename,
    'url' => buktiUrl($filename)
], 'Bukti pembayaran berhasil diupload');
?>