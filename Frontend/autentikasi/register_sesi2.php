<?php
// register_sesi2.php - Verifikasi Kode (VIEW ONLY - FE)
session_start();

// Ambil email dari session (kalau tidak ada, pakai placeholder)
$email = $_SESSION['register_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Verifikasi Kode - Vincent'SQ Arena</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <script>
    tailwind.config = {
      theme: { extend: {
        fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
        colors: {
          navy: '#0F172A',
          accent: '#DC2626',
          blue: '#3B82F6',
        },
      }},
    };
  </script>
</head>
<body class="font-sans min-h-screen bg-navy">

<!-- Background: Lapangan Futsal + Overlay Gelap -->
<div class="fixed inset-0 z-0">
    <div class="absolute inset-0 bg-cover bg-center"
         style="background-image: url('../assets/uploads/lapangan/hero.jpeg');"></div>
    <div class="absolute inset-0" style="background: rgba(15, 23, 42, 0.85);"></div>
</div>

<!-- Tombol Kembali (di luar card, pojok kiri atas) -->
<a href="register.php"
   class="fixed top-8 left-8 z-20 w-14 h-14 flex items-center justify-center text-white hover:text-slate-300 transition"
   aria-label="Kembali">
    <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 19l-7-7 7-7"/>
    </svg>
</a>

<!-- Card Verifikasi -->
<div class="relative z-10 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl px-8 py-10">

        <!-- Logo -->
        <div class="text-center mb-6">
            <h1 class="text-3xl font-extrabold text-navy tracking-tight">
                Vincent'<span class="text-accent">SQ</span>
            </h1>
            <p class="text-[11px] font-extrabold text-accent tracking-[0.25em] mt-1">SPORTS ARENA</p>
        </div>

        <!-- Ilustrasi: Phone + Email + Person -->
        <div class="flex justify-center mb-6">
            <img 
                src="https://illustrations.popsy.co/blue/surprise.svg" 
                alt="Ilustrasi Verifikasi"
                class="w-48 h-48 object-contain"
                onerror="this.style.display='none'; document.getElementById('svg-fallback').style.display='flex';"
            >
            <!-- Fallback SVG jika gambar eksternal gagal -->
            <div id="svg-fallback" class="hidden w-48 h-48 items-center justify-center bg-blue-50 rounded-2xl">
                <svg viewBox="0 0 200 200" class="w-32 h-32">
                    <rect x="55" y="30" width="70" height="120" rx="10" fill="#1E40AF"/>
                    <rect x="60" y="40" width="60" height="100" rx="5" fill="#3B82F6"/>
                    <rect x="68" y="70" width="50" height="40" rx="6" fill="white"/>
                    <rect x="78" y="82" width="30" height="20" rx="3" fill="none" stroke="#2563EB" stroke-width="2"/>
                    <path d="M78 83 L93 93 L108 83" stroke="#2563EB" stroke-width="2" fill="none"/>
                    <circle cx="72" cy="78" r="9" fill="#DC2626"/>
                    <text x="72" y="82" text-anchor="middle" fill="white" font-size="12" font-weight="bold">!</text>
                </svg>
            </div>
        </div>

        <!-- Judul -->
        <div class="text-center mb-8">
            <p class="text-blue-500 font-bold text-[15px] leading-relaxed">
                Kami telah mengirimkan<br>kode verifikasi
            </p>
        </div>

        <!-- FORM -->
        <form method="POST" action="#" class="space-y-5">

            <!-- Input Email -->
            <div>
                <label class="block text-sm font-semibold text-blue-500 mb-2">Email</label>
                <div class="relative">
                    <!-- Ikon Envelope -->
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-blue-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="2" y="4" width="20" height="16" rx="2"/>
                            <path d="M2 6l10 7 10-7"/>
                        </svg>
                    </div>
                    <input 
                        type="email" 
                        name="email"
                        value="<?= htmlspecialchars($email) ?>"
                        placeholder=""
                        class="w-full pl-12 pr-4 py-3 border-2 border-blue-200 rounded-lg text-slate-700 focus:outline-none focus:border-blue-500 transition"
                    >
                </div>
            </div>

            <!-- Tombol Verifikasi -->
            <button 
                type="submit"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 rounded-lg transition shadow-md"
            >
                Verifikasi
            </button>
        </form>

    </div>
</div>

</body>
</html>