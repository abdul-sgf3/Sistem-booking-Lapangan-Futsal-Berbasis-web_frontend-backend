<?php
// register.php - Halaman Register User
session_start();

// ============================================================
// KONEKSI DATABASE
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

if (!isset($conn) || !$conn) {
    $conn = @mysqli_connect('localhost', 'root', '', 'vincent_sqdb');
    if ($conn) {
        mysqli_set_charset($conn, 'utf8mb4');
    }
}

// ============================================================
// PATH BACKGROUND HERO
// ============================================================
$hero_url = '/vincent-arena/Frontend/assets/uploads/lapangan/hero.jpeg';

$error = '';
$success = '';

// Proses Form Registrasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $konfirmasi = $_POST['konfirmasi_password'];

    if (empty($email) || empty($password) || empty($konfirmasi)) {
        $error = "Semua field wajib diisi!";
    } elseif ($password !== $konfirmasi) {
        $error = "Password dan Konfirmasi Password tidak cocok!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } else {
        $cek_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
        if ($cek_email && mysqli_num_rows($cek_email) > 0) {
            $error = "Email sudah terdaftar! Silakan gunakan email lain.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
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
            --vsq-title-blue: #2C437B;
            --vsq-red: #DC2626;
            --vsq-black: #0F172A;
            --vsq-gray-text: #64748B;
            --vsq-btn-blue: #2563EB;
            --vsq-input-border: #CBD5E1;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: 
                linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.85)),
                url('<?= htmlspecialchars($hero_url) ?>') center/cover no-repeat fixed;
            background-color: #0F172A;
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
        .vsq-back-btn:hover { background: rgba(255, 255, 255, 0.2); transform: translateX(-4px); }
        .vsq-back-btn svg {
            width: 28px; height: 28px;
            stroke: #FFFFFF; stroke-width: 2.5;
            fill: none; stroke-linecap: round; stroke-linejoin: round;
        }

        /* Kartu Register */
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

        .vsq-logo { text-align: center; margin-bottom: 40px; }
        .vsq-logo h1 {
            font-size: 32px;
            font-weight: 800;
            color: var(--vsq-title-blue);
            letter-spacing: -0.5px;
            margin: 0;
            line-height: 1.2;
        }
        .vsq-logo p {
            font-size: 14px;
            font-weight: 700;
            color: var(--vsq-red);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .vsq-form-group { margin-bottom: 24px; }

        .vsq-form-group label {
            display: block;
            font-size: 15px;
            font-weight: 400;
            color: var(--vsq-black);
            margin-bottom: 8px;
        }

        .vsq-form-group label span { color: var(--vsq-red); }

        .vsq-input-wrapper { position: relative; width: 100%; }

        .vsq-form-control {
            width: 100%;
            padding: 14px 18px;
            padding-right: 50px;
            font-size: 15px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--vsq-black);
            background-color: #DBEAFE;
            border: 1.5px solid #BFDBFE;
            border-radius: 12px;
            outline: none;
            transition: all 0.3s ease;
        }

        .vsq-form-control::placeholder { color: #94A3B8; font-weight: 400; }

        .vsq-form-control:focus {
            background-color: #FFFFFF;
            border-color: var(--vsq-btn-blue);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
        }

        /* Ikon Mata */
        .vsq-toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748B;
            transition: color 0.2s;
            background: transparent;
            border: none;
            padding: 0;
            z-index: 2;
            width: 24px;
            height: 24px;
        }
        .vsq-toggle-password:hover { color: var(--vsq-btn-blue); }
        .vsq-toggle-password svg {
            width: 22px; height: 22px;
            stroke: currentColor; stroke-width: 2;
            fill: none; stroke-linecap: round; stroke-linejoin: round;
        }

        .vsq-alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 24px;
            text-align: center;
        }
        .vsq-alert-error { background-color: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }
        .vsq-alert-success { background-color: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; }

        .vsq-login-link-top {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 24px;
            font-size: 14px;
            color: var(--vsq-gray-text);
            font-weight: 500;
        }
        .vsq-login-link-top a {
            color: var(--vsq-btn-blue);
            text-decoration: none;
            font-weight: 400;
            transition: color 0.2s;
        }
        .vsq-login-link-top a:hover { color: #1D4ED8; text-decoration: underline; }

        .vsq-btn-submit {
            display: block;
            width: 100%;
            max-width: 320px;
            margin: 0 auto;
            padding: 12px 20px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #FFFFFF;
            background-color: var(--vsq-btn-blue);
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }
        .vsq-btn-submit:hover {
            background-color: #1D4ED8;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4);
        }
        .vsq-btn-submit:active { transform: translateY(0); }

        @media (max-width: 480px) {
            .vsq-register-card { padding: 40px 24px; border-radius: 20px; }
            .vsq-logo h1 { font-size: 28px; }
            .vsq-back-btn { top: 20px; left: 20px; width: 40px; height: 40px; }
            .vsq-back-btn svg { width: 24px; height: 24px; }
            .vsq-btn-submit { max-width: 100%; }
        }
    </style>
</head>
<body>

    <a href="../pages/index.php" class="vsq-back-btn" title="Kembali ke Beranda">
        <svg viewBox="0 0 24 24">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
    </a>

    <div class="vsq-register-card">
        
        <div class="vsq-logo">
            <h1>Vincent'SQ</h1>
            <p>Sports Arena</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="vsq-alert vsq-alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="vsq-alert vsq-alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            
            <div class="vsq-form-group">
                <label for="email">Email<span>*</span></label>
                <input type="email" id="email" name="email" class="vsq-form-control" placeholder="Masukkan email..." required autocomplete="email">
            </div>

            <div class="vsq-form-group">
                <label for="password">Password<span>*</span></label>
                <div class="vsq-input-wrapper">
                    <input type="password" id="password" name="password" class="vsq-form-control" placeholder="Masukkan Password..." required autocomplete="new-password">
                    <button type="button" class="vsq-toggle-password" onclick="togglePassword('password', this)">
                        <!-- Ikon default: mata tertutup (eyelash) -->
                        <svg viewBox="0 0 24 24" class="icon-eye-closed">
                            <path d="M3 10 Q12 18 21 10" />
                            <line x1="3" y1="10" x2="2" y2="13" />
                            <line x1="7" y1="13" x2="5.5" y2="16" />
                            <line x1="12" y1="15" x2="12" y2="18" />
                            <line x1="17" y1="13" x2="18.5" y2="16" />
                            <line x1="21" y1="10" x2="22" y2="13" />
                        </svg>
                        <!-- Ikon alternate: mata terbuka (disembunyikan default) -->
                        <svg viewBox="0 0 24 24" class="icon-eye-open" style="display:none;">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="vsq-form-group">
                <label for="konfirmasi_password">Konfirmasi Password<span>*</span></label>
                <div class="vsq-input-wrapper">
                    <input type="password" id="konfirmasi_password" name="konfirmasi_password" class="vsq-form-control" placeholder="Masukkan Password..." required autocomplete="new-password">
                    <button type="button" class="vsq-toggle-password" onclick="togglePassword('konfirmasi_password', this)">
                        <!-- Ikon default: mata tertutup (eyelash) -->
                        <svg viewBox="0 0 24 24" class="icon-eye-closed">
                            <path d="M3 10 Q12 18 21 10" />
                            <line x1="3" y1="10" x2="2" y2="13" />
                            <line x1="7" y1="13" x2="5.5" y2="16" />
                            <line x1="12" y1="15" x2="12" y2="18" />
                            <line x1="17" y1="13" x2="18.5" y2="16" />
                            <line x1="21" y1="10" x2="22" y2="13" />
                        </svg>
                        <!-- Ikon alternate: mata terbuka (disembunyikan default) -->
                        <svg viewBox="0 0 24 24" class="icon-eye-open" style="display:none;">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="vsq-login-link-top">
                Sudah memiliki akun? <a href="login.php">Login di sini</a>
            </div>

            <button type="submit" class="vsq-btn-submit">Daftar Akun</button>

        </form>

    </div>

    <script>
        function togglePassword(inputId, iconElement) {
            const input = document.getElementById(inputId);
            const iconClosed = iconElement.querySelector('.icon-eye-closed');
            const iconOpen = iconElement.querySelector('.icon-eye-open');
            
            if (input.type === 'password') {
                // Tampilkan password
                input.type = 'text';
                iconClosed.style.display = 'none';
                iconOpen.style.display = 'block';
            } else {
                // Sembunyikan password
                input.type = 'password';
                iconClosed.style.display = 'block';
                iconOpen.style.display = 'none';
            }
        }
    </script>

</body>
</html>