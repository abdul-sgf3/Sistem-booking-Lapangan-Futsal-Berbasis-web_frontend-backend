<?php
// ============================================
// Backend/API/lapangan.php
// GET /api/lapangan - Daftar lapangan aktif
// ============================================
require_once 'config.php';

requireMethod('GET');

// Pagination (opsional)
$page  = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = isset($_GET['limit']) ? max(1, min(50, (int)$_GET['limit'])) : 50;
$offset = ($page - 1) * $limit;

// Query
$query = "SELECT id, nama_lapangan, deskripsi, lokasi, harga_per_jam, foto, status, created_at 
          FROM lapangan 
          WHERE status = 'aktif' 
          ORDER BY id ASC 
          LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $query);

if (!$result) {
    sendError('Query gagal: ' . mysqli_error($conn), 500);
}

// Ambil data
$lapangan = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['id'] = (int)$row['id'];
    $row['harga_per_jam'] = (int)$row['harga_per_jam'];
    $row['foto_url'] = fotoLapanganUrl($row['foto']);
    $lapangan[] = $row;
}

// Total untuk pagination info
$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM lapangan WHERE status = 'aktif'");
$totalData = (int)mysqli_fetch_assoc($totalQuery)['total'];

sendResponse($lapangan, 'Daftar lapangan berhasil diambil');
?>