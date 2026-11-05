<?php
// ============================================================
// admin/booking_list.php - Daftar semua booking (admin)
// GET /API/admin/booking_list.php?status=all|pending|confirmed|completed|canceled
// GET /API/admin/bookings         (route .htaccess)
// ============================================================

require_once '../config.php';

requireMethod('GET');
requireAdminLogin();

$status = $_GET['status'] ?? 'all';
if ($status !== 'all' && !in_array($status, ['pending', 'confirmed', 'completed', 'canceled'], true)) {
    sendError('Filter status tidak valid', 400);
}

$sql = "SELECT b.*, u.nama AS user_nama, u.email AS user_email, u.whatsapp AS user_wa, l.nama_lapangan
          FROM booking b
          JOIN users u ON b.id_user = u.id
          JOIN lapangan l ON b.id_lapangan = l.id";

if ($status !== 'all') {
    $stmt = mysqli_prepare($conn, $sql . " WHERE b.status = ? ORDER BY b.created_at DESC, b.id DESC");
    mysqli_stmt_bind_param($stmt, "s", $status);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, $sql . " ORDER BY b.created_at DESC, b.id DESC");
}

$bookings = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['id']                    = (int)$row['id'];
    $row['total_harga']           = (int)$row['total_harga'];
    $row['durasi']                = (int)$row['durasi'];
    $row['total_harga_formatted'] = 'Rp ' . number_format($row['total_harga'], 0, ',', '.');
    $row['tanggal_formatted']     = date('d/m/Y', strtotime($row['tanggal']));
    $row['bukti_url']             = buktiUrl($row['bukti_pembayaran'] ?? '');
    $bookings[] = $row;
}

sendResponse($bookings, 'Daftar booking admin berhasil diambil');
?>
