<?php
require_once '../config.php';
requireMethod('GET');

if (!isset($_SESSION['admin_id'])) {
    sendError('Akses ditolak', 403);
}

// Optional filter status
$status = $_GET['status'] ?? 'all';
$sql = "SELECT b.*, u.nama AS user_nama, l.nama_lapangan 
        FROM booking b 
        JOIN users u ON b.id_user = u.id 
        JOIN lapangan l ON b.id_lapangan = l.id";

if ($status !== 'all') {
    $sql .= " WHERE b.status = '" . mysqli_real_escape_string($conn, $status) . "'";
}
$sql .= " ORDER BY b.created_at DESC";

$result = mysqli_query($conn, $sql);
$bookings = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['total_harga_formatted'] = 'Rp ' . number_format($row['total_harga'], 0, ',', '.');
    $row['tanggal_formatted'] = date('d/m/Y', strtotime($row['tanggal']));
    $bookings[] = $row;
}
sendResponse($bookings, 'Daftar booking admin berhasil diambil');
?>