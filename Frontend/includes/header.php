<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$halaman = basename($_SERVER['PHP_SELF']);
$judul   = $judul ?? 'Beranda';
$nama    = $_SESSION['nama'] ?? $_SESSION['username'] ?? 'Pengguna';

// Base URL Project
$base_url = '/Booking-Futsal-main/tsubasa_arena/Frontend';

function navClass($file, $halaman, $mobile = false) {
    $base = $mobile ? 'block px-4 py-2.5 rounded-lg transition-all duration-200' : 'px-4 py-2 rounded-lg transition-all duration-200';
    // Menu aktif: background biru navy + teks putih
    return $base . ($halaman === $file 
        ? ' bg-navy text-white font-bold shadow-sm' 
        : ' text-slate-700 hover:bg-slate-100 hover:text-navy font-medium');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($judul) ?> - Vincent SQ Arena</title>
  <title><?= htmlspecialchars($judul) ?> - Sport Arena</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <script>
    tailwind.config = {
      theme: { extend: {
        fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
        colors: { 
            navy: '#0F172A', 
            accent: '#DC2626',
            gold: '#EAB308' 
        },
      }},
    };
  </script>
</head>
<body class="font-sans text-slate-800 bg-white">

<header class="bg-white border-b border-slate-200 sticky top-0 z-40">
  <div class="max-w-5xl mx-auto px-6 h-20 flex items-center justify-between">

    <!-- KIRI: Logo & Menu Desktop -->
    <div class="flex items-center gap-8">
      <!-- Logo -->
      <a href="<?= $base_url ?>/pages/index.php" class="text-2xl font-extrabold text-navy tracking-tight">
        Vincent<span class="text-accent">SQ</span>
      </a>

      <!-- Menu Desktop -->
      <nav class="hidden lg:flex items-center gap-2 text-sm">
        <a href="<?= $base_url ?>/pages/index.php" class="<?= navClass('index.php', $halaman) ?>">Beranda</a>
        <a href="<?= $base_url ?>/pages/lapangan.php" class="<?= navClass('lapangan.php', $halaman) ?>">Booking Lapangan</a>
        <a href="<?= $base_url ?>/pages/riwayat_booking.php" class="<?= navClass('riwayat_booking.php', $halaman) ?>">Riwayat Booking</a>
        <a href="<?= $base_url ?>/pages/makanan.php" class="<?= navClass('makanan.php', $halaman) ?>">Makanan Minuman</a>
      </nav>
    </div>

    <!-- KANAN: Notifikasi & Tombol Login -->
    <div class="flex items-center gap-4">
      <button class="relative p-2 rounded-full hover:bg-slate-100" aria-label="Notifikasi">
        <svg class="w-5 h-5 text-navy" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 1 1-6 0"/></svg>
        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-accent rounded-full ring-2 ring-white"></span>
      </button>

      <?php if(isset($_SESSION['user_id']) || isset($_SESSION['id_user'])): ?>
          <div class="flex items-center gap-2">
            <span class="hidden sm:inline text-sm font-semibold text-navy">Hai, <?= htmlspecialchars($nama) ?>!</span>
            <span class="w-9 h-9 rounded-full bg-navy text-white grid place-items-center text-sm font-bold">
              <?= strtoupper(substr($nama, 0, 1)) ?>
            </span>
          </div>
          <a href="<?= $base_url ?>/autentikasi/logout.php" class="hidden lg:inline text-accent font-semibold hover:underline text-sm">Logout</a>
      <?php else: ?>
          <a href="<?= $base_url ?>/autentikasi/login.php" class="bg-accent text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-red-700 transition shadow-sm">Login</a>
      <?php endif; ?>

      <button id="menuBtn" class="lg:hidden p-2 rounded-lg hover:bg-slate-100" aria-label="Menu">
        <svg class="w-6 h-6 text-navy" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
    </div>

  </div>

  <!-- Menu Mobile -->
  <nav id="mobileMenu" class="hidden lg:hidden border-t bg-white px-6 py-3 space-y-1 text-sm font-semibold">
    <a href="<?= $base_url ?>/pages/index.php" class="<?= navClass('index.php', $halaman, true) ?>">Beranda</a>
    <a href="<?= $base_url ?>/pages/lapangan.php" class="<?= navClass('lapangan.php', $halaman, true) ?>">Booking Lapangan</a>
    <a href="<?= $base_url ?>/pages/riwayat_booking.php" class="<?= navClass('riwayat_booking.php', $halaman, true) ?>">Riwayat Booking</a>
    <a href="<?= $base_url ?>/pages/makanan.php" class="<?= navClass('makanan.php', $halaman, true) ?>">Makanan Minuman</a>
    
    <?php if(isset($_SESSION['user_id']) || isset($_SESSION['id_user'])): ?>
        <a href="<?= $base_url ?>/autentikasi/logout.php" class="block px-4 py-2.5 rounded-lg text-accent">Logout</a>
    <?php else: ?>
        <a href="<?= $base_url ?>/autentikasi/login.php" class="block px-4 py-2.5 rounded-lg text-accent">Login</a>
    <?php endif; ?>
  </nav>
</header>

<script>
  const menuBtn = document.getElementById('menuBtn');
  const mobileMenu = document.getElementById('mobileMenu');
  if(menuBtn && mobileMenu) {
      menuBtn.addEventListener('click', () => {
          mobileMenu.classList.toggle('hidden');
      });
  }
</script>