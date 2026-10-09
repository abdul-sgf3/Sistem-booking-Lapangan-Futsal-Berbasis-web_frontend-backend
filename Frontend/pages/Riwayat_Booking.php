<?php
// riwayat_booking.php - Riwayat Booking User
session_start();
include '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../autentikasi/login.php");
    exit();
}

$id_user = $_SESSION['user_id'];
$title = 'Riwayat Booking - Vincent Arena';
$halaman = 'riwayat_booking.php';
include '../includes/header.php';

// Prepared Statement untuk mencegah SQL Injection
$query = "SELECT b.*, l.nama_lapangan 
          FROM booking b 
          JOIN lapangan l ON b.id_lapangan = l.id 
          WHERE b.id_user = ? 
          ORDER BY b.created_at DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id_user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<style>
    :root { 
        --hm-navy: #131b33; 
        --hm-red: #f01d24; 
        --hm-ok: #16a34a; 
        --hm-line: #e2e8f0; 
        --hm-muted: #64748b; 
    }

    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        background-color: #f8fafc;
    }

    .container {
        flex: 1;
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
        width: 100%;
        box-sizing: border-box;
    }

    .page-header {
        margin-bottom: 30px;
        text-align: center;
    }
    
    .page-header h1 {
        font-size: 30px;
        font-weight: 800;
        color: var(--hm-navy);
        margin: 0 0 8px 0;
    }

    .page-header p {
        color: var(--hm-muted);
        font-size: 14px;
        margin: 0;
    }
    
    .table-container {
        background: white;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid var(--hm-line);
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        overflow-x: auto;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    th {
        background: var(--hm-navy);
        color: white;
        padding: 14px 16px;
        text-align: left;
        font-size: 13px;
        font-weight: 600;
    }

    th:first-child { border-top-left-radius: 8px; }
    th:last-child { border-top-right-radius: 8px; }
    
    td {
        padding: 14px 16px;
        text-align: left;
        border-bottom: 1px solid var(--hm-line);
        font-size: 13px;
        color: #334155;
    }
    
    tr:hover {
        background: #f8fafc;
    }
    
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-confirmed { background: #dcfce7; color: #166534; }
    .status-completed { background: #e0f2fe; color: #075985; }
    .status-canceled { background: #fee2e2; color: #991b1b; }
    
    .btn-action {
        padding: 6px 14px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
        transition: background .2s;
    }

    .btn-cancel { background: #ef4444; color: white; }
    .btn-cancel:hover { background: #dc2626; }
    
    .btn-upload { background: #10b981; color: white; }
    .btn-upload:hover { background: #059669; }

    .btn-view { background: #3b82f6; color: white; }
    .btn-view:hover { background: #2563eb; }

    .btn-primary {
        background: var(--hm-navy);
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-block;
    }

    .empty-data {
        text-align: center;
        padding: 50px 20px;
        color: var(--hm-muted);
    }
    
    .kode-booking {
        font-family: monospace;
        font-weight: 700;
        color: var(--hm-red);
    }
</style>

<div class="container">
    <div class="page-header">
        <h1>📋 Riwayat Booking</h1>
        <p>Daftar dan status transaksi lapangan futsal Anda</p>
    </div>
    
    <div class="table-container">
        <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Lapangan</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Durasi</th>
                    <th>Total</th>
                    <th>Bukti</th>
                    <th>Status Bayar</th>
                    <th>Status Booking</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($booking = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td class="kode-booking"><?= htmlspecialchars($booking['kode_booking']); ?></td>
                    <td><?= htmlspecialchars($booking['nama_lapangan']); ?></td>
                    <td><?= date('d/m/Y', strtotime($booking['tanggal'])); ?></td>
                    <td><?= date('H:i', strtotime($booking['jam_mulai'])); ?> WIB</td>
                    <td><?= htmlspecialchars($booking['durasi']); ?> Jam</td>
                    <td style="font-weight:700; color:var(--hm-red);">Rp <?= number_format($booking['total_harga'], 0, ',', '.'); ?></td>
                    
                    <!-- Kolom Bukti Pembayaran -->
                    <td>
                        <?php if (!empty($booking['bukti_pembayaran']) && file_exists('../assets/uploads/bukti/' . $booking['bukti_pembayaran'])): ?>
                            <a href="../assets/uploads/bukti/<?= rawurlencode($booking['bukti_pembayaran']); ?>" target="_blank" class="btn-action btn-view">📎 Lihat</a>
                        <?php else: ?>
                            <?php if ($booking['status'] == 'pending'): ?>
                                <a href="upload_bukti.php?id=<?= $booking['id']; ?>" class="btn-action btn-upload">📤 Upload</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    
                    <!-- Kolom Status Pembayaran -->
                    <td>
                        <?php if ($booking['status'] == 'confirmed'): ?>
                            <span class="status-badge status-confirmed">✅ Lunas</span>
                        <?php elseif ($booking['status'] == 'pending' && !empty($booking['bukti_pembayaran'])): ?>
                            <span class="status-badge status-pending">🔄 Verifikasi</span>
                        <?php elseif ($booking['status'] == 'pending'): ?>
                            <span class="status-badge status-pending">⏳ Belum Bayar</span>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    
                    <!-- Kolom Status Booking -->
                    <td>
                        <?php
                        $status_class = '';
                        switch($booking['status']) {
                            case 'pending': $status_class = 'status-pending'; break;
                            case 'confirmed': $status_class = 'status-confirmed'; break;
                            case 'completed': $status_class = 'status-completed'; break;
                            case 'canceled': $status_class = 'status-canceled'; break;
                        }
                        ?>
                        <span class="status-badge <?= $status_class; ?>"><?= ucfirst($booking['status']); ?></span>
                    </td>
                    
                    <!-- Kolom Aksi -->
                    <td>
                        <?php if ($booking['status'] == 'pending'): ?>
                            <a href="batalkan_booking.php?id=<?= $booking['id']; ?>" class="btn-action btn-cancel" onclick="return confirm('Yakin ingin membatalkan booking ini?')">Batalkan</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-data">
            <p style="font-size: 16px; margin-bottom: 20px;">📭 Belum ada riwayat booking. Yuk sewa lapangan sekarang!</p>
            <a href="lapangan.php" class="btn-primary">Lihat Lapangan</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>