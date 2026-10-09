<?php
// register.php - Halaman registrasi USER (Sesuai Figma)
session_start();
include '../../Backend/config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = md5($_POST['password']);
    $confirm_password = md5($_POST['confirm_password']);
    
    if ($password != $confirm_password) {
        $error = "Password dan konfirmasi password tidak sama!";
    } else {
        $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check_email) > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            $username = explode('@', $email)[0];
            $nama = ucfirst($username);
            $no_hp = '';
            
            $cek_username = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
            if (mysqli_num_rows($cek_username) > 0) {
                $username = $username . rand(10, 99);
            }
            
            $query = "INSERT INTO users (nama, username, email, no_hp, password, role) 
                      VALUES ('$nama', '$username', '$email', '$no_hp', '$password', 'user')";
            
            if (mysqli_query($conn, $query)) {
                $success = "Registrasi berhasil! Silakan login.";
            } else {
                $error = "Registrasi gagal: " . mysqli_error($conn);
            }
        }
    }
}

$title = 'Register - Vincent\'SQ Arena';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <style>
        /* Warna Custom */
        .btn-primary {
            background-color: #3B82F6;
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            background-color: #2563EB;
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
        }
        
        .input-field {
            background-color: #DBEAFE;  /* Biru muda */
            border: 1.5px solid #93C5FD; /* Biru medium */
            transition: all 0.2s ease;
        }
        .input-field:focus {
            background-color: #FFFFFF;
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
            outline: none;
        }
        
        .label-field {
            color: #3B82F6;
            font-weight: 500;
        }
    </style>
    <script>
        tailwind.config = {
            theme: { extend: {
                fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                colors: {
                    navy: '#0F172A',
                    accent: '#DC2626',
                    blue: '#3B82F6'
                },
            }},
        };
    </script>
</head>
<body class="font-sans">

    <!-- BACKGROUND IMAGE -->
    <div class="fixed inset-0 bg-cover bg-center" 
         style="background-image: url('../assets/uploads/lapangan/hero.jpeg');">
        <div class="absolute inset-0 bg-black/65"></div>
    </div>

    <!-- TOMBOL BACK -->
    <a href="../pages/index.php" 
       class="fixed top-6 left-6 z-20 text-white hover:text-gray-300 transition p-2 rounded-full hover:bg-white/10"
       aria-label="Kembali">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
    </a>

    <!-- CARD REGISTER -->
    <div class="relative z-10 min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8 sm:p-10">

            <!-- LOGO -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-extrabold text-navy tracking-tight">
                    Vincent'<span class="text-accent">SQ</span>
                </h1>
                <p class="text-xs font-bold text-accent tracking-[0.2em] mt-1">SPORTS ARENA</p>
            </div>

            <!-- Notifikasi -->
            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg mb-5 text-center">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg mb-5 text-center">
                    <?= $success ?><br>
                    <a href="login.php" class="text-accent font-bold underline mt-2 inline-block">Login di sini</a>
                </div>
            <?php endif; ?>

            <!-- FORM -->
            <form method="POST" class="space-y-5">

                <!-- Email -->
                <div>
                    <label class="block text-sm label-field mb-2">
                        Email<span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" required
                           class="w-full px-4 py-3 input-field rounded-xl text-sm text-slate-700">
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm label-field mb-2">
                        Password<span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 input-field rounded-xl text-sm text-slate-700">
                </div>

                <!-- Konfirmasi Password -->
                <div>
                    <label class="block text-sm label-field mb-2">
                        Konfirmasi Password<span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="confirm_password" required
                           class="w-full px-4 py-3 input-field rounded-xl text-sm text-slate-700">
                </div>

                <!-- Tombol Submit -->
                <div class="pt-4">
                    <button type="submit"
                            class="w-full btn-primary text-white font-semibold py-3.5 rounded-full text-base shadow-lg shadow-blue-500/25">
                        Daftar Akun
                    </button>
                </div>

            </form>

            <!-- Link ke Login -->
            <p class="text-center text-sm text-slate-500 mt-6">
                Sudah punya akun?
                <a href="login.php" class="text-blue-600 font-bold hover:underline">Login di sini</a>
            </p>

        </div>
    </div>

</body>
</html>