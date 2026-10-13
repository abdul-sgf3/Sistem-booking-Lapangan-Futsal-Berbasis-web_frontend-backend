<?php
// POST /api/customer/booking_create
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method tidak diizinkan', 405);
}
if (!isset($_SESSION['user_id'])) {
    sendError('Anda belum login', 401);
}

$input = getJsonInput();
$id_user = $_SESSION['user_id'];
$id_lapangan = (int)($input['id_lapangan'] ?? 0);
$tanggal = $input['tanggal'] ?? '';
$jam_mulai = $input['jam_mulai'] ?? '';
$durasi = (int)($input['durasi'] ?? 1);
$catatan = $input['catatan'] ?? '';

if ($id_lapangan <= 0 || empty($tanggal) || empty($jam_mulai)) {
    sendError('Data booking tidak lengkap', 400);
}
if ($durasi < 1 || $durasi > 5) {
    sendError('Durasi 1-5 jam', 400);
}

// Ambil harga
$stmt = mysqli_prepare($conn, "SELECT harga_per_jam FROM lapangan WHERE id=? AND status='aktif'");
mysqli_stmt_bind_param($stmt, "i", $id_lapangan);
mysqli_stmt_execute($stmt);
$lap = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$lap) sendError('Lapangan tidak ditemukan', 404);

$total_harga = (int)$lap['harga_per_jam'] * $durasi;

// Cek bentrok
$jam_selesai = date('H:i:s', strtotime($jam_mulai) + ($durasi * 3600));
$stmt = mysqli_prepare($conn,
    "SELECT id FROM booking 
     WHERE id_lapangan=? AND tanggal=? AND status NOT IN ('canceled','completed')
     AND (jam_mulai < ? AND ADDTIME(jam_mulai, SEC_TO_TIME(durasi*3600)) > ?)");
mysqli_stmt_bind_param($stmt, "isss", $id_lapangan, $tanggal, $jam_selesai, $jam_mulai);
mysqli_stmt_execute($stmt);
if (mysqli_stmt_get_result($stmt)->num_rows > 0) {
    sendError('Jadwal sudah dibooking', 409);
}

$kode = 'BSK' . date('Ymd') . str_pad(random_int(1,9999), 4, '0', STR_PAD_LEFT);

$stmt = mysqli_prepare($conn,
    "INSERT INTO booking (id_user, id_lapangan, kode_booking, tanggal, jam_mulai, durasi, total_harga, catatan, status) 
     VALUES (?,?,?,?,?,?,?,?,'pending')");
mysqli_stmt_bind_param($stmt, "iissssis", 
    $id_user, $id_lapangan, $kode, $tanggal, $jam_mulai, $durasi, $total_harga, $catatan);
mysqli_stmt_execute($stmt);

sendResponse([
    'kode_booking' => $kode,
    'total_harga' => $total_harga
], 'Booking berhasil');
?>