<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$halaman = basename($_SERVER['PHP_SELF']);
$judul   = $judul ?? 'Beranda';
$nama    = $_SESSION['nama'] ?? $_SESSION['username'] ?? 'Pengguna';

function navClass($file, $halaman, $mobile = false) {
    $base = $mobile ? 'block px-4 py-2.5 rounded-lg' : 'px-4 py-2 rounded-lg';
    return $base . ($halaman === $file ? ' bg-navy text-white' : ' text-slate-700 hover:bg-slate-100');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($judul) ?> - Vincent Arena</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <script>
    tailwind.config = {
      theme: { extend: {
        fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
        colors: { navy: '#0B2A52', accent: '#E8394A', ok: '#16A34A' },
      }},
    };
  </script>
</head>
<body class="font-sans text-slate-800 bg-white">

<header class="bg-white border-b border-slate-200 sticky top-0 z-40">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">

    <div class="flex items-center gap-3 sm:gap-6">
      <?php if ($halaman !== 'index.php'): ?>
      <button onclick="history.back()" class="lg:hidden -ml-1 p-2 rounded-lg hover:bg-slate-100" aria-label="Kembali">
        <svg class="w-5 h-5 text-navy" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
      </button>
      <?php endif; ?>

      <a href="index.php" class="flex flex-col leading-none">
        <span class="flex items-center gap-1 text-xl font-extrabold text-navy">
          <svg class="w-5 h-5 text-orange-500" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2 4 14h6l-1 8 9-12h-6l1-8z"/></svg>
          Vincent
        </span>
        <span class="text-[9px] font-bold tracking-[0.2em] text-accent ml-6">ARENA</span>
      </a>

      <nav class="hidden lg:flex items-center gap-1 text-sm font-semibold">
        <a href="index.php" class="<?= navClass('index.php', $halaman) ?>">Beranda</a>
        <a href="lapangan.php" class="<?= navClass('lapangan.php', $halaman) ?>">Booking Lapangan</a>
        <a href="riwayat_booking.php" class="<?= navClass('riwayat_booking.php', $halaman) ?>">Riwayat Booking</a>
        <a href="makanan.php" class="<?= navClass('makanan.php', $halaman) ?>">Makanan Minuman</a>
      </nav>
    </div>

    <div class="flex items-center gap-3">
      <button class="relative p-2 rounded-full hover:bg-slate-100" aria-label="Notifikasi">
        <svg class="w-5 h-5 text-navy" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 1 1-6 0"/></svg>
        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-accent rounded-full ring-2 ring-white"></span>
      </button>

      <div class="flex items-center gap-2 border border-slate-200 rounded-full pl-3 pr-1 py-1">
        <span class="hidden sm:inline text-sm font-semibold text-navy">Hai, <?= htmlspecialchars($nama) ?>!</span>
        <span class="w-8 h-8 rounded-full bg-navy text-white grid place-items-center text-sm font-bold">
          <?= strtoupper(substr($nama, 0, 1)) ?>
        </span>
      </div>

      <a href="logout.php" class="hidden lg:inline text-red-500 font-semibold hover:underline text-sm">Logout</a>

      <button id="menuBtn" class="lg:hidden p-2 rounded-lg hover:bg-slate-100" aria-label="Menu">
        <svg class="w-6 h-6 text-navy" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
    </div>

  </div>

  <nav id="mobileMenu" class="hidden lg:hidden border-t bg-white px-4 py-3 space-y-1 text-sm font-semibold">
    <a href="index.php" class="<?= navClass('index.php', $halaman, true) ?>">Beranda</a>
    <a href="lapangan.php" class="<?= navClass('lapangan.php', $halaman, true) ?>">Booking Lapangan</a>
    <a href="riwayat_booking.php" class="<?= navClass('riwayat_booking.php', $halaman, true) ?>">Riwayat Booking</a>
    <a href="makanan.php" class="<?= navClass('makanan.php', $halaman, true) ?>">Makanan Minuman</a>
    <a href="logout.php" class="block px-4 py-2.5 rounded-lg text-accent">Logout</a>
  </nav>
</header>