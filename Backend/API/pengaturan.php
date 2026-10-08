<?php
// ============================================
// Backend/API/pengaturan.php
// GET /api/pengaturan - Pengaturan booking
// ============================================
require_once 'config.php';

requireMethod('GET');

$result = mysqli_query($conn, "SELECT * FROM pengaturan LIMIT 1");

if (!$result) {
    sendError('Query gagal: ' . mysqli_error($conn), 500);
}

$pengaturan = mysqli_fetch_assoc($result);

if (!$pengaturan) {
    sendError('Pengaturan belum diatur', 404);
}

$pengaturan['id'] = (int)$pengaturan['id'];
$pengaturan['maksimal_durasi'] = (int)$pengaturan['maksimal_durasi'];
$pengaturan['biaya_booking'] = (int)$pengaturan['biaya_booking'];

sendResponse($pengaturan, 'Pengaturan berhasil diambil');
?>