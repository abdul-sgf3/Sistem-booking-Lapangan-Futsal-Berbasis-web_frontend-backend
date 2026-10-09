<?php
// ============================================
// Backend/API/lapangan_detail.php
// GET /api/lapangan/{id} - Detail lapangan
// ============================================
require_once 'config.php';

requireMethod('GET');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    sendError('ID lapangan tidak valid', 400);
}

$stmt = mysqli_prepare($conn, 
    "SELECT id, nama_lapangan, deskripsi, lokasi, harga_per_jam, foto, status, created_at 
     FROM lapangan 
     WHERE id = ? AND status = 'aktif'"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$lapangan = mysqli_fetch_assoc($result);

if (!$lapangan) {
    sendError('Lapangan tidak ditemukan', 404);
}

// Format tipe data
$lapangan['id'] = (int)$lapangan['id'];
$lapangan['harga_per_jam'] = (int)$lapangan['harga_per_jam'];
$lapangan['foto_url'] = fotoLapanganUrl($lapangan['foto']);

sendResponse($lapangan, 'Detail lapangan berhasil diambil');
?>