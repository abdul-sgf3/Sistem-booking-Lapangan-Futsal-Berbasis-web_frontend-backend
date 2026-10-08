<?php
// proses_booking.php
session_start();
include '../../Backend/config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = $_SESSION['user_id'];
    $id_lapangan = (int)$_POST['id_lapangan'];
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jam_mulai = mysqli_real_escape_string($conn, $_POST['jam_mulai']);
    $durasi = (int)$_POST['durasi'];
    $total_harga = (int)$_POST['total_harga'];
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan'] ?? '');
    $metode_pembayaran = $_POST['metode_pembayaran'] ?? 'transfer';
    
    $kode_booking = 'BSK' . date('Ymd') . rand(100, 999);
    
    // Upload bukti
    $bukti = '';
    if ($metode_pembayaran == 'transfer' && isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0) {
        $target_dir = "../assets/uploads/bukti/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_extension = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
        $bukti = time() . '_' . uniqid() . '.' . $file_extension;
        move_uploaded_file($_FILES['bukti']['tmp_name'], $target_dir . $bukti);
    }
    
    // Cek bentrok jadwal
    $cek = mysqli_query($conn, "SELECT id FROM booking 
                                WHERE id_lapangan = $id_lapangan 
                                AND tanggal = '$tanggal' 
                                AND jam_mulai = '$jam_mulai' 
                                AND status != 'canceled'");
    
    if (mysqli_num_rows($cek) > 0) {
        $_SESSION['booking_error'] = "Jam tersebut sudah dibooking! Silakan pilih jam lain.";
        header("Location: ../pages/detail_lapangan.php?id=$id_lapangan");
        exit();
    }
    
    $status = 'pending';
    
    $query = "INSERT INTO booking (id_user, id_lapangan, kode_booking, tanggal, jam_mulai, durasi, total_harga, catatan, bukti_pembayaran, status) 
              VALUES ($id_user, $id_lapangan, '$kode_booking', '$tanggal', '$jam_mulai', $durasi, $total_harga, '$catatan', '$bukti', '$status')";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['booking_success'] = "Booking berhasil! Kode booking: $kode_booking.";
        header("Location: ../pages/riwayat_booking.php");
        exit();
    } else {
        $_SESSION['booking_error'] = "Gagal melakukan booking!";
        header("Location: ../pages/detail_lapangan.php?id=$id_lapangan");
        exit();
    }
} else {
    header("Location: ../pages/lapangan.php");
    exit();
}
?>