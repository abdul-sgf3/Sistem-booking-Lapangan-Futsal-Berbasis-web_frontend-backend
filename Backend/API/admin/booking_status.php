<?php
// ============================================================
// admin/booking_status.php - Ubah status booking
// POST /API/admin/booking_status.php   Body: { id, status }
// POST /API/admin/bookings/{id}/status Body: { status }
// status: pending | confirmed | completed | canceled
// ============================================================

require_once '../config.php';

requireMethod('POST');
requireAdminLogin();

$input  = getJsonInput();
$id     = getRequestId($input, ['id', 'booking_id']);
$status = $input['status'] ?? '';

if ($id <= 0 || !in_array($status, ['pending', 'confirmed', 'completed', 'canceled'], true)) {
    sendError('Data tidak valid', 400);
}

$stmt = mysqli_prepare($conn,
    "SELECT id, id_lapangan, tanggal, jam_mulai, durasi FROM booking WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$booking = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$booking) sendError('Booking tidak ditemukan', 404);

mysqli_begin_transaction($conn);
try {
    $stmt = mysqli_prepare($conn,
        "UPDATE booking SET status = ?, canceled_at = IF(? = 'canceled', NOW(), canceled_at) WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $status, $status, $id);
    mysqli_stmt_execute($stmt);

    if ($status === 'confirmed') {
        // Hapus jadwal lama booking ini, lalu isi slot per jam
        $stmt = mysqli_prepare($conn, "DELETE FROM jadwal WHERE booking_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $start_min = timeToMinutes($booking['jam_mulai']);
        for ($i = 0; $i < (int)$booking['durasi']; $i++) {
            $jam = minutesToTime($start_min + $i * 60);
            $stmt = mysqli_prepare($conn,
                "INSERT INTO jadwal (id_lapangan, tanggal, jam, status, booking_id)
                 VALUES (?, ?, ?, 'booked', ?)
                 ON DUPLICATE KEY UPDATE status = 'booked', booking_id = VALUES(booking_id)");
            mysqli_stmt_bind_param($stmt, "issi", $booking['id_lapangan'], $booking['tanggal'], $jam, $id);
            mysqli_stmt_execute($stmt);
        }
    } elseif ($status === 'canceled') {
        $stmt = mysqli_prepare($conn, "DELETE FROM jadwal WHERE booking_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
    }

    mysqli_commit($conn);
} catch (Throwable $e) {
    mysqli_rollback($conn);
    throw $e;
}

sendResponse(['id' => $id, 'status' => $status], 'Status berhasil diubah');
?>
