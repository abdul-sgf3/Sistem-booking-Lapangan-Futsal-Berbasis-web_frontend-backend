<?php
// upload_bukti.php - Upload bukti pembayaran
session_start();
include 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Cek booking milik user ini
$cek = mysqli_query($conn, "SELECT * FROM booking WHERE id = $id AND id_user = {$_SESSION['user_id']}");
if (mysqli_num_rows($cek) == 0) {
    header("Location: riwayat.php");
    exit();
}

$booking = mysqli_fetch_assoc($cek);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['bukti'])) {
    $target_dir = "assets/uploads/bukti/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $file_extension = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
    $bukti = time() . '_' . uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $bukti;
    
    if (move_uploaded_file($_FILES['bukti']['tmp_name'], $target_file)) {
        mysqli_query($conn, "UPDATE booking SET bukti_pembayaran = '$bukti' WHERE id = $id");
        $_SESSION['success'] = "Bukti pembayaran berhasil diupload! Menunggu konfirmasi admin.";
    } else {
        $_SESSION['error'] = "Gagal upload bukti!";
    }
    header("Location: riwayat.php");
    exit();
}

$title = 'Upload Bukti - Vincent Arena';
include 'includes/header.php';
?>

<style>
    .upload-container {
        max-width: 500px;
        margin: 60px auto;
        background: white;
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        text-align: center;
    }
    
    .info-booking {
        background: #f0f2f5;
        padding: 20px;
        border-radius: 12px;
        margin: 20px 0;
        text-align: left;
    }
    
    .btn-upload {
        background: #e63946;
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 30px;
        font-size: 16px;
        cursor: pointer;
        margin-top: 20px;
    }
    
    .btn-upload:hover {
        background: #c1121f;
    }
</style>

<div class="upload-container">
    <h2>📎 Upload Bukti Transfer</h2>
    <p>Booking: <strong><?= $booking['kode_booking'] ?></strong></p>
    
    <div class="info-booking">
        <p><strong>💰 Total Pembayaran:</strong> Rp <?= number_format($booking['total_harga'], 0, ',', '.') ?></p>
        <p><strong>🏦 Rekening Tujuan:</strong></p>
        <p>Bank Mandiri: 123-456-7890 a.n. Vincent Arena</p>
        <p>Bank BCA: 987-654-3210 a.n. Vincent Arena</p>
    </div>
    
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="bukti" accept="image/*" required style="width: 100%; padding: 10px;">
        <button type="submit" class="btn-upload">📤 Upload Bukti</button>
    </form>
    
    <a href="riwayat.php" style="display: block; margin-top: 20px; color: #888;">← Kembali</a>
</div>

<?php include 'includes/footer.php'; ?>