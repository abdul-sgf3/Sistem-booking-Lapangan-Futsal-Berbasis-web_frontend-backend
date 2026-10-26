<?php
require_once '../config.php';
requireMethod('POST');

if (!isset($_SESSION['admin_id'])) sendError('Akses ditolak', 403);

$input = getJsonInput();
$id = (int)($input['id'] ?? 0);
$status = $input['status'] ?? '';

if ($id <= 0 || !in_array($status, ['pending', 'confirmed', 'completed', 'canceled'])) {
    sendError('Data tidak valid', 400);
}

// Ambil data booking
$stmt = mysqli_prepare($conn, 
    "SELECT id, id_lapangan, tanggal, jam_mulai, durasi FROM booking WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$booking = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$booking) sendError('Booking tidak ditemukan', 404);

// Mulai transaksi
mysqli_begin_transaction($conn);

try {
    // Update status booking
    $stmt = mysqli_prepare($conn, "UPDATE booking SET status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $status, $id);
    mysqli_stmt_execute($stmt);

    // Efek ke tabel jadwal
    if ($status === 'confirmed') {
        // Hapus jadwal lama untuk booking ini
        $stmt = mysqli_prepare($conn, "DELETE FROM jadwal WHERE booking_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        // Insert slot-slot jadwal
        for ($i = 0; $i < (int)$booking['durasi']; $i++) {
            $jam = date('H:i:s', strtotime($booking['jam_mulai']) + ($i * 3600));
            $stmt = mysqli_prepare($conn, 
                "INSERT INTO jadwal (id_lapangan, tanggal, jam, status, booking_id) 
                 VALUES (?, ?, ?, 'booked', ?)");
            mysqli_stmt_bind_param($stmt, "isss", 
                $booking['id_lapangan'], $booking['tanggal'], $jam, $id);
            mysqli_stmt_execute($stmt);
        }
    } elseif ($status === 'canceled') {
        // Hapus jadwal
        $stmt = mysqli_prepare($conn, "DELETE FROM jadwal WHERE booking_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
    }

    mysqli_commit($conn);
    sendResponse(['id' => $id, 'status' => $status], 'Status berhasil diubah');
} catch (Exception $e) {
    mysqli_rollback($conn);
    sendError('Gagal mengubah status: ' . $e->getMessage(), 500);
}
?>