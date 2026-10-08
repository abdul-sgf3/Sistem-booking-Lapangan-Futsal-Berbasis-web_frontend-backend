<?php
// index.php - Halaman Depan User (Sesuai Desain Figma)
session_start();
include '../../Backend/config/database.php';

$title = 'Beranda - Vincent SQ Arena';
include '../includes/header.php';
?>

<style>
    :root {
        --vsq-navy: #0F172A;
        --vsq-blue: #2563EB;
        --vsq-red: #DC2626;
        --vsq-gold: #EAB308;
        --vsq-gray: #64748B;
        --vsq-light: #F8FAFC;
        --vsq-border: #E2E8F0;
    }

    body {
        background-color: #ffffff;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* HERO */
    .vsq-hero {
        position: relative;
        width: 100%;
        min-height: 400px;
        background: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.85)), 
                    url('../assets/uploads/lapangan/hero.jpeg') center/cover no-repeat;
        display: flex;
        align-items: center;
        color: white;
        padding: 60px 20px;
        box-sizing: border-box;
    }

    .vsq-hero-content {
        max-width: 1200px;
        margin: 0 auto;
        width: 100%;
    }

    .vsq-hero h1 {
        font-size: 42px;
        font-weight: 800;
        line-height: 1.2;
        margin: 0 0 16px 0;
        max-width: 600px;
    }

    .vsq-hero h1 span { color: var(--vsq-red); }

    .vsq-hero p {
        font-size: 15px;
        line-height: 1.6;
        color: #CBD5E1;
        max-width: 500px;
        margin: 0;
    }

    .vsq-location-badge {
        position: absolute;
        bottom: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.95);
        color: var(--vsq-navy);
        padding: 8px 16px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* SECTION */
    .vsq-section {
        padding: 50px 20px;
        max-width: 1200px;
        margin: 0 auto;
        box-sizing: border-box;
    }

    .vsq-section-title {
        text-align: center;
        margin-bottom: 36px;
    }

    .vsq-section-title h2 {
        font-size: 32px;
        font-weight: 800;
        color: var(--vsq-navy);
        margin: 0 0 8px 0;
    }

    .vsq-section-title.title-blue h2 {
        color: var(--vsq-blue);
    }

    .vsq-section-title p {
        font-size: 14px;
        color: var(--vsq-gray);
        margin: 0;
    }

    .vsq-section-title.title-blue p {
        color: var(--vsq-blue);
    }

    /* KARTU LAPANGAN */
    .vsq-lapangan-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }

    .vsq-card {
        background: white;
        border: 1px solid var(--vsq-border);
        border-radius: 16px;
        overflow: hidden;
        display: flex;
        flex-direction: row;
        transition: box-shadow 0.3s, transform 0.3s;
        min-height: 240px;
    }

    .vsq-card:hover {
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        transform: translateY(-4px);
    }

    .vsq-card-img {
        position: relative;
        width: 45%;
        flex-shrink: 0;
        background-size: cover;
        background-position: center;
    }

    .vsq-card-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        padding: 5px 14px;
        border-radius: 25px;
        font-size: 12px;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }

    .badge-vip { background: var(--vsq-gold); color: var(--vsq-navy); }
    .badge-regular { background: #F1F5F9; color: var(--vsq-navy); }

    .vsq-card-body {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-width: 0;
    }

    .vsq-card-body h3 {
        font-size: 20px;
        font-weight: 700;
        color: var(--vsq-navy);
        margin: 0 0 14px 0;
    }

    .vsq-fasilitas {
        list-style: none;
        padding: 0;
        margin: 0 0 16px 0;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px 12px;
    }

    .vsq-fasilitas li {
        font-size: 12px;
        color: var(--vsq-gray);
        display: flex;
        align-items: flex-start;
        gap: 6px;
        line-height: 1.4;
    }

    .vsq-fasilitas li::before {
        content: "✓";
        color: var(--vsq-navy);
        font-weight: 900;
        font-size: 13px;
        flex-shrink: 0;
    }

    .vsq-card-footer {
        margin-top: auto;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        padding-top: 14px;
        border-top: 1px solid var(--vsq-border);
    }

    .vsq-btn-booking {
        background: var(--vsq-red);
        color: white;
        padding: 10px 22px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        transition: all 0.2s;
        box-shadow: 0 4px 10px rgba(220, 38, 38, 0.3);
    }
    .vsq-btn-booking:hover { background: #B91C1C; }

    /* CARA BOOKING */
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
        position: relative;
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

    /* FAQ */
    .vsq-faq-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        align-items: start;
    }

    .vsq-faq-item {
        background: white;
        border: 1px solid var(--vsq-border);
        border-radius: 12px;
        padding: 0;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        overflow: hidden;
    }

    .vsq-faq-item:hover { 
        border-color: var(--vsq-blue);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.08);
    }

    .vsq-faq-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 22px;
        gap: 12px;
        user-select: none;
    }

    .vsq-faq-header .faq-text {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        font-size: 14px;
        font-weight: 600;
        color: var(--vsq-navy);
        line-height: 1.4;
    }

    .vsq-faq-header .faq-number {
        color: var(--vsq-navy);
        font-weight: 700;
        flex-shrink: 0;
    }

    .vsq-faq-header .faq-arrow {
        color: var(--vsq-blue);
        font-size: 18px;
        font-weight: 700;
        flex-shrink: 0;
        transition: transform 0.3s ease;
        display: inline-block;
        line-height: 1;
    }

    .vsq-faq-item.active .faq-arrow { transform: rotate(180deg); }

    .vsq-faq-body {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease, padding 0.3s ease;
        padding: 0 22px;
        font-size: 13px;
        color: var(--vsq-gray);
        line-height: 1.6;
        border-top: 0 solid var(--vsq-border);
    }

    .vsq-faq-item.active .vsq-faq-body {
        max-height: 200px;
        padding: 14px 22px 18px;
        border-top: 1px solid var(--vsq-border);
    }

    /* RESPONSIVE */
    @media (max-width: 1023px) {
        .vsq-lapangan-grid { grid-template-columns: 1fr; }
        .vsq-steps { grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .vsq-step-wrapper { flex-direction: column; gap: 10px; }
        .vsq-arrow { display: none; }
    }

    @media (max-width: 767px) {
        .vsq-hero h1 { font-size: 28px; }
        .vsq-hero p { font-size: 13px; }
        .vsq-location-badge { position: static; margin-top: 20px; display: inline-flex; }
        
        .vsq-card { flex-direction: column; min-height: auto; }
        .vsq-card-img { width: 100%; height: 180px; }
        .vsq-fasilitas { grid-template-columns: 1fr; }
        .vsq-card-footer { justify-content: stretch; }
        .vsq-btn-booking { text-align: center; width: 100%; }
        
        .vsq-faq-grid { grid-template-columns: 1fr; }
        .vsq-steps { grid-template-columns: 1fr; gap: 20px; }
        .vsq-section-title h2 { font-size: 24px; }
    }
</style>

<!-- HERO -->
<section class="vsq-hero">
    <div class="vsq-hero-content">
        <h1>Sewa Lapangan Olahraga Favoritmu dengan Sekali <span>Klik</span></h1>
        <p>Vincent SQ Arena kini hadir untuk Futsal, Basket, Badminton, dan Voli dengan fasilitas standar nasional dan sistem konfirmasi realtime.</p>
    </div>
    <div class="vsq-location-badge">
        📍 Majasem, Kota Cirebon, Jawa Barat
    </div>
</section>

<!-- PILIHAN LAPANGAN -->
<section class="vsq-section">
    <div class="vsq-section-title">
        <h2>Pilihan Lapangan Tersedia</h2>
        <p>Pilih jenis lapangan dengan spesifikasi lantai terbaik untuk kenyamanan tim dan performa bertanding maksimal.</p>
    </div>

    <div class="vsq-lapangan-grid">
        <?php
        $query  = "SELECT * FROM lapangan WHERE status = 'aktif' ORDER BY id ASC LIMIT 4";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0):
            while ($lapangan = mysqli_fetch_assoc($result)):
                $foto = (!empty($lapangan['foto']) && file_exists('../assets/uploads/lapangan/' . $lapangan['foto']))
                    ? '../assets/uploads/lapangan/' . rawurlencode($lapangan['foto']) : '';
                
                $badge_text = ($lapangan['harga_per_jam'] >= 200000) ? 'VIP' : 'Regular';
                $badge_class = ($badge_text == 'VIP') ? 'badge-vip' : 'badge-regular';
        ?>
        <article class="vsq-card">
            <div class="vsq-card-img" style="background-image: url('<?= $foto ?>');">
                <span class="vsq-card-badge <?= $badge_class ?>"><?= $badge_text ?></span>
            </div>
            <div class="vsq-card-body">
                <h3><?= htmlspecialchars($lapangan['nama_lapangan']) ?></h3>
                <ul class="vsq-fasilitas">
                    <li>Lapangan berstandar nasional</li>
                    <li>Pencahayaan LED</li>
                    <li>Loker & Ruang Ganti</li>
                    <li>Area Parkir Luas</li>
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
        <?php endif; ?>
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
    <div class="vsq-section-title title-blue">
        <h2>Pertanyaan yang sering diajukan</h2>
    </div>
    <div class="vsq-faq-grid">
        
        <div class="vsq-faq-item" onclick="toggleFaq(this)">
            <div class="vsq-faq-header">
                <div class="faq-text">
                    <span class="faq-number">1.</span>
                    <span>Apakah booking dapat dibatalkan?</span>
                </div>
                <span class="faq-arrow">⌄</span>
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
                <span class="faq-arrow">⌄</span>
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
                <span class="faq-arrow">⌄</span>
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
                <span class="faq-arrow">⌄</span>
            </div>
            <div class="vsq-faq-body">
                Bisa, ketik memilih jadwal anda bisa memilih dengan cara beberapa jadwal yang kosong/tersedia
            </div>
        </div>

    </div>
</section>

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

<?php include '../includes/footer.php'; ?>