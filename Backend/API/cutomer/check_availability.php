<?php
// ============================================================
// customer/check_availability.php - Cek Ketersediaan Jadwal
// GET /api/customer/check_availability.php
// Params: id_lapangan, tanggal, jam_mulai, durasi
// ============================================================

require_once '../config.php';

requireMethod('GET');

$id_lapangan = (int)($_GET['id_lapangan'] ?? 0);
$tanggal     = trim($_GET['tanggal'] ?? '');
$jam_mulai   = trim($_GET['jam_mulai'] ?? '');
$durasi      = (int)($_GET['durasi'] ?? 1);

// Validasi
if ($id_lapangan <= 0) sendError('ID lapangan tidak valid', 400);
if (empty($tanggal) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
    sendError('Format tanggal tidak valid', 400);
}
if (empty($jam_mulai)) sendError('Jam mulai wajib diisi', 400);
if ($durasi < 1 || $durasi > 5) sendError('Durasi harus 1-5 jam', 400);

// Hitung jam selesai
$jam_selesai = date('H:i:s', strtotime($jam_mulai) + ($durasi * 3600));
$jam_mulai_db = date('H:i:s', strtotime($jam_mulai));

// Cek bentrok
$stmt = mysqli_prepare($conn,
    "SELECT id, kode_booking, jam_mulai, durasi 
     FROM booking 
     WHERE id_lapangan = ? 
       AND tanggal = ? 
       AND status NOT IN ('canceled', 'completed')
       AND (jam_mulai < ? AND ADDTIME(jam_mulai, SEC_TO_TIME(durasi * 3600)) > ?)
     ORDER BY jam_mulai ASC");
mysqli_stmt_bind_param($stmt, "isss", $id_lapangan, $tanggal, $jam_selesai, $jam_mulai_db);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$bentrok = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['id'] = (int)$row['id'];
    $row['durasi'] = (int)$row['durasi'];
    $row['jam_mulai_formatted'] = substr($row['jam_mulai'], 0, 5);
    $row['jam_selesai_formatted'] = date('H:i', strtotime($row['jam_mulai']) + ($row['durasi'] * 3600));
    $bentrok[] = $row;
}

$tersedia = count($bentrok) === 0;

sendResponse([
    'tersedia' => $tersedia,
    'jam_mulai' => substr($jam_mulai, 0, 5),
    'jam_selesai' => substr($jam_selesai, 0, 5),
    'durasi' => $durasi,
    'tanggal' => $tanggal,
    'bentrok' => $bentrok,
    'total_bentrok' => count($bentrok)
], $tersedia ? 'Jadwal tersedia' : 'Jadwal bentrok dengan booking lain');
?>