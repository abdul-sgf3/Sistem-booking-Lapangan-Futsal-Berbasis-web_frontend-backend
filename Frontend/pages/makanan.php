<?php
// makanan.php - Katalog Makanan & Minuman Vincent Arena
session_start();
include '../../config/database.php';

$title = 'Makanan & Minuman - Vincent Arena';
$halaman = 'makanan.php'; // Menentukan menu aktif di header
include '../includes/header.php';

// Ambil filter kategori jika ada
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : 'semua';

// Query data makanan & minuman
if ($kategori != 'semua') {
    $stmt = mysqli_prepare($conn, "SELECT * FROM makanan_minuman WHERE kategori = ? ORDER BY nama ASC");
    mysqli_stmt_bind_param($stmt, "s", $kategori);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, "SELECT * FROM makanan_minuman ORDER BY kategori DESC, nama ASC");
}
?>

<style>
    :root { 
        --hm-navy: #131b33; 
        --hm-red: #f01d24; 
        --hm-line: #e2e8f0; 
        --hm-muted: #64748b; 
    }

    body {
        background-color: #f8fafc;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
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
        text-align: center;
        margin-bottom: 32px;
    }

    .page-header h1 {
        font-size: 32px;
        font-weight: 800;
        color: var(--hm-navy);
        margin: 0 0 8px 0;
    }

    .page-header p {
        color: var(--hm-muted);
        font-size: 14px;
        margin: 0;
    }

    /* Filter Category Buttons */
    .filter-container {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin-bottom: 36px;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 10px 22px;
        border-radius: 30px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        color: var(--hm-muted);
        background: white;
        border: 1px solid var(--hm-line);
        transition: all 0.2s ease;
    }

    .filter-btn:hover {
        border-color: var(--hm-navy);
        color: var(--hm-navy);
    }

    .filter-btn.active {
        background: var(--hm-navy);
        color: white;
        border-color: var(--hm-navy);
        box-shadow: 0 4px 12px rgba(19, 27, 51, 0.15);
    }

    /* Menu Grid */
    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 24px;
    }

    .menu-card {
        background: white;
        border-radius: 16px;
        border: 1px solid var(--hm-line);
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        flex-direction: column;
    }

    .menu-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.08);
    }

    .menu-img-container {
        position: relative;
        width: 100%;
        height: 180px;
        background: #f1f5f9;
        overflow: hidden;
    }

    .menu-img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .badge-category {
        position: absolute;
        top: 12px;
        left: 12px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-makanan { background: #fee2e2; color: #991b1b; }
    .badge-minuman { background: #e0f2fe; color: #075985; }

    .menu-body {
        padding: 18px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .menu-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--hm-navy);
        margin: 0 0 6px 0;
    }

    .menu-desc {
        font-size: 13px;
        color: var(--hm-muted);
        margin: 0 0 16px 0;
        line-height: 1.5;
        flex: 1;
    }

    .menu-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: auto;
        padding-top: 12px;
        border-top: 1px dashed var(--hm-line);
    }

    .menu-price {
        font-size: 18px;
        font-weight: 800;
        color: var(--hm-red);
    }

    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
        color: var(--hm-muted);
        background: white;
        border-radius: 16px;
        border: 1px solid var(--hm-line);
    }
</style>

<div class="container">
    <div class="page-header">
        <h1>🍿 Makanan & Minuman</h1>
        <p>Pilihan cemilan dan minuman segar untuk menemani aktivitas olahraga kamu di Vincent Arena</p>
    </div>

    <!-- Filter Kategori -->
    <div class="filter-container">
        <a href="makanan.php?kategori=semua" class="filter-btn <?= ($kategori == 'semua') ? 'active' : ''; ?>">Semua Menu</a>
        <a href="makanan.php?kategori=makanan" class="filter-btn <?= ($kategori == 'makanan') ? 'active' : ''; ?>">Makanan</a>
        <a href="makanan.php?kategori=minuman" class="filter-btn <?= ($kategori == 'minuman') ? 'active' : ''; ?>">Minuman</a>
    </div>

    <!-- Daftar Menu -->
    <div class="menu-grid">
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($item = mysqli_fetch_assoc($result)): 
                $foto_path = '../assets/uploads/makanan/' . $item['foto'];
                if (empty($item['foto']) || !file_exists($foto_path)) {
                    $foto_path = '../assets/uploads/makanan/default-food.jpg';
                }
            ?>
                <div class="menu-card">
                    <div class="menu-img-container">
                        <img src="<?= $foto_path; ?>" alt="<?= htmlspecialchars($item['nama']); ?>">
                        <span class="badge-category <?= ($item['kategori'] == 'makanan') ? 'badge-makanan' : 'badge-minuman'; ?>">
                            <?= htmlspecialchars($item['kategori']); ?>
                        </span>
                    </div>
                    <div class="menu-body">
                        <h3 class="menu-title"><?= htmlspecialchars($item['nama']); ?></h3>
                        <p class="menu-desc"><?= htmlspecialchars($item['deskripsi'] ?? 'Menu siap disajikan pasca berolahraga.'); ?></p>
                        <div class="menu-footer">
                            <div class="menu-price">Rp <?= number_format($item['harga'], 0, ',', '.'); ?></div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <p style="font-size: 16px; margin: 0;">🥤 Belum ada menu makanan/minuman yang tersedia saat ini.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>