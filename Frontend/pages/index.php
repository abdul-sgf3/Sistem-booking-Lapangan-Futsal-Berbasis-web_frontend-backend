<?php
// index.php - Halaman Depan User
session_start();
include 'config/database.php';

$title = 'Beranda - Tsubasa Arena';
include 'includes/header.php';

$keunggulan = [
    ['Lokasi Strategis',    '<path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/>'],
    ['Fasilitas Lengkap',   '<path d="M4 7h16M4 12h16M4 17h10"/><path d="m17 16 2 2 3-4"/>'],
    ['Pencahayaan Premium', '<path d="M9 18h6m-5 3h4M12 3a6 6 0 0 0-3.5 10.9c.5.4.8 1 .8 1.6V16h5.4v-.5c0-.6.3-1.2.8-1.6A6 6 0 0 0 12 3z"/>'],
    ['Aman & Nyaman',       '<path d="M12 3 4 6v6c0 4.4 3.4 8.1 8 9 4.6-.9 8-4.6 8-9V6l-8-3z"/><path d="m9 12 2 2 4-4"/>'],
    ['Harga Kompetitif',    '<circle cx="12" cy="12" r="9"/><path d="M14.5 9.5c-.4-.9-1.4-1.5-2.5-1.5-1.4 0-2.5.8-2.5 2s1 1.7 2.5 2 2.5.8 2.5 2-1.1 2-2.5 2c-1.1 0-2.1-.6-2.5-1.5M12 6.5V8m0 8v1.5"/>'],
];
?>

<style>
    html { overflow-x: hidden; }
    :root { --hm-navy:#131b33; --hm-red:#f01d24; --hm-ok:#16a34a; --hm-line:#e2e8f0; --hm-muted:#64748b; }

    /* ===== HERO (lebar penuh) ===== */
    .hm-hero {
        position: relative; width: 100vw; margin-left: calc(50% - 50vw);
        min-height: 440px; display: flex; align-items: center;
        background: var(--hm-navy); overflow: hidden; color: #fff;
    }
    .hm-hero img.hm-bg {
        position: absolute; inset: 0; width: 100%; height: 100%;
        object-fit: cover; object-position: center 60%;
    }
    .hm-hero::after {
        content: ""; position: absolute; inset: 0;
        background: linear-gradient(90deg, var(--hm-navy) 0%, var(--hm-navy) 32%, rgba(19,27,51,.72) 50%, rgba(19,27,51,.15) 100%);
    }
    .hm-wrap { width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 24px; box-sizing: border-box; }
    .hm-hero .hm-wrap { position: relative; z-index: 2; padding-top: 40px; padding-bottom: 70px; }
    .hm-hero h1 { font-size: 46px; line-height: 1.15; font-weight: 800; margin: 0; max-width: 620px; }
    .hm-hero h1 span { color: var(--hm-red); }
    .hm-hero p { font-size: 15px; line-height: 1.7; margin: 22px 0 0; color: #e5e7eb; max-width: 520px; }
    .hm-btn {
        display: inline-block; margin-top: 28px; background: var(--hm-red); color: #fff;
        font-weight: 600; font-size: 15px; padding: 14px 26px; border-radius: 10px; text-decoration: none;
        text-align: center;
    }
    .hm-btn:hover { background: #d4141b; }
    .hm-badge {
        position: absolute; z-index: 2; right: 28px; bottom: 22px; display: flex; align-items: center; gap: 8px;
        background: rgba(229,231,235,.9); color: #4b5563; font-size: 14px; font-weight: 600;
        padding: 10px 18px; border-radius: 12px;
    }
    .hm-badge svg { width: 18px; height: 18px; stroke: #4b5563; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }

    /* ===== SECTION ===== */
    .hm-sec { padding: 56px 0 0; }
    .hm-title { text-align: center; margin-bottom: 32px; }
    .hm-title h2 { margin: 0; font-size: 30px; font-weight: 800; color: var(--hm-navy); }
    .hm-title p { margin: 8px auto 0; max-width: 460px; font-size: 13px; color: var(--hm-muted); line-height: 1.6; }

    /* ===== CARD LAPANGAN ===== */
    .hm-list { display: flex; flex-direction: column; gap: 20px; }
    .hm-card {
        display: flex; background: #fff; border: 1px solid #cbd5e1; border-radius: 16px;
        overflow: hidden; transition: box-shadow .2s;
    }
    .hm-card:hover { box-shadow: 0 10px 24px rgba(0,0,0,.10); }
    .hm-img {
        position: relative; width: 52%; flex-shrink: 0; min-height: 270px;
        background: #cbd5e1 center/cover no-repeat; border-radius: 16px;
    }
    .hm-status {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(0,0,0,.72); color: #fff; font-size: 12px; font-weight: 600; padding: 5px 12px; border-radius: 999px;
    }
    .hm-status i { width: 8px; height: 8px; border-radius: 50%; background: var(--hm-ok); flex-shrink: 0; }
    .hm-body { padding: 24px 28px; display: flex; flex-direction: column; justify-content: center; flex: 1; min-width: 0; }
    .hm-body h3 { margin: 0; font-size: 21px; font-weight: 700; color: var(--hm-navy); }
    .hm-body p { margin: 8px 0 0; font-size: 14px; line-height: 1.6; color: var(--hm-muted); }
    .hm-price { margin-top: 10px; font-size: 16px; font-weight: 700; color: var(--hm-red); }
    .hm-price small { font-weight: 500; color: var(--hm-muted); font-size: 13px; }
    .hm-detail {
        align-self: flex-start; margin-top: 16px; background: var(--hm-navy); color: #fff;
        font-size: 14px; font-weight: 600; padding: 10px 22px; border-radius: 10px; text-decoration: none;
    }
    .hm-detail:hover { background: #1f2b4d; }
    .hm-empty { text-align: center; padding: 40px 20px; color: #888; border: 1px dashed var(--hm-line); border-radius: 16px; }

    /* ===== KEUNGGULAN ===== */
    .hm-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; }
    .hm-feat { text-align: center; padding: 22px 12px; border: 1px solid var(--hm-line); border-radius: 16px; background: #fff; transition: box-shadow .2s; }
    .hm-feat:hover { box-shadow: 0 10px 24px rgba(0,0,0,.1); }
    .hm-ico { width: 56px; height: 56px; margin: 0 auto; border-radius: 16px; background: var(--hm-navy); display: grid; place-items: center; }
    .hm-ico svg { width: 28px; height: 28px; stroke: #fff; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
    .hm-feat h3 { margin: 12px 0 0; font-size: 15px; font-weight: 700; color: var(--hm-navy); }

    /* ===== TABLET (<=1023px) ===== */
    @media (max-width: 1023px) {
        .hm-wrap { padding: 0 20px; }
        .hm-hero h1 { font-size: 36px; }
        .hm-grid { grid-template-columns: repeat(3, 1fr); }
        .hm-hero::after { background: linear-gradient(90deg, var(--hm-navy) 0%, rgba(19,27,51,.85) 60%, rgba(19,27,51,.35) 100%); }
    }

    /* ===== MOBILE (<=767px) ===== */
    @media (max-width: 767px) {
        .hm-wrap { padding: 0 16px; }
        .hm-sec { padding: 40px 0 0; }

        .hm-hero { min-height: 380px; }
        .hm-hero .hm-wrap { padding-top: 28px; padding-bottom: 60px; }
        .hm-hero h1 { font-size: 26px; max-width: 100%; }
        .hm-hero p { font-size: 13px; max-width: 100%; }
        .hm-btn { width: 100%; padding: 13px 20px; font-size: 14px; }
        .hm-badge { left: 16px; right: 16px; bottom: 14px; font-size: 12px; padding: 9px 14px; justify-content: center; }

        .hm-title h2 { font-size: 22px; }
        .hm-title p { font-size: 12.5px; padding: 0 8px; }

        .hm-card { flex-direction: column; }
        .hm-img { width: 100%; min-height: 190px; border-radius: 16px 16px 0 0; }
        .hm-body { padding: 18px 20px 22px; }
        .hm-body h3 { font-size: 18px; }
        .hm-detail { align-self: stretch; text-align: center; }

        .hm-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; }
        .hm-grid .hm-feat:last-child { grid-column: span 2; }
        .hm-feat { padding: 18px 10px; }
        .hm-ico { width: 48px; height: 48px; }
        .hm-ico svg { width: 24px; height: 24px; }
        .hm-feat h3 { font-size: 13.5px; }
    }

    /* ===== MOBILE KECIL (<=380px) ===== */
    @media (max-width: 380px) {
        .hm-hero h1 { font-size: 22px; }
        .hm-badge { font-size: 11px; }
        .hm-grid { grid-template-columns: 1fr; }
        .hm-grid .hm-feat:last-child { grid-column: span 1; }
    }
</style>

<!-- HERO -->
<section class="hm-hero">
    <img class="hm-bg" src="assets/uploads/lapangan/hero.jpg" alt="Interior lapangan futsal"
         onerror="this.onerror=null;this.src='assets/uploads/lapangan/hero.jpeg'">
    <div class="hm-wrap">
        <h1>Sewa Lapangan Olahraga Favoritmu dengan Sekali <span>Klik</span></h1>
        <p>Vincent Arena kini hadir untuk Futsal, Basket, Badminton, dan Voli dengan fasilitas standar nasional dan sistem konfirmasi realtime.</p>
        <a href="lapangan.php" class="hm-btn">Booking Sekarang</a>
    </div>
    <div class="hm-badge">
        <svg viewBox="0 0 24 24"><path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
        Majasem, Kota Cirebon, Jawa Barat
    </div>
</section>

<!-- PILIHAN LAPANGAN -->
<section class="hm-sec">
    <div class="hm-wrap">
        <div class="hm-title">
            <h2>Pilihan Lapangan Tersedia</h2>
            <p>Pilih jenis lapangan dengan spesifikasi lantai terbaik untuk kenyamanan tim dan performa bertanding maksimal.</p>
        </div>

        <div class="hm-list">
            <?php
            $query  = "SELECT * FROM lapangan WHERE status = 'aktif' ORDER BY id ASC LIMIT 2";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0):
                while ($lapangan = mysqli_fetch_assoc($result)):
                    $foto = (!empty($lapangan['foto']) && file_exists('assets/uploads/lapangan/' . $lapangan['foto']))
                        ? 'assets/uploads/lapangan/' . rawurlencode($lapangan['foto']) : '';
            ?>
            <article class="hm-card">
                <div class="hm-img" <?= $foto ? 'style="background-image:url(\'' . $foto . '\')"' : '' ?>></div>
                <div class="hm-body">
                    <span class="hm-status" style="align-self:flex-start;margin-bottom:10px"><i></i>Tersedia</span>
                    <h3><?= htmlspecialchars($lapangan['nama_lapangan']) ?></h3>
                    <p><?= htmlspecialchars(mb_strimwidth($lapangan['deskripsi'] ?? '', 0, 110, '...')) ?></p>
                    <div class="hm-price">Rp <?= number_format($lapangan['harga_per_jam'], 0, ',', '.') ?> <small>/ jam</small></div>
                    <a href="detail_lapangan.php?id=<?= (int)$lapangan['id'] ?>" class="hm-detail">Booking Sekarang →</a>
                </div>
            </article>
            <?php
                endwhile;
            else:
            ?>
            <div class="hm-empty">Belum ada lapangan tersedia.</div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- KENAPA TSUBASA ARENA -->
<section class="hm-sec" style="padding-bottom:56px">
    <div class="hm-wrap">
        <div class="hm-title">
            <h2>Kenapa Harus Bermain di <span style="color:var(--hm-red)">Vincent Arena</span>?</h2>
            <p>Didesain khusus untuk kebutuhan yang hobi futsal hingga untuk kompetisi turnamen di kota Cirebon.</p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>