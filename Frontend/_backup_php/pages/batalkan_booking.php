<?php
// batalkan_booking.php - Batalkan Booking User
session_start();
include 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Cek booking milik user ini
$cek = mysqli_query($conn, "SELECT id FROM booking WHERE id = $id AND id_user = {$_SESSION['user_id']}");
if (mysqli_num_rows($cek) > 0) {
    mysqli_query($conn, "UPDATE booking SET status = 'canceled' WHERE id = $id");
    mysqli_query($conn, "DELETE FROM jadwal WHERE booking_id = $id");
}

header("Location: riwayat.php");
exit();
?>