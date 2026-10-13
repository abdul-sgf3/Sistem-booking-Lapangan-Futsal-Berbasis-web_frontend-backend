<?php
// index.php - Halaman Depan User (Final)
session_start();

// ============================================================
// KONEKSI DATABASE (DIPERBAIKI)
// Nama database yang benar: vincent_sqdb
// ============================================================
$db_paths = [
    __DIR__ . '/../../Backend/config/database.php',
    __DIR__ . '/../../../Backend/config/database.php',
    __DIR__ . '/../../Backend/config.php',
    __DIR__ . '/../../../Backend/config.php',
    $_SERVER['DOCUMENT_ROOT'] . '/vincent-arena/Backend/config/database.php',
    $_SERVER['DOCUMENT_ROOT'] . '/vincent-arena/Backend/config.php',
];

$db_path = null;
foreach ($db_paths as $path) {
    if (file_exists($path)) {
        $db_path = $path;
        break;
    }
}

if ($db_path) {
    include $db_path;
}

// Fallback: koneksi langsung jika $conn belum terdefinisi
if (!isset($conn) || !$conn) {
    // ✅ NAMA DATABASE: vincent_sqdb
    $conn = @mysqli_connect('localhost', 'root', '', 'vincent_sqdb');
    if ($conn) {
        mysqli_set_charset($conn, 'utf8mb4');
    }
}

$title = 'Beranda - Vincent SQ Arena';
include '../includes/header.php';
?>

<style>
    :root {
        --vsq-navy: #0F172A;
        --vsq-blue: #2563EB;
        --vsq-red: #DC2626;
        --vsq-gold: #FACC15;
        --vsq-gray: #64748B;
        --vsq-border: #E2E8F0;
        --vsq-footer-bg: #0B1120;
    }

    body {
        background-color: #ffffff;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* ===== HERO SECTION ===== */
    .vsq-hero {
        position: relative;
        width: 100%;
        min-height: 480px;
        background: 
            linear-gradient(to right, rgba(15, 23, 42, 0.95) 0%, rgba(15, 23, 42, 0.6) 50%, rgba(15, 23, 42, 0.3) 100%),
            linear-gradient(to bottom, rgba(15, 23, 42, 0.6) 0%, rgba(15, 23, 42, 0.6) 100%),
            url('../assets/uploads/lapangan/hero.jpeg') center/cover no-repeat;
        display: flex;
        align-items: center;
        color: white;
        padding: 0 20px;
        box-sizing: border-box;
    }

    .vsq-hero-content {
        max-width: 1200px;
        margin: 0 auto;
        width: 100%;
        padding-top: 20px;
        position: relative;
        z-index: 2;
    }

    .vsq-hero h1 {
        font-size: 46px;
        font-weight: 800;
        line-height: 1.15;
        margin: 0 0 20px 0;
        max-width: 700px;
        letter-spacing: -0.5px;
        text-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }

    .vsq-hero h1 span { 
        color: var(--vsq-red); 
    }

    .vsq-hero p {
        font-size: 16px;
        line-height: 1.6;
        color: #E2E8F0;
        max-width: 550px;
        margin: 0;
        font-weight: 400;
        text-shadow: 0 1px 5px rgba(0,0,0,0.3);
    }

    .vsq-location-badge {
        position: absolute;
        bottom: 30px;
        right: 40px;
        background: #FFFFFF;
        color: var(--vsq-navy);
        padding: 10px 22px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        z-index: 10;
    }

    /* ===== SECTION UMUM ===== */
    .vsq-section {
        padding: 50px 20px;
        max-width: 1200px;
        margin: 0 auto;
        box-sizing: border-box;
    }

    .vsq-section.section-lapangan {
        padding-top: 30px;
    }

    .vsq-section-title {
        text-align: center;
        margin-bottom: 40px;
    }

    .vsq-section-title h2 {
        font-size: 36px;
        font-weight: 800;
        color: var(--vsq-navy);
        margin: 0 0 12px 0;
    }

    .vsq-section-title.title-blue h2 { color: var(--vsq-blue); }

    .vsq-section-title p {
        font-size: 15px;
        color: var(--vsq-gray);
        margin: 0;
    }

    .vsq-section-title.title-blue p { color: var(--vsq-blue); }

    /* ===== KARTU LAPANGAN ===== */
    .vsq-lapangan-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 30px;
        align-items: stretch;
        max-width: 1000px;
        margin: 0 auto;
    }

    .vsq-card {
        background: white;
        border: 1px solid var(--vsq-border);
        border-radius: 20px;
        overflow: hidden;
        display: flex;
        flex-direction: row;
        transition: box-shadow 0.3s, transform 0.3s;
        height: 320px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }

    .vsq-card:hover {
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        transform: translateY(-4px);
    }

    .vsq-card-img {
        position: relative;
        width: 45%;
        flex-shrink: 0;
        background-size: cover;
        background-position: center;
    }

    .vsq-card-body {
        padding: 30px 40px;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .vsq-card-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 35px;
        flex-wrap: wrap;
    }

    .vsq-card-header h3 {
        font-size: 24px;
        font-weight: 800;
        color: var(--vsq-navy);
        margin: 0;
        line-height: 1.2;
    }

    .vsq-card-badge {
        padding: 6px 20px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 800;
        white-space: nowrap;
        display: inline-block;
        letter-spacing: 0.5px;
    }

    .badge-vip { background: var(--vsq-gold); color: var(--vsq-navy); }
    .badge-regular { background: #E2E8F0; color: var(--vsq-navy); }

    .vsq-fasilitas {
        list-style: none;
        padding: 0;
        margin: 0 0 30px 0;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .vsq-fasilitas li {
        font-size: 15px;
        color: #1E293B;
        display: flex;
        align-items: center;
        gap: 12px;
        line-height: 1.4;
        font-weight: 500;
    }

    .vsq-fasilitas li::before {
        content: "✓";
        color: white;
        background: var(--vsq-navy);
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 900;
        flex-shrink: 0;
    }

    .vsq-card-footer {
        margin-top: auto;
    }

    .vsq-btn-booking {
        background: var(--vsq-red);
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 700;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        border: none;
        cursor: pointer;
    }

    .vsq-btn-booking::after {
        content: "→";
        font-size: 18px;
        font-weight: 400;
        transition: transform 0.2s;
    }

    .vsq-btn-booking:hover { background: #B91C1C; }
    .vsq-btn-booking:hover::after { transform: translateX(5px); }

    /* ===== CARA BOOKING ===== */
    .vsq-steps {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        align-items: center;
    }

    .vsq-step-wrapper {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .vsq-step-wrapper:last-child { gap: 0; }

    .vsq-step {
        background: #FFFFFF;
        border: 1px solid var(--vsq-border);
        border-radius: 16px;
        padding: 20px 16px 24px;
        text-align: center;
        flex: 1;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        min-height: 200px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .vsq-step-header {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        margin-bottom: 14px;
        width: 100%;
    }

    .vsq-step-number {
        width: 36px;
        height: 36px;
        background: var(--vsq-blue);
        color: white;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 16px;
        font-weight: 800;
        flex-shrink: 0;
    }

    .vsq-step-svg {
        width: 44px;
        height: 44px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }

    .vsq-step-svg svg {
        width: 44px;
        height: 44px;
        stroke: var(--vsq-blue);
        fill: none;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .vsq-step h4 {
        font-size: 14px;
        font-weight: 700;
        color: var(--vsq-blue);
        margin: 0 0 8px 0;
    }

    .vsq-step p {
        font-size: 12px;
        color: var(--vsq-gray);
        margin: 0;
        line-height: 1.5;
    }

    .vsq-arrow {
        font-size: 32px;
        color: var(--vsq-blue);
        font-weight: 300;
        flex-shrink: 0;
    }

    /* ===== FAQ ===== */
    .vsq-faq-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        align-items: start;
    }

    .vsq-faq-item {
        background: #FFFFFF;
        border: 1.5px solid #E2E8F0;
        border-radius: 16px;
        padding: 0;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        overflow: hidden;
    }

    .vsq-faq-item:hover, 
    .vsq-faq-item.active { 
        border-color: var(--vsq-blue);
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.1);
    }

    .vsq-faq-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 24px 28px;
        gap: 16px;
        user-select: none;
    }

    .vsq-faq-header .faq-text {
        display: flex;
        gap: 16px;
        align-items: flex-start;
        font-size: 16px;
        font-weight: 600;
        color: #1E293B;
        line-height: 1.4;
    }

    .vsq-faq-header .faq-number {
        color: #1E293B;
        font-weight: 700;
        flex-shrink: 0;
    }

    .vsq-faq-header .faq-arrow {
        color: var(--vsq-blue);
        font-size: 20px;
        font-weight: 400;
        flex-shrink: 0;
        transition: transform 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
    }

    .vsq-faq-item.active .faq-arrow { transform: rotate(180deg); }

    .vsq-faq-body {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease, padding 0.3s ease;
        padding: 0 28px;
        font-size: 14px;
        color: var(--vsq-gray);
        line-height: 1.6;
    }

    .vsq-faq-item.active .vsq-faq-body {
        max-height: 200px;
        padding: 0 28px 24px 28px;
    }

    /* ===== FOOTER ===== */
    .vsq-footer {
        background-color: var(--vsq-footer-bg);
        color: #FFFFFF;
        padding: 60px 20px 30px 20px;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .vsq-footer-content {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1.5fr 1fr 1.5fr;
        gap: 40px;
        margin-bottom: 50px;
    }

    .vsq-footer-brand {
        font-size: 24px;
        font-weight: 800;
        color: #FFFFFF;
        letter-spacing: -0.5px;
    }

    .vsq-footer-brand span {
        color: var(--vsq-red);
    }

    .vsq-footer-menu {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .vsq-footer-menu a {
        color: #FFFFFF;
        text-decoration: none;
        font-size: 16px;
        font-weight: 600;
        transition: color 0.2s;
    }

    .vsq-footer-menu a:hover {
        color: var(--vsq-red);
    }

    .vsq-footer-contact {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .vsq-footer-contact-item {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 16px;
        font-weight: 600;
        color: #FFFFFF;
    }

    .vsq-footer-contact-item svg {
        width: 20px;
        height: 20px;
        stroke: var(--vsq-red);
        fill: none;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
        flex-shrink: 0;
    }

    .vsq-footer-bottom {
        max-width: 1200px;
        margin: 0 auto;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 30px;
        text-align: center;
        font-size: 14px;
        color: #94A3B8;
        font-weight: 500;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1023px) {
        .vsq-steps { grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .vsq-step-wrapper { flex-direction: column; gap: 10px; }
        .vsq-arrow { display: none; }
        
        .vsq-footer-content {
            grid-template-columns: 1fr 1fr;
        }
        .vsq-footer-brand {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 767px) {
        .vsq-hero { 
            min-height: 400px; 
            padding: 40px 20px;
            background: 
                linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.8)),
                url('../assets/uploads/lapangan/hero.jpeg') center/cover no-repeat;
        }
        .vsq-hero h1 { font-size: 32px; max-width: 100%; }
        .vsq-hero p { font-size: 14px; max-width: 100%; }
        .vsq-location-badge { position: static; margin-top: 30px; display: inline-flex; width: fit-content; }
        
        .vsq-card { flex-direction: column; height: auto; }
        .vsq-card-img { width: 100%; height: 200px; }
        .vsq-card-body { padding: 20px; }
        .vsq-card-header { margin-bottom: 20px; }
        .vsq-card-header h3 { font-size: 20px; }
        .vsq-fasilitas { gap: 10px; margin-bottom: 20px; }
        .vsq-fasilitas li { font-size: 13px; }
        .vsq-btn-booking { width: 100%; justify-content: center; }
        
        .vsq-faq-grid { grid-template-columns: 1fr; gap: 16px; }
        .vsq-faq-header { padding: 18px 20px; }
        .vsq-faq-header .faq-text { font-size: 14px; gap: 10px; }
        .vsq-faq-body { padding: 0 20px; }
        .vsq-faq-item.active .vsq-faq-body { padding: 0 20px 20px 20px; }
        
        .vsq-steps { grid-template-columns: 1fr; gap: 20px; }
        .vsq-section-title h2 { font-size: 24px; }
        .vsq-section.section-lapangan { padding-top: 20px; }
        
        .vsq-footer-content {
            grid-template-columns: 1fr;
            gap: 30px;
            text-align: center;
        }
        .vsq-footer-brand {
            text-align: center;
        }
        .vsq-footer-menu {
            align-items: center;
        }
        .vsq-footer-contact {
            align-items: center;
        }
        .vsq-footer-contact-item {
            justify-content: center;
        }
    }
</style>

<!-- HERO -->
<section class="vsq-hero">
    <div class="vsq-hero-content">
        <h1>Sewa Lapangan Olahraga Favoritmu dengan Sekali <span>Klik</span></h1>
        <p>Vincent'SQ Arena hadir untuk lapangan Futsal dengan fasilitas standar nasional dan sistem konfirmasi realtime.</p>
    </div>
    <div class="vsq-location-badge">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;">
            <path d="M12 2C8.13 2 5 5.13 5 9c0 3.5 5.5 9.5 6.4 10.4.3.3.9.3 1.2 0C13.5 18.5 19 12.5 19 9c0-3.87-3.13-7-7-7z" stroke="#0F172A" stroke-width="2" fill="none"/>
            <circle cx="12" cy="9" r="2.5" fill="#0F172A"/>
        </svg>
        <span>Tuparev, Kota Cirebon. Jawabarat</span>
    </div>
</section>

<!-- PILIHAN LAPANGAN -->
<section class="vsq-section section-lapangan">
    <div class="vsq-section-title">
        <h2>Pilihan Lapangan Tersedia</h2>
    </div>

    <div class="vsq-lapangan-grid">
        <?php
        if (!isset($conn) || !$conn) {
            echo '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #888;">';
            echo '<p style="font-size: 16px; font-weight: 600;">⚠️ Koneksi database gagal</p>';
            echo '</div>';
        } else {
            $query  = "SELECT * FROM lapangan WHERE status = 'aktif' ORDER BY id ASC LIMIT 4";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0):
                while ($lapangan = mysqli_fetch_assoc($result)):
                    $foto = (!empty($lapangan['foto']) && file_exists('../assets/uploads/lapangan/' . $lapangan['foto']))
                        ? '../assets/uploads/lapangan/' . rawurlencode($lapangan['foto']) : '';
                    
                    $badge_text = ($lapangan['harga_per_jam'] >= 200000) ? 'VIP' : 'Regular';
                    $badge_class = ($badge_text == 'VIP') ? 'badge-vip' : 'badge-regular';
                    
                    if ($badge_text == 'VIP') {
                        $fasilitas = [
                            'Lapangan dengan kondisi indoor',
                            'Area bermain lebih Luas',
                            'Include dengan area penonton',
                            'Sirkulasi udara yang nyaman'
                        ];
                    } else {
                        $fasilitas = [
                            'Lapangan dengan kondisi indoor',
                            'Area bermain ukuran standar umum'
                        ];
                    }
            ?>
            <article class="vsq-card">
                <div class="vsq-card-img" style="background-image: url('<?= $foto ?>');"></div>
                <div class="vsq-card-body">
                    <div class="vsq-card-header">
                        <h3><?= htmlspecialchars($lapangan['nama_lapangan']) ?></h3>
                        <span class="vsq-card-badge <?= $badge_class ?>"><?= $badge_text ?></span>
                    </div>
                    <ul class="vsq-fasilitas">
                        <?php foreach ($fasilitas as $f): ?>
                            <li><?= htmlspecialchars($f) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="vsq-card-footer">
                        <a href="detail_lapangan.php?id=<?= (int)$lapangan['id'] ?>" class="vsq-btn-booking">Booking Sekarang</a>
                    </div>
                </div>
            </article>
            <?php
                endwhile;
            else:
            ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #888;">
                Belum ada lapangan tersedia.
            </div>
            <?php endif;
        } ?>
    </div>
</section>

<!-- CARA BOOKING -->
<section class="vsq-section">
    <div class="vsq-section-title title-blue">
        <h2>Cara Booking Lapangan</h2>
        <p>Booking lapangan di Vincent'SQ dapat dilakukan dengan beberapa langkah berikut</p>
    </div>
    <div class="vsq-steps">
        
        <div class="vsq-step-wrapper">
            <div class="vsq-step">
                <div class="vsq-step-header">
                    <div class="vsq-step-number">1</div>
                    <div class="vsq-step-svg">
                        <svg viewBox="0 0 24 24">
                            <rect x="3" y="5" width="18" height="14" rx="1.5"/>
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M3 9h2v6H3"/>
                            <path d="M21 9h-2v6h2"/>
                        </svg>
                    </div>
                </div>
                <h4>Pilih Lapangan</h4>
                <p>Pilih Lapangan yang digunakan di menu booking</p>
            </div>
            <span class="vsq-arrow">›</span>
        </div>

        <div class="vsq-step-wrapper">
            <div class="vsq-step">
                <div class="vsq-step-header">
                    <div class="vsq-step-number">2</div>
                    <div class="vsq-step-svg">
                        <svg viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                            <rect x="7" y="13" width="2" height="2" rx="0.5" fill="currentColor" stroke="none"/>
                            <rect x="12" y="13" width="2" height="2" rx="0.5" fill="currentColor" stroke="none"/>
                            <rect x="17" y="13" width="2" height="2" rx="0.5" fill="currentColor" stroke="none"/>
                            <rect x="7" y="17" width="2" height="2" rx="0.5" fill="currentColor" stroke="none"/>
                            <rect x="12" y="17" width="2" height="2" rx="0.5" fill="currentColor" stroke="none"/>
                        </svg>
                    </div>
                </div>
                <h4>Pilih Jadwal</h4>
                <p>Pilih jadwal lapangan yang tersedia</p>
            </div>
            <span class="vsq-arrow">›</span>
        </div>

        <div class="vsq-step-wrapper">
            <div class="vsq-step">
                <div class="vsq-step-header">
                    <div class="vsq-step-number">3</div>
                    <div class="vsq-step-svg">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/>
                            <rect x="8" y="7" width="8" height="10" rx="1"/>
                            <line x1="9.5" y1="10" x2="14.5" y2="10"/>
                            <line x1="9.5" y1="12" x2="14.5" y2="12"/>
                            <line x1="9.5" y1="14" x2="12.5" y2="14"/>
                        </svg>
                    </div>
                </div>
                <h4>Isi Data Booking</h4>
                <p>Isi data diri anda untuk Melakukan pembookingan</p>
            </div>
            <span class="vsq-arrow">›</span>
        </div>

        <div class="vsq-step-wrapper">
            <div class="vsq-step">
                <div class="vsq-step-header">
                    <div class="vsq-step-number">4</div>
                    <div class="vsq-step-svg">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="8 12 11 15 16 9"/>
                        </svg>
                    </div>
                </div>
                <h4>Konfirmasi</h4>
                <p>Tunggu verifikasi dari admin</p>
            </div>
        </div>

    </div>
</section>

<!-- FAQ -->
<section class="vsq-section">
    <div class="vsq-section-title">
        <h2>Pertanyaan yang sering diajukan</h2>
    </div>
    <div class="vsq-faq-grid">
        
        <div class="vsq-faq-item" onclick="toggleFaq(this)">
            <div class="vsq-faq-header">
                <div class="faq-text">
                    <span class="faq-number">1.</span>
                    <span>Apakah booking dapat dibatalkan?</span>
                </div>
                <span class="faq-arrow">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </span>
            </div>
            <div class="vsq-faq-body">
                Bisa, pada bagian menu "Riwayat pembookingan" pilih lapangan yang kalian booking, lalu klik "Batalkan booking"
            </div>
        </div>

        <div class="vsq-faq-item" onclick="toggleFaq(this)">
            <div class="vsq-faq-header">
                <div class="faq-text">
                    <span class="faq-number">3.</span>
                    <span>Bagaimana cara saya mengetahui booking saya berhasil</span>
                </div>
                <span class="faq-arrow">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </span>
            </div>
            <div class="vsq-faq-body">
                Ketika sudah melakukan pembayaran, status pembookingan akan terlihat pada bagian menu "Riwayat Booking"
            </div>
        </div>

        <div class="vsq-faq-item" onclick="toggleFaq(this)">
            <div class="vsq-faq-header">
                <div class="faq-text">
                    <span class="faq-number">2.</span>
                    <span>Apakah saya bisa mengubah jadwal lapangan</span>
                </div>
                <span class="faq-arrow">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </span>
            </div>
            <div class="vsq-faq-body">
                Bisa, untuk perubahan jadwal langsung saja hubungi kontak kami
            </div>
        </div>

        <div class="vsq-faq-item" onclick="toggleFaq(this)">
            <div class="vsq-faq-header">
                <div class="faq-text">
                    <span class="faq-number">4.</span>
                    <span>Apakah bisa melakukan booking dengan beberapa jadwal sekaligus?</span>
                </div>
                <span class="faq-arrow">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </span>
            </div>
            <div class="vsq-faq-body">
                Bisa, ketik memilih jadwal anda bisa memilih dengan cara beberapa jadwal yang kosong/tersedia
            </div>
        </div>

    </div>
</section>

<!-- FOOTER -->
<footer class="vsq-footer">
    <div class="vsq-footer-content">
        <div class="vsq-footer-brand">
            Vincent'SQ <span>Arena</span>
        </div>

        <div class="vsq-footer-menu">
            <a href="index.php">Beranda</a>
            <a href="booking.php">Booking lapangan</a>
            <a href="riwayat.php">Riwayat Booking</a>
            <a href="makanan.php">Makanan & Minuman</a>
        </div>

        <div class="vsq-footer-contact">
            <div class="vsq-footer-contact-item">
                <svg viewBox="0 0 24 24">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    <path d="M14.05 2a9 9 0 0 1 8 7.94"></path>
                    <path d="M14.05 6A5 5 0 0 1 18 10"></path>
                </svg>
                <span>WA: 0893-3283-4839</span>
            </div>
            <div class="vsq-footer-contact-item">
                <svg viewBox="0 0 24 24">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
                <span>Email: vincenttt3@gmail.com</span>
            </div>
            <div class="vsq-footer-contact-item">
                <svg viewBox="0 0 24 24">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 3.5 5.5 9.5 6.4 10.4.3.3.9.3 1.2 0C13.5 18.5 19 12.5 19 9c0-3.87-3.13-7-7-7z"></path>
                    <circle cx="12" cy="9" r="2.5"></circle>
                </svg>
                <span>Lokasi: Tuparev, Kota Cirebon</span>
            </div>
        </div>
    </div>

    <div class="vsq-footer-bottom">
        @ 2026 Vincent'SQ Arena. All Right Reserved
    </div>
</footer>

<script>
function toggleFaq(element) {
    const isActive = element.classList.contains('active');
    document.querySelectorAll('.vsq-faq-item').forEach(item => {
        item.classList.remove('active');
    });
    if (!isActive) {
        element.classList.add('active');
    }
}
</script>

<!-- ===== NETWORK CSS ===== -->
<link rel="stylesheet" href="/vincent-arena/Frontend/assets/css/network.css">

<!-- ===== NETWORK OVERLAY ===== -->
<div id="network-overlay" class="network-overlay">
    <div class="network-content">
        <div class="network-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="80" height="80">
                <path d="M1 1l22 22"/>
                <path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55"/>
                <path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.39"/>
                <path d="M10.71 5.05A16 16 0 0 1 22.58 9"/>
                <path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88"/>
                <path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
                <line x1="12" y1="20" x2="12.01" y2="20"/>
            </svg>
        </div>
        <h2 class="network-title">Tidak Ada Koneksi Internet</h2>
        <p class="network-message">Periksa koneksi Anda dan coba lagi</p>
        <button onclick="retryConnection()" class="network-btn">Coba Lagi</button>
    </div>
</div>

<!-- ===== TOAST CONTAINER ===== -->
<div id="toast-container" class="toast-container"></div>

<!-- ===== NETWORK JS ===== -->
<script src="/vincent-arena/Frontend/assets/js/network.js"></script>

<!-- ===== VALIDASI BOOKING JS ===== -->
<script src="/vincent-arena/Frontend/assets/js/validasi_booking.js"></script>

</body>
</html>