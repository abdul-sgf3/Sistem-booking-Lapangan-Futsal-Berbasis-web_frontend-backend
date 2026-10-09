<?php
// login.php - Halaman Login User
session_start();

// Jika sudah login, redirect ke beranda
if (isset($_SESSION['user_id'])) {
    header("Location: ../pages/index.php");
    exit();
}

$error = '';
$success = '';

// Cek pesan sukses dari register
if (isset($_SESSION['register_success'])) {
    $success = $_SESSION['register_success'];
    unset($_SESSION['register_success']);
}

// Proses login (untuk sementara validasi FE-only, integrasi BE nanti)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($email) || empty($password)) {
        $error = "Email dan password wajib diisi!";
    } else {
        // ============================================
        // TODO: Integrasi dengan BE endpoint /Backend/API/login.php
        // ============================================
        // Sementara kita pakai validasi placeholder
        // BE akan kirim response JSON: { status, message, data: { user_id, nama } }
        
        // Placeholder validasi (hapus setelah BE siap)
        $error = "Fitur login sedang dalam pengembangan BE.";
        
        // Kode asli setelah BE siap:
        /*
        $response = kirimKeAPI('/Backend/API/login.php', [
            'email' => $email,
            'password' => $password
        ]);
        
        if ($response['status'] === 'success') {
            $_SESSION['user_id'] = $response['data']['user_id'];
            $_SESSION['nama'] = $response['data']['nama'];
            header("Location: ../pages/index.php");
            exit();
        } else {
            $error = $response['message'];
        }
        */
    }
}

$title = 'Login - Vincent SQ Arena';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($title) ?></title>
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

<!-- Background -->
<div class="fixed inset-0 z-0">
    <div class="absolute inset-0 bg-cover bg-center"
         style="background-image: url('../assets/uploads/lapangan/hero.jpeg');"></div>
    <div class="absolute inset-0" style="background: rgba(15, 23, 42, 0.85);"></div>
</div>

<!-- Tombol Kembali -->
<a href="../pages/index.php"
   class="fixed top-8 left-8 z-20 w-14 h-14 flex items-center justify-center text-white hover:text-slate-300 transition"
   aria-label="Kembali">
    <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 19l-7-7 7-7"/>
    </svg>
</a>

<!-- Card Login -->
<div class="relative z-10 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl px-8 py-10">

        <!-- Logo -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-navy tracking-tight">
                Vincent'<span class="text-accent">SQ</span>
            </h1>
            <p class="text-[11px] font-extrabold text-accent tracking-[0.25em] mt-1">SPORTS ARENA</p>
        </div>

        <!-- Judul -->
        <h2 class="text-xl font-bold text-navy text-center mb-8">Masuk ke Akun Anda</h2>

        <!-- Pesan Error -->
        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 text-sm font-semibold p-3 rounded-lg mb-4 text-center border border-red-200">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Pesan Sukses -->
        <?php if ($success): ?>
            <div class="bg-green-50 text-green-600 text-sm font-semibold p-3 rounded-lg mb-4 text-center border border-green-200">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <!-- FORM LOGIN -->
        <form method="POST" class="space-y-5">

            <!-- Input Email -->
            <div>
                <label class="block text-sm font-semibold text-blue-500 mb-2">Email</label>
                <div class="relative">
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-blue-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="2" y="4" width="20" height="16" rx="2"/>
                            <path d="M2 6l10 7 10-7"/>
                        </svg>
                    </div>
                    <input 
                        type="email" 
                        name="email" 
                        placeholder="Masukkan email"
                        required
                        autocomplete="email"
                        class="w-full pl-12 pr-4 py-3 border-2 border-blue-200 rounded-lg text-slate-700 focus:outline-none focus:border-blue-500 transition">
                </div>
            </div>

            <!-- Input Password -->
            <div>
                <label class="block text-sm font-semibold text-blue-500 mb-2">Password</label>
                <div class="relative">
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-blue-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2"/>
                            <path d="M7 11V7a5 5 0 0110 0v4"/>
                        </svg>
                    </div>
                    <input 
                        type="password" 
                        name="password" 
                        id="password"
                        placeholder="Masukkan password"
                        required
                        autocomplete="current-password"
                        class="w-full pl-12 pr-12 py-3 border-2 border-blue-200 rounded-lg text-slate-700 focus:outline-none focus:border-blue-500 transition">
                    
                    <!-- Toggle Show/Hide Password -->
                    <button type="button" id="togglePassword" 
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Lupa Password -->
            <div class="text-right">
                <a href="#" class="text-xs text-blue-500 hover:underline font-semibold">Lupa Password?</a>
            </div>

            <!-- Tombol Login -->
            <button type="submit"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 rounded-lg transition shadow-md">
                Login
            </button>
        </form>

        <!-- Divider -->
        <div class="flex items-center gap-3 my-6">
            <div class="flex-1 h-px bg-slate-200"></div>
            <span class="text-xs text-slate-400 font-semibold">ATAU</span>
            <div class="flex-1 h-px bg-slate-200"></div>
        </div>

        <!-- Link Register -->
        <p class="text-center text-sm text-slate-500">
            Belum punya akun? 
            <a href="register.php" class="text-accent font-bold hover:underline">Daftar di sini</a>
        </p>

    </div>
</div>

<script>
// ===== Toggle Show/Hide Password =====
const toggleBtn = document.getElementById('togglePassword');
const passwordInput = document.getElementById('password');
const eyeIcon = document.getElementById('eyeIcon');

toggleBtn.addEventListener('click', function() {
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        // Ikon mata dengan garis (hidden)
        eyeIcon.innerHTML = `
            <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
            <line x1="1" y1="1" x2="23" y2="23"/>
        `;
    } else {
        passwordInput.type = 'password';
        // Ikon mata normal
        eyeIcon.innerHTML = `
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>
        `;
    }
});
</script>

</body>
</html>