<?php
// ============================================
// Backend/API/jadwal.php
// GET /api/jadwal?id_lapangan=X&tanggal=YYYY-MM-DD
// ============================================
require_once 'config.php';

requireMethod('GET');

$id_lapangan = isset($_GET['id_lapangan']) ? (int)$_GET['id_lapangan'] : 0;
$tanggal     = isset($_GET['tanggal']) ? trim($_GET['tanggal']) : '';

if ($id_lapangan <= 0) {
    sendError('Parameter id_lapangan wajib diisi dan harus integer positif', 400);
}

if (empty($tanggal) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
    sendError('Parameter tanggal wajib diisi dengan format YYYY-MM-DD', 400);
}

$stmt = mysqli_prepare($conn,
    "SELECT id, id_lapangan, tanggal, jam, status, booking_id 
     FROM jadwal 
     WHERE id_lapangan = ? AND tanggal = ?
     ORDER BY jam ASC"
);
mysqli_stmt_bind_param($stmt, "is", $id_lapangan, $tanggal);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$jadwal = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['id'] = (int)$row['id'];
    $row['id_lapangan'] = (int)$row['id_lapangan'];
    $row['booking_id'] = $row['booking_id'] !== null ? (int)$row['booking_id'] : null;
    $jadwal[] = $row;
}

sendResponse($jadwal, 'Jadwal berhasil diambil');
?>