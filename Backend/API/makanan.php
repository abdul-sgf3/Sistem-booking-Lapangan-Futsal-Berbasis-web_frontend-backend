<?php
// api/makanan.php - GET semua makanan/minuman
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Method tidak diizinkan', 405);
}

$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : 'semua';

if ($kategori !== 'semua') {
    $stmt = mysqli_prepare($conn, "SELECT * FROM makanan_minuman WHERE kategori = ? ORDER BY nama ASC");
    mysqli_stmt_bind_param($stmt, "s", $kategori);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, "SELECT * FROM makanan_minuman ORDER BY kategori DESC, nama ASC");
}

if (!$result) {
    sendError('Query gagal: ' . mysqli_error($conn), 500);
}

$makanan = [];
while ($row = mysqli_fetch_assoc($result)) {
    if (!empty($row['foto'])) {
        $row['foto_url'] = '/Booking-Futsal-main/tsubasa_arena/Frontend/assets/uploads/makanan/' . $row['foto'];
    } else {
        $row['foto_url'] = '';
    }
    $makanan[] = $row;
}

sendResponse([
    'status' => 'success',
    'total'  => count($makanan),
    'data'   => $makanan
]);
?>