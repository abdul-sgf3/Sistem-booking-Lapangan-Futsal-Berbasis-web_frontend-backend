<?php
require_once '../config.php';
requireMethod('GET');

if (!isset($_SESSION['admin_id'])) {
    sendError('Akses ditolak', 403);
}

// 1. Total Pendapatan
$q_pendapatan = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM booking WHERE status IN ('confirmed', 'completed')");
$pendapatan = mysqli_fetch_assoc($q_pendapatan)['total'] ?? 0;

// 2. Total Booking
$q_booking = mysqli_query($conn, "SELECT COUNT(id) as total FROM booking");
$total_booking = mysqli_fetch_assoc($q_booking)['total'];

// 3. Total User
$q_user = mysqli_query($conn, "SELECT COUNT(id) as total FROM users WHERE role = 'user'");
$total_user = mysqli_fetch_assoc($q_user)['total'];

// 4. Total Lapangan
$q_lapangan = mysqli_query($conn, "SELECT COUNT(id) as total FROM lapangan");
$total_lapangan = mysqli_fetch_assoc($q_lapangan)['total'];

sendResponse([
    'pendapatan' => (int)$pendapatan,
    'total_booking' => (int)$total_booking,
    'total_user' => (int)$total_user,
    'total_lapangan' => (int)$total_lapangan
], 'Statistik dashboard berhasil diambil');
?>