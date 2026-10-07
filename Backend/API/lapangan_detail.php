<?php
// api/lapangan_detail.php?id=1
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    sendError('ID lapangan tidak valid', 400);
}

$stmt = mysqli_prepare($conn, "SELECT * FROM lapangan WHERE id = ? AND status = 'aktif'");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$lapangan = mysqli_fetch_assoc($result);

if (!$lapangan) {
    sendError('Lapangan tidak ditemukan', 404);
}

if (!empty($lapangan['foto'])) {
    $lapangan['foto_url'] = '/Booking-Futsal-main/tsubasa_arena/Frontend/assets/uploads/lapangan/' . $lapangan['foto'];
}

sendResponse([
    'status' => 'success',
    'data'   => $lapangan
]);
?>