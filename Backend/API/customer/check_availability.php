<?php
// ============================================================
// customer/check_availability.php - Cek Ketersediaan Jadwal
// GET /API/customer/check_availability.php
// Params: id_lapangan, tanggal (YYYY-MM-DD), jam_mulai (HH:MM), durasi (1-5)
// ============================================================

require_once '../config.php';

requireMethod('GET');

$id_lapangan = (int)($_GET['id_lapangan'] ?? 0);
$tanggal     = trim($_GET['tanggal'] ?? '');
$jam_mulai   = trim($_GET['jam_mulai'] ?? '');
$durasi      = (int)($_GET['durasi'] ?? 1);

if ($id_lapangan <= 0) sendError('ID lapangan tidak valid', 400);
if (!validateDate($tanggal)) sendError('Format tanggal tidak valid (YYYY-MM-DD)', 400);
if ($jam_mulai === '') sendError('Jam mulai wajib diisi', 400);
$start_min = timeToMinutes($jam_mulai);
if ($start_min === null) sendError('Format jam tidak valid (HH:MM)', 400);
if ($durasi < 1 || $durasi > 5) sendError('Durasi harus 1-5 jam', 400);

$end_min = $start_min + $durasi * 60;
if ($end_min > 24 * 60) sendError('Durasi melewati tengah malam', 400);

$mulai   = minutesToTime($start_min);
$selesai = minutesToTime($end_min);

$bentrok  = findBookingConflicts($conn, $id_lapangan, $tanggal, $mulai, $selesai);
$tersedia = count($bentrok) === 0;

sendResponse([
    'tersedia'      => $tersedia,
    'jam_mulai'     => substr($mulai, 0, 5),
    'jam_selesai'   => substr($selesai, 0, 5),
    'durasi'        => $durasi,
    'tanggal'       => $tanggal,
    'bentrok'       => $bentrok,
    'total_bentrok' => count($bentrok),
], $tersedia ? 'Jadwal tersedia' : 'Jadwal bentrok dengan booking lain');
?>
