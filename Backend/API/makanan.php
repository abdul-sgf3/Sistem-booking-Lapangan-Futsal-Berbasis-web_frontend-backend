<?php
// ============================================
// Backend/API/makanan.php
// GET /api/makanan?kategori=makanan|minuman|semua
// ============================================
require_once 'config.php';

requireMethod('GET');

$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : 'semua';

if (!in_array($kategori, ['semua', 'makanan', 'minuman'])) {
    sendError('Kategori tidak valid. Gunakan: semua, makanan, atau minuman', 400);
}

if ($kategori === 'semua') {
    $result = mysqli_query($conn, "SELECT * FROM makanan_minuman ORDER BY kategori ASC, nama ASC");
} else {
    $stmt = mysqli_prepare($conn, "SELECT * FROM makanan_minuman WHERE kategori = ? ORDER BY nama ASC");
    mysqli_stmt_bind_param($stmt, "s", $kategori);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}

if (!$result) {
    sendError('Query gagal: ' . mysqli_error($conn), 500);
}

$makanan = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['id'] = (int)$row['id'];
    $row['harga'] = (int)$row['harga'];
    $row['foto_url'] = fotoMakananUrl($row['foto']);
    $makanan[] = $row;
}

sendResponse($makanan, 'Daftar makanan/minuman berhasil diambil');
?>