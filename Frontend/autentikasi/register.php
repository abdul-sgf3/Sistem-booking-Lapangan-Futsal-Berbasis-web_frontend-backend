<?php
// register.php - Halaman Register User
session_start();

// Jika user sudah login, redirect ke halaman utama
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Include koneksi database (sesuaikan path jika berbeda)
include '../../Backend/config/database.php';

$error = '';
$success = '';

// Proses Form Registrasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $konfirmasi = $_POST['konfirmasi_password'];

    // Validasi
    if (empty($email) || empty($password) || empty($konfirmasi)) {
        $error = "Semua field wajib diisi!";
    } elseif ($password !== $konfirmasi) {
        $error = "Password dan Konfirmasi Password tidak cocok!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } else {
        // Cek apakah email sudah terdaftar
        $cek_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
        if (mysqli_num_rows($cek_email) > 0) {
            $error = "Email sudah terdaftar! Silakan gunakan email lain.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Simpan ke database
            $query = "INSERT INTO users (email, password, role) VALUES ('$email', '$hashed_password', 'user')";
            
            if (mysqli_query($conn, $query)) {
                $success = "Akun berhasil dibuat! Silakan login.";
                header("refresh:2;url=login.php");
            } else {
                $error = "Gagal mendaftar: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Vincent'SQ Arena</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --vsq-title-blue: #2C437B; /* Warna Biru Navy untuk Vincent'SQ */
            --vsq-red: #DC2626;        /* Untuk "SPORTS ARENA" dan tanda bintang (*) */
            --vsq-black: #0F172A;      /* Untuk Label Email, Password, dll (Hitam) */
            --vsq-gray-text: #64748B;  /* Untuk teks "Sudah memiliki akun?" */
            --vsq-input-bg: #DBEAFE;   /* Background input biru muda */
            --vsq-input-border: #BFDBFE;
            --vsq-placeholder: #94A3B8;/* Warna placeholder abu-abu */
            --vsq-btn-blue: #2563EB;   /* Warna tombol dan link login */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: 
                linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.85)),
                url('../assets/uploads/lapangan/hero.jpeg') center/cover no-repeat;
            position: relative;
            padding: 20px;
        }

        /* Tombol Kembali */
        .vsq-back-btn {
            position: absolute;
            top: 30px;
            left: 30px;
            color: #FFFFFF;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            transition: background 0.3s, transform 0.3s;
            z-index: 10;
        }

        .vsq-back-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(-4px);
        }

        .vsq-back-btn svg {
            width: 28px;
            height: 28px;
            stroke: #FFFFFF;
            stroke-width: 2.5;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* Kartu Form Register */
        .vsq-register-card {
            background: #FFFFFF;
            width: 100%;
            max-width: 440px;
            border-radius: 24px;
            padding: 50px 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            z-index: 5;
        }

        /* Logo dan Judul */
        .vsq-logo {
            text-align: center;
            margin-bottom: 40px;
        }

        .vsq-logo h1 {
            font-size: 32px;
            font-weight: 800; /* Judul tetap Bold */
            color: var(--vsq-title-blue); /* #2C437B */
            letter-spacing: -0.5px;
            margin: 0;
            line-height: 1.2;
        }

        .vsq-logo p {
            font-size: 14px;
            font-weight: 700;
            color: var(--vsq-red); /* Merah */
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        /* Form Group */
        .vsq-form-group {
            margin-bottom: 24px;
        }

        /* PERBAIKAN: Label tidak boleh Bold (Menggunakan 400) */
        .vsq-form-group label {
            display: block;
            font-size: 15px;
            font-weight: 400; /* <--- Diubah menjadi Normal (tidak tebal) */
            color: var(--vsq-black); 
            margin-bottom: 8px;
        }

        /* Tanda Bintang tetap Merah */
        .vsq-form-group label span {
            color: var(--vsq-red); 
        }

        .vsq-form-control {
            width: 100%;
            padding: 14px 18px;
            font-size: 15px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--vsq-black);
            background-color: var(--vsq-input-bg);
            border: 1.5px solid var(--vsq-input-border);
            border-radius: 12px;
            outline: none;
            transition: all 0.3s ease;
        }

        /* PERBAIKAN: Placeholder juga tidak boleh Bold */
        .vsq-form-control::placeholder {
            color: var(--vsq-placeholder); 
            font-weight: 400; /* <--- Diubah menjadi Normal */
        }

        .vsq-form-control:focus {
            background-color: #FFFFFF;
            border-color: var(--vsq-btn-blue);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
        }

        /* Pesan Error / Sukses */
        .vsq-alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 24px;
            text-align: center;
        }

        .vsq-alert-error {
            background-color: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FECACA;
        }

        .vsq-alert-success {
            background-color: #DCFCE7;
            color: #166534;
            border: 1px solid #BBF7D0;
        }

        /* Tombol Daftar */
        .vsq-btn-submit {
            width: 100%;
            padding: 16px;
            font-size: 16px;
            font-weight: 700; /* Tombol tetap Bold */
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #FFFFFF; 
            background-color: var(--vsq-btn-blue);
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            margin-top: 10px;
        }

        .vsq-btn-submit:hover {
            background-color: #1D4ED8;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4);
        }

        .vsq-btn-submit:active {
            transform: translateY(0);
        }

        /* Link Login */
        .vsq-login-link {
            text-align: center;
            margin-top: 28px;
            font-size: 14px;
            color: var(--vsq-gray-text); 
            font-weight: 500;
        }

        .vsq-login-link a {
            color: var(--vsq-btn-blue); 
            text-decoration: none;
            font-weight: 700;
            transition: color 0.2s;
        }

        .vsq-login-link a:hover {
            color: #1D4ED8;
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .vsq-register-card {
                padding: 40px 24px;
                border-radius: 20px;
            }
            .vsq-logo h1 {
                font-size: 28px;
            }
            .vsq-back-btn {
                top: 20px;
                left: 20px;
                width: 40px;
                height: 40px;
            }
            .vsq-back-btn svg {
                width: 24px;
                height: 24px;
            }
        }
    </style>
</head>
<body>

    <!-- Tombol Kembali -->
    <a href="index.php" class="vsq-back-btn" title="Kembali ke Beranda">
        <svg viewBox="0 0 24 24">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
    </a>

    <!-- Kartu Register -->
    <div class="vsq-register-card">
        
        <!-- Logo -->
        <div class="vsq-logo">
            <h1>Vincent'SQ</h1>
            <p>Sports Arena</p>
        </div>

        <!-- Pesan Error / Sukses -->
        <?php if (!empty($error)): ?>
            <div class="vsq-alert vsq-alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="vsq-alert vsq-alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <!-- Form Registrasi -->
        <form action="" method="POST">
            
            <div class="vsq-form-group">
                <label for="email">Email<span>*</span></label>
                <input type="email" id="email" name="email" class="vsq-form-control" placeholder="Masukkan email..." required autocomplete="email">
            </div>

            <div class="vsq-form-group">
                <label for="password">Password<span>*</span></label>
                <input type="password" id="password" name="password" class="vsq-form-control" placeholder="Masukkan Password..." required autocomplete="new-password">
            </div>

            <div class="vsq-form-group">
                <label for="konfirmasi_password">Konfirmasi Password<span>*</span></label>
                <input type="password" id="konfirmasi_password" name="konfirmasi_password" class="vsq-form-control" placeholder="Masukkan Password..." required autocomplete="new-password">
            </div>

            <button type="submit" class="vsq-btn-submit">Daftar Akun</button>

        </form>

        <!-- Link Login -->
        <div class="vsq-login-link">
            Sudah memiliki akun? <a href="login.php">Login di sini</a>
        </div>

    </div>

</body>
</html>