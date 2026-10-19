<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$is_logged_in = isset($_SESSION['user_id']);
$user_nama = $_SESSION['nama'] ?? 'Mifa'; 

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Vincent\'SQ Arena' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ===== RESET ===== */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #F8FAFC; }

        /* ===== HEADER CONTAINER (1440 x 106) ===== */
        .vsq-header {
            display: flex;
            flex-direction: row;
            align-items: center;
            padding: 26px 44px;
            gap: 24px; /* Jarak antar elemen utama */
            background: #FFFFFF;
            width: 100%;
            height: 106px;
            box-sizing: border-box;
            border-bottom: 1px solid #E2E8F0; 
        }

        /* ===== LOGO ===== */
        .vsq-logo {
            display: flex;
            flex-direction: column;
            justify-content: center;
            width: 115px;
            text-decoration: none;
            flex-shrink: 0;
        }
        .vsq-logo h1 {
            font-weight: 700;
            font-size: 21px;
            line-height: 1;
            letter-spacing: -0.45px;
            color: #2C437B;
            margin: 0;
        }
        .vsq-logo p {
            font-weight: 700;
            font-size: 12px; /* Diperkecil sedikit agar pas */
            line-height: 1;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            color: #FF0000;
            margin: 4px 0 0 0;
        }

        /* ===== MENU TENGAH ===== */
        .vsq-nav-menu {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 32px; /* Jarak antar menu */
            flex: 1; 
        }

        .vsq-nav-menu a {
            font-weight: 500;
            font-size: 16px; /* UKURAN DIPERKECIL agar tidak lipat 2 baris */
            color: #000000;
            text-decoration: none;
            white-space: nowrap; /* Mencegah teks turun ke bawah */
            transition: color 0.2s;
        }

        .vsq-nav-menu a:hover {
            color: #477FEB;
        }

        /* Menu Aktif (Beranda) */
        .vsq-nav-menu a.active {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0 20px;
            background: #131B2E;
            border-radius: 12px;
            color: #FFFFFF;
            font-size: 16px;
            height: 44px; /* Proporsional dengan tinggi header */
        }

        /* ===== SISI KANAN ===== */
        .vsq-nav-right {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 24px; 
            flex-shrink: 0;
        }

        /* Ikon Lonceng */
        .vsq-notif-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            background: transparent;
            border: none;
            padding: 0;
        }
        .vsq-notif-btn svg {
            width: 28px; /* Ukuran disesuaikan */
            height: 28px;
            stroke: #000000;
            fill: none;
            stroke-width: 1.5;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* Profil Box (Border Radius 20px) */
        .vsq-profile-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-sizing: border-box;
            width: 220px; /* Diperkecil dari 254px agar proporsional */
            height: 60px; /* Diperkecil dari 79px */
            background: #FFFFFF;
            border: 1px solid #000000;
            border-radius: 20px;
            padding: 0 10px 0 20px; 
            text-decoration: none;
        }

        /* Teks "Hai Mifa" */
        .vsq-profile-name {
            font-weight: 400;
            font-size: 18px; /* Jauh lebih kecil dari 25px agar mirip gambar */
            color: #000000;
            white-space: nowrap;
        }

        /* Avatar Bulat */
        .vsq-profile-avatar {
            box-sizing: border-box;
            width: 40px; /* Proporsional */
            height: 40px;
            border: 1px solid #000000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
            color: #131B2E;
            background: #FFFFFF;
        }

        /* Tombol Login (jika belum masuk) */
        .vsq-btn-login {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 24px;
            height: 44px;
            background: #131B2E;
            color: #FFFFFF;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
        }

        /* Responsif untuk layar kecil */
        @media (max-width: 1024px) {
            .vsq-header { padding: 15px 20px; height: auto; flex-wrap: wrap; }
            .vsq-nav-menu { display: none; } /* Menu disembunyikan di tablet/mobile */
        }
    </style>
</head>
<body>

<!-- HEADER CONTAINER -->
<header class="vsq-header">
    
    <!-- 1. LOGO -->
    <a href="index.php" class="vsq-logo">
        <h1>Vincent'SQ</h1>
        <p>SPORTS ARENA</p>
    </a>

    <!-- 2. MENU TENGAH -->
    <nav class="vsq-nav-menu">
        <a href="index.php" class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">Beranda</a>
        <a href="booking.php" class="<?= ($current_page == 'booking.php') ? 'active' : '' ?>">Booking Lapangan</a>
        <a href="riwayat.php" class="<?= ($current_page == 'riwayat.php') ? 'active' : '' ?>">Riwayat Booking</a>
        <a href="makanan.php" class="<?= ($current_page == 'makanan.php') ? 'active' : '' ?>">Makanan Minuman</a>
    </nav>

    <!-- 3. SISI KANAN (NOTIF & PROFIL) -->
    <div class="vsq-nav-right">
        
        <!-- Ikon Notifikasi -->
        <button class="vsq-notif-btn" title="Notifikasi">
            <svg viewBox="0 0 24 24">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                <path d="M4 3L2 5"></path>
                <path d="M20 3L22 5"></path>
            </svg>
        </button>

        <?php if ($is_logged_in): ?>
            <!-- Kotak Profil -->
            <a href="logout.php" class="vsq-profile-box" title="Klik untuk Logout">
                <span class="vsq-profile-name">Hai <?= htmlspecialchars($user_nama) ?></span>
                <div class="vsq-profile-avatar">
                    <?= strtoupper(substr($user_nama, 0, 1)) ?>
                </div>
            </a>
        <?php else: ?>
            <a href="login.php" class="vsq-btn-login">Login</a>
        <?php endif; ?>

    </div>

</header>