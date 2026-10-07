<?php
// detail_lapangan.php - Halaman Detail & Form Booking Lapangan
session_start();
include '../../config/database.php';

// Ambil ID lapangan dari URL
$id_lapangan = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_lapangan <= 0) {
    header("Location: lapangan.php");
    exit();
}

// Ambil data lapangan
$query = "SELECT * FROM lapangan WHERE id = ? AND status = 'aktif'";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id_lapangan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$lapangan = mysqli_fetch_assoc($result);

if (!$lapangan) {
    header("Location: lapangan.php");
    exit();
}

$title = htmlspecialchars($lapangan['nama_lapangan']) . ' - Vincent SQ Arena';
include '../includes/header.php';
?>

<style>
    .detail-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 40px;
    }
    .detail-img {
        width: 100%;
        height: 400px;
        background-size: cover;
        background-position: center;
        border-radius: 16px;
        margin-bottom: 24px;
    }
    .detail-info h1 {
        font-size: 32px;
        font-weight: 800;
        color: #0F172A;
        margin: 0 0 16px 0;
    }
    .detail-harga {
        font-size: 28px;
        font-weight: 800;
        color: #DC2626;
        margin-bottom: 24px;
    }
    .detail-deskripsi {
        font-size: 15px;
        line-height: 1.7;
        color: #64748B;
        margin-bottom: 24px;
    }
    .fasilitas-list {
        list-style: none;
        padding: 0;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .fasilitas-list li {
        font-size: 14px;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .fasilitas-list li::before {
        content: "✓";
        color: #16A34A;
        font-weight: 900;
    }
    
    /* Form Booking */
    .booking-form {
        background: white;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        position: sticky;
        top: 100px;
    }
    .booking-form h3 {
        font-size: 20px;
        font-weight: 700;
        color: #0F172A;
        margin: 0 0 20px 0;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
    }
    .form-group input, .form-group select {
        width: 100%;
        padding: 12px;
        border: 1px solid #CBD5E1;
        border-radius: 8px;
        font-size: 14px;
        box-sizing: border-box;
    }
    .btn-submit {
        width: 100%;
        background: #DC2626;
        color: white;
        padding: 14px;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        margin-top: 10px;
        transition: background 0.2s;
    }
    .btn-submit:hover { background: #B91C1C; }

    @media (max-width: 1023px) {
        .detail-container { grid-template-columns: 1fr; }
        .booking-form { position: static; }
    }
</style>

<div class="detail-container">
    <!-- KIRI: Info Lapangan -->
    <div class="detail-info">
        <div class="detail-img" style="background-image: url('../assets/uploads/lapangan/<?= htmlspecialchars($lapangan['foto'] ?? 'default.jpg') ?>');"></div>
        <h1><?= htmlspecialchars($lapangan['nama_lapangan']) ?></h1>
        <div class="detail-harga">Rp <?= number_format($lapangan['harga_per_jam'], 0, ',', '.') ?> <small style="font-size: 14px; color:#64748B;">/ jam</small></div>
        
        <h3 style="font-size: 18px; color:#0F172A; margin-bottom: 12px;">Deskripsi</h3>
        <p class="detail-deskripsi"><?= nl2br(htmlspecialchars($lapangan['deskripsi'] ?? 'Lapangan berkualitas dengan fasilitas terbaik.')) ?></p>
        
        <h3 style="font-size: 18px; color:#0F172A; margin-bottom: 12px;">Fasilitas</h3>
        <ul class="fasilitas-list">
            <li>Lapangan berstandar nasional</li>
            <li>Pencahayaan LED</li>
            <li>Loker & Ruang Ganti</li>
            <li>Area Parkir Luas</li>
            <li>Toilet Bersih</li>
            <li>Musholla</li>
        </ul>
    </div>

    <!-- KANAN: Form Booking -->
    <div>
        <div class="booking-form">
            <h3>📅 Form Booking</h3>
            <form action="../autentikasi/proses_booking.php" method="POST">
                <input type="hidden" name="id_lapangan" value="<?= $lapangan['id'] ?>">
                <input type="hidden" name="harga_per_jam" value="<?= $lapangan['harga_per_jam'] ?>">
                
                <div class="form-group">
                    <label>Tanggal Bermain</label>
                    <input type="date" name="tanggal" required min="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-group">
                    <label>Jam Mulai</label>
                    <input type="time" name="jam_mulai" required>
                </div>
                <div class="form-group">
                    <label>Durasi (Jam)</label>
                    <select name="durasi" required>
                        <option value="1">1 Jam</option>
                        <option value="2">2 Jam</option>
                        <option value="3">3 Jam</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-submit">Booking Sekarang</button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>