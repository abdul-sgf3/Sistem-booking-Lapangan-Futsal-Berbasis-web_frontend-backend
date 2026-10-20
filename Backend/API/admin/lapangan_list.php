<?php
require_once '../config.php';
requireMethod('GET');

if (!isset($_SESSION['admin_id'])) sendError('Akses ditolak', 403);

$result = mysqli_query($conn, "SELECT * FROM lapangan ORDER BY id DESC");
$lapangan = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['harga_per_jam'] = (int)$row['harga_per_jam'];
    $row['foto_url'] = fotoLapanganUrl($row['foto']);
    $lapangan[] = $row;
}
sendResponse($lapangan, 'Daftar lapangan admin');
?>