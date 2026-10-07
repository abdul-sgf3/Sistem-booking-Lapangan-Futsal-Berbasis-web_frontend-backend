<?php
// api/lapangan.php - GET semua lapangan
require_once 'config.php';

// Cek method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Method tidak diizinkan', 405);
}

// Query
$query = "SELECT * FROM lapangan WHERE status = 'aktif' ORDER BY id ASC";
$result = mysqli_query($conn, $query);

if (!$result) {
    sendError('Query gagal: ' . mysqli_error($conn), 500);
}

// Ambil semua data
$lapangan = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Format URL foto
    if (!empty($row['foto'])) {
        $row['foto_url'] = '/Booking-Futsal-main/tsubasa_arena/Frontend/assets/uploads/lapangan/' . $row['foto'];
    } else {
        $row['foto_url'] = '';
    }
    $lapangan[] = $row;
}

// Response
sendResponse([
    'status' => 'success',
    'total'  => count($lapangan),
    'data'   => $lapangan
]);
?>