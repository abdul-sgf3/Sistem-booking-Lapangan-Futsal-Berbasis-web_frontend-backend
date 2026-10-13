<?php
// ============================================================
// customer/booking_detail.php - Detail Booking
// GET /api/customer/booking_detail.php?id=X
// ============================================================

require_once '../config.php';

requireMethod('GET');

if (!isset($_SESSION['user_id'])) {
    sendError('Anda belum login', 401);
}

$id_user = $_SESSION['user_id'];
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    sendError('ID booking tidak valid', 400);
}

$stmt = mysqli_prepare($conn,
    "SELECT 
        b.*, 
        l.nama_lapangan, l.foto AS foto_lapangan, l.lokasi, l.deskripsi AS deskripsi_lapangan
     FROM booking b 
     JOIN lapangan l ON b.id_lapangan = l.id 
     WHERE b.id = ? AND b.id_user = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "ii", $id, $id_user);
mysqli_stmt_execute($stmt);
$booking = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$booking) {
    sendError('Booking tidak ditemukan', 404);
}

// Format data
$booking['id'] = (int)$booking['id'];
$booking['id_lapangan'] = (int)$booking['id_lapangan'];
$booking['id_user'] = (int)$booking['id_user'];
$booking['durasi'] = (int)$booking['durasi'];
$booking['total_harga'] = (int)$booking['total_harga'];
$booking['total_harga_formatted'] = 'Rp ' . number_format($booking['total_harga'], 0, ',', '.');
$booking['jam_mulai_formatted'] = substr($booking['jam_mulai'], 0, 5) . ' WIB';
$booking['jam_selesai'] = date('H:i', strtotime($booking['jam_mulai']) + ($booking['durasi'] * 3600)) . ' WIB';
$booking['tanggal_formatted'] = date('d F Y', strtotime($booking['tanggal']));
$booking['foto_lapangan_url'] = fotoLapanganUrl($booking['foto_lapangan']);
$booking['bukti_url'] = buktiUrl($booking['bukti_pembayaran'] ?? '');

sendResponse($booking, 'Detail booking berhasil diambil');
?>