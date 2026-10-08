<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$halaman = basename($_SERVER['PHP_SELF']);
$judul   = $judul ?? 'Beranda';
$nama    = $_SESSION['nama'] ?? $_SESSION['username'] ?? 'Pengguna';

// Base URL Project
$base_url = '/Booking-Futsal-main/tsubasa_arena/Frontend';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($judul) ?> - Vincent'SQ Sports Arena</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <script>
    tailwind.config = {
      theme: { extend: {
        fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
        colors: { 
            navy: '#0F172A', 
            accent: '#DC2626',
            gold: '#EAB308',
            blue: '#3B82F6'
        },
      }},
    };
  </script>
</head>
<body class="font-sans text-slate-800 bg-white">

<header class="bg-white border-b border-slate-200 sticky top-0 z-40">
  <div class="max-w-7xl mx-auto px-6 h-24 flex items-center justify-between">

    <!-- KIRI: Logo & Menu Desktop -->
    <div class="flex items-center gap-10">
      <!-- Logo -->
      <a href="<?= $base_url ?>/pages/index.php" class="flex flex-col leading-tight">
        <span class="text-2xl font-extrabold text-navy tracking-tight">
          Vincent'<span class="text-accent">SQ</span>
        </span>
        <span class="text-[10px] font-bold text-accent tracking-[0.15em] mt-0.5">SPORTS ARENA</span>
      </a>

      <!-- Menu Desktop -->
      <nav class="hidden lg:flex items-center gap-1 text-sm">
        <!-- Beranda: SIAP, pakai <a> -->
        <a href="<?= $base_url ?>/pages/index.php" class="px-4 py-2 rounded-lg bg-navy text-white font-bold">Beranda</a>
        
        <!-- Menu belum jadi: pakai <span> (TIDAK BISA DIKLIK) -->
        <span class="px-4 py-2 rounded-lg text-slate-400 opacity-50 cursor-not-allowed select-none">Booking Lapangan</span>
        <span class="px-4 py-2 rounded-lg text-slate-400 opacity-50 cursor-not-allowed select-none">Riwayat Booking</span>
        <span class="px-4 py-2 rounded-lg text-slate-400 opacity-50 cursor-not-allowed select-none">Makanan Minuman</span>
      </nav>
    </div>

    <!-- KANAN: Tombol Login & Register -->
    <div class="flex items-center gap-3">
      <?php if(isset($_SESSION['user_id']) || isset($_SESSION['id_user'])): ?>
          <div class="flex items-center gap-2">
            <span class="hidden sm:inline text-sm font-semibold text-navy">Hai, <?= htmlspecialchars($nama) ?>!</span>
            <span class="w-9 h-9 rounded-full bg-navy text-white grid place-items-center text-sm font-bold">
              <?= strtoupper(substr($nama, 0, 1)) ?>
            </span>
          </div>
          <a href="<?= $base_url ?>/autentikasi/logout.php" class="hidden lg:inline bg-accent text-white px-5 py-2.5 rounded-lg text-sm font-bold hover:bg-red-700 transition">Logout</a>
      <?php else: ?>
          <a href="<?= $base_url ?>/autentikasi/login.php" class="bg-blue text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-blue-600 transition shadow-sm">Login</a>
          <a href="<?= $base_url ?>/autentikasi/register.php" class="bg-accent text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-red-700 transition shadow-sm">Register</a>
      <?php endif; ?>

      <!-- Tombol Menu Mobile -->
      <button id="menuBtn" class="lg:hidden p-2 rounded-lg hover:bg-slate-100" aria-label="Menu">
        <svg class="w-6 h-6 text-navy" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
    </div>

  </div>

  <!-- Menu Mobile -->
  <nav id="mobileMenu" class="hidden lg:hidden border-t bg-white px-6 py-3 space-y-1 text-sm font-semibold">
    <!-- Beranda: SIAP, pakai <a> -->
    <a href="<?= $base_url ?>/pages/index.php" class="block px-4 py-2.5 rounded-lg bg-navy text-white font-bold">Beranda</a>
    
    <!-- Menu belum jadi: pakai <span> (TIDAK BISA DIKLIK) -->
    <span class="block px-4 py-2.5 rounded-lg text-slate-400 opacity-50 cursor-not-allowed select-none">Booking Lapangan</span>
    <span class="block px-4 py-2.5 rounded-lg text-slate-400 opacity-50 cursor-not-allowed select-none">Riwayat Booking</span>
    <span class="block px-4 py-2.5 rounded-lg text-slate-400 opacity-50 cursor-not-allowed select-none">Makanan Minuman</span>
    
    <?php if(isset($_SESSION['user_id']) || isset($_SESSION['id_user'])): ?>
        <a href="<?= $base_url ?>/autentikasi/logout.php" class="block px-4 py-2.5 rounded-lg text-accent">Logout</a>
    <?php else: ?>
        <a href="<?= $base_url ?>/autentikasi/login.php" class="block px-4 py-2.5 rounded-lg text-blue font-bold">Login</a>
        <a href="<?= $base_url ?>/autentikasi/register.php" class="block px-4 py-2.5 rounded-lg text-accent font-bold">Register</a>
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