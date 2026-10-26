<?php
require_once '../config.php';
requireMethod('GET');

if (!isset($_SESSION['admin_id'])) sendError('Akses ditolak', 403);

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) sendError('ID booking tidak valid', 400);

$stmt = mysqli_prepare($conn,
    "SELECT b.*, u.nama AS user_nama, u.email AS user_email, u.whatsapp AS user_wa,
            l.nama_lapangan, l.harga_per_jam, l.lokasi, l.foto AS foto_lapangan
     FROM booking b
     JOIN users u ON b.id_user = u.id
     JOIN lapangan l ON b.id_lapangan = l.id
     WHERE b.id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$booking = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$booking) sendError('Booking tidak ditemukan', 404);

// Format
$booking['total_harga_formatted'] = 'Rp ' . number_format($booking['total_harga'], 0, ',', '.');
$booking['tanggal_formatted'] = date('d/m/Y', strtotime($booking['tanggal']));
$booking['bukti_url'] = buktiUrl($booking['bukti_pembayaran'] ?? '');
$booking['foto_lapangan_url'] = fotoLapanganUrl($booking['foto_lapangan']);

sendResponse($booking, 'Detail booking berhasil diambil');
?>