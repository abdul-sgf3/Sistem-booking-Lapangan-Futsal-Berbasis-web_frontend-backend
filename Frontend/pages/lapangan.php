<?php
// lapangan.php - Halaman Semua Lapangan
session_start();
include '../../config/database.php';

$title = 'Lapangan - Vincent Arena';
include '../includes/header.php';

// Pagination
$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM lapangan WHERE status = 'aktif'");
$total_data = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_data / $limit);

$query = "SELECT * FROM lapangan WHERE status = 'aktif' ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
?>

<style>
    .page-header {
        margin: 30px 0;
        text-align: center;
    }
    
    .page-header h1 {
        font-size: 32px;
        color: #0a2b4e;
    }
    
    .page-header p {
        color: #888;
    }
    
    .lapangan-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }
    
    .lapangan-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transition: all 0.3s;
    }
    
    .lapangan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    .lapangan-img {
        height: 180px;
        background-size: cover;
        background-position: center;
        position: relative;
    }
    
    .lapangan-status {
        position: absolute;
        bottom: 10px;
        left: 10px;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
    }
    
    .lapangan-info {
        padding: 20px;
    }
    
    .lapangan-info h3 {
        color: #0a2b4e;
        margin-bottom: 8px;
    }
    
    .harga {
        font-size: 20px;
        font-weight: bold;
        color: #e63946;
        margin: 12px 0;
    }
    
    .btn-booking {
        background: #0a2b4e;
        color: white;
        padding: 10px 20px;
        border-radius: 25px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        display: inline-block;
        width: 100%;
        text-align: center;
        transition: all 0.3s;
    }
    
    .btn-booking:hover {
        background: #1e4d7c;
    }
    
    .pagination {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin: 30px 0;
        flex-wrap: wrap;
    }
    
    .pagination a, .pagination span {
        padding: 8px 14px;
        border-radius: 8px;
        text-decoration: none;
        color: #0a2b4e;
        background: white;
        border: 1px solid #ddd;
        transition: all 0.3s;
    }
    
    .pagination a:hover {
        background: #e63946;
        color: white;
        border-color: #e63946;
    }
    
    .pagination .active {
        background: #e63946;
        color: white;
        border-color: #e63946;
    }
    
    .empty-data {
        text-align: center;
        padding: 50px;
        color: #888;
    }
</style>

<div class="container">
    <div class="page-header">
        <h1> Lapangan Futsal</h1>
        <p>Pilih lapangan favoritmu dan booking sekarang juga!</p>
    </div>
    
    <div class="lapangan-grid">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($lapangan = mysqli_fetch_assoc($result)): ?>
            <div class="lapangan-card">
                <div class="lapangan-img" style="background-image: linear-gradient(rgba(0,0,0,0.2), rgba(0,0,0,0.2)), url('../assets/uploads/lapangan/<?= $lapangan['foto'] ?? 'default.jpg' ?>');">
                    <span class="lapangan-status">✅ Tersedia</span>
                </div>
                <div class="lapangan-info">
                    <h3><?= $lapangan['nama_lapangan'] ?></h3>
                    <p><?= substr($lapangan['deskripsi'], 0, 80) ?>...</p>
                    <div class="harga">Rp <?= number_format($lapangan['harga_per_jam'], 0, ',', '.') ?> <span style="font-size: 12px;">/ jam</span></div>
                    <a href="detail_lapangan.php?id=<?= $lapangan['id'] ?>" class="btn-booking">Booking Sekarang →</a>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-data">
                <p>📭 Belum ada lapangan tersedia.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination">
        <a href="?page=1">« First</a>
        <a href="?page=<?= $page-1 ?>" class="<?= $page <= 1 ? 'disabled' : '' ?>">‹ Prev</a>
        
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        
        <a href="?page=<?= $page+1 ?>" class="<?= $page >= $total_pages ? 'disabled' : '' ?>">Next ›</a>
        <a href="?page=<?= $total_pages ?>">Last »</a>
    </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>