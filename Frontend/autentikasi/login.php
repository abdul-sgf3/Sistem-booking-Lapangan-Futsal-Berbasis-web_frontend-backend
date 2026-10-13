<?php
// login.php - Halaman Login User
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

$hero_url = '/vincent-arena/Frontend/assets/uploads/lapangan/hero.jpeg';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Email dan Password wajib diisi!";
    } else {
        $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'] ?? 'user';
                header("Location: ../pages/index.php");
                exit;
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "Email tidak terdaftar!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Vincent'SQ Arena</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --vsq-title-blue: #2C437B;
            --vsq-red: #DC2626;
            --vsq-black: #000000;
            --vsq-btn-blue: #2563EB;
            --vsq-input-bg: #DBEAFE;
            --vsq-input-border: #BFDBFE;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body { height: 100%; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .vsq-login-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        .vsq-login-left {
            flex: 1;
            background: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 30px;
        }

        .vsq-login-card {
            width: 100%;
            max-width: 400px;
        }

        .vsq-logo {
            text-align: center;
            margin-bottom: 8px;
        }

        .vsq-logo h1 {
            font-size: 28px;
            font-weight: 800;
            color: var(--vsq-title-blue);
            letter-spacing: -0.5px;
            margin: 0;
            line-height: 1.2;
        }

        .vsq-logo p {
            font-size: 13px;
            font-weight: 800;
            color: var(--vsq-red);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .vsq-welcome {
            text-align: center;
            font-size: 15px;
            font-weight: 700;
            color: #000000;
            margin: 30px 0 25px 0;
        }

        .vsq-form-group { margin-bottom: 16px; }

        .vsq-form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--vsq-black);
            margin-bottom: 6px;
        }

        .vsq-form-group label span { color: var(--vsq-red); }

        .vsq-input-wrapper { position: relative; width: 100%; }

        .vsq-form-control {
            width: 100%;
            padding: 11px 16px;
            padding-right: 45px;
            font-size: 13px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--vsq-black);
            background-color: var(--vsq-input-bg);
            border: 1.5px solid var(--vsq-input-border);
            border-radius: 8px;
            outline: none;
            transition: all 0.3s ease;
        }

        .vsq-form-control::placeholder { color: #94A3B8; }

        .vsq-form-control:focus {
            background-color: #FFFFFF;
            border-color: var(--vsq-btn-blue);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
        }

        .vsq-toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748B;
            background: transparent;
            border: none;
            padding: 0;
            z-index: 2;
            width: 22px;
            height: 22px;
        }
        .vsq-toggle-password:hover { color: var(--vsq-btn-blue); }
        .vsq-toggle-password svg {
            width: 20px; height: 20px;
            stroke: currentColor; stroke-width: 2;
            fill: none; stroke-linecap: round; stroke-linejoin: round;
        }

        .vsq-forgot-link {
            text-align: right;
            margin-top: 8px;
            margin-bottom: 22px;
        }
        .vsq-forgot-link a {
            font-size: 12px;
            color: #2563EB;
            text-decoration: none;
            font-weight: 500;
        }
        .vsq-forgot-link a:hover { text-decoration: underline; }

        /* ============================================================
           reCAPTCHA BOX - Sesuai Gambar Referensi
           ============================================================ */
        .vsq-recaptcha {
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 4px;
            padding: 12px 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
            height: 80px;
        }

        .vsq-recaptcha-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .vsq-recaptcha-checkbox {
            width: 24px;
            height: 24px;
            border: 2px solid #9CA3AF;
            border-radius: 2px;
            background: #FFFFFF;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            transition: all 0.2s;
            position: relative;
            flex-shrink: 0;
        }
        .vsq-recaptcha-checkbox:checked {
            background: #2563EB;
            border-color: #2563EB;
        }
        .vsq-recaptcha-checkbox:checked::after {
            content: "✓";
            color: #FFFFFF;
            font-size: 16px;
            font-weight: 700;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .vsq-recaptcha-label {
            font-size: 13px;
            color: #374151;
            cursor: pointer;
            font-weight: 400;
        }

        /* Logo reCAPTCHA resmi */
        .vsq-recaptcha-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0;
            line-height: 1;
        }

        .vsq-recaptcha-logo svg {
            width: 42px;
            height: 42px;
        }

        .vsq-recaptcha-logo .recaptcha-text {
            font-size: 9px;
            color: #6B7280;
            letter-spacing: 0.3px;
            font-family: Arial, sans-serif;
            margin-top: -2px;
        }

        .vsq-register-link {
            text-align: center;
            font-size: 12px;
            color: #000000;
            margin-bottom: 22px;
        }
        .vsq-register-link a {
            color: #2563EB;
            text-decoration: none;
            font-weight: 600;
        }
        .vsq-register-link a:hover { text-decoration: underline; }

        .vsq-btn-submit {
            display: block;
            width: 100%;
            padding: 12px 20px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #FFFFFF;
            background-color: #2563EB;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
        }
        .vsq-btn-submit:hover {
            background-color: #1D4ED8;
            transform: translateY(-1px);
        }

        .vsq-alert {
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 16px;
            text-align: center;
        }
        .vsq-alert-error {
            background-color: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FECACA;
        }

        .vsq-login-right {
            flex: 1;
            background: 
                linear-gradient(rgba(15, 23, 42, 0.35), rgba(15, 23, 42, 0.35)),
                url('<?= htmlspecialchars($hero_url) ?>') center/cover no-repeat;
            position: relative;
        }

        @media (max-width: 900px) {
            .vsq-login-wrapper { flex-direction: column; }
            .vsq-login-left { padding: 40px 20px; order: 2; }
            .vsq-login-right { min-height: 250px; order: 1; }
        }
        @media (max-width: 480px) {
            .vsq-logo h1 { font-size: 24px; }
            .vsq-login-left { padding: 30px 20px; }
        }
    </style>
</head>
<body>

    <div class="vsq-login-wrapper">
        
        <div class="vsq-login-left">
            <div class="vsq-login-card">
                
                <div class="vsq-logo">
                    <h1>Vincent'SQ</h1>
                    <p>Sports Arena</p>
                </div>

                <div class="vsq-welcome">Selamat Datang kembali!</div>

                <?php if (!empty($error)): ?>
                    <div class="vsq-alert vsq-alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form action="" method="POST">
                    
                    <div class="vsq-form-group">
                        <label for="email">Email<span>*</span></label>
                        <div class="vsq-input-wrapper">
                            <input type="email" id="email" name="email" class="vsq-form-control" placeholder="Masukkan email..." required>
                        </div>
                    </div>

                    <div class="vsq-form-group">
                        <label for="password">Password<span>*</span></label>
                        <div class="vsq-input-wrapper">
                            <input type="password" id="password" name="password" class="vsq-form-control" placeholder="Masukkan Password..." required>
                            <button type="button" class="vsq-toggle-password" onclick="togglePassword('password', this)">
                                <svg viewBox="0 0 24 24" class="icon-eye-closed">
                                    <path d="M3 10 Q12 18 21 10" />
                                    <line x1="3" y1="10" x2="2" y2="13" />
                                    <line x1="7" y1="13" x2="5.5" y2="16" />
                                    <line x1="12" y1="15" x2="12" y2="18" />
                                    <line x1="17" y1="13" x2="18.5" y2="16" />
                                    <line x1="21" y1="10" x2="22" y2="13" />
                                </svg>
                                <svg viewBox="0 0 24 24" class="icon-eye-open" style="display:none;">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="vsq-forgot-link">
                        <a href="#">Lupa Password?</a>
                    </div>

                    <!-- ============================================================
                         reCAPTCHA BOX dengan Logo RESMI reCAPTCHA
                         (Dua panah melingkar: biru tua + biru muda + abu-abu)
                         ============================================================ -->
                    <div class="vsq-recaptcha">
                        <div class="vsq-recaptcha-left">
                            <input type="checkbox" id="recaptcha" class="vsq-recaptcha-checkbox" required>
                            <label for="recaptcha" class="vsq-recaptcha-label">I'm not a robot</label>
                        </div>
                        <div class="vsq-recaptcha-logo">
                            <!-- Logo reCAPTCHA resmi (dua panah melingkar) -->
                            <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                <!-- Panah biru tua (kanan atas) -->
                                <path d="M24 4 C35.05 4 44 12.95 44 24 L38 24 C38 16.27 31.73 10 24 10 L24 4 Z" fill="#1C3AA9"/>
                                <!-- Ujung panah biru tua (segitiga) -->
                                <path d="M44 24 L38 24 L41 18 L44 24 Z" fill="#1C3AA9"/>
                                <path d="M24 4 L30 4 L24 10 L24 4 Z" fill="#1C3AA9"/>
                                
                                <!-- Panah abu-abu (kiri bawah) -->
                                <path d="M24 44 C12.95 44 4 35.05 4 24 L10 24 C10 31.73 16.27 38 24 38 L24 44 Z" fill="#B0B0B0"/>
                                <!-- Ujung panah abu-abu (segitiga) -->
                                <path d="M4 24 L10 24 L7 30 L4 24 Z" fill="#B0B0B0"/>
                                <path d="M24 44 L18 44 L24 38 L24 44 Z" fill="#B0B0B0"/>
                                
                                <!-- Panah biru muda (kiri atas) -->
                                <path d="M24 4 C16 4 9.5 8.5 6.5 14.5 L11.5 17.5 C13.5 13 18.5 10 24 10 L24 4 Z" fill="#4285F4"/>
                                
                                <!-- Pusat putih membentuk huruf C -->
                                <circle cx="24" cy="24" r="9" fill="#F9FAFB"/>
                                <!-- Bentuk huruf C di tengah -->
                                <path d="M24 15 A9 9 0 0 1 33 24 L28 24 A4 4 0 0 0 24 20 L24 15 Z" fill="#FFFFFF"/>
                                <path d="M24 33 A9 9 0 0 1 15 24 L20 24 A4 4 0 0 0 24 28 L24 33 Z" fill="#FFFFFF"/>
                            </svg>
                            <div class="recaptcha-text">reCAPTCHA</div>
                        </div>
                    </div>

                    <div class="vsq-register-link">
                        Belum mempunyai akun? <a href="register.php">Daftar sekarang</a>
                    </div>

                    <button type="submit" class="vsq-btn-submit">Masuk</button>

                </form>

            </div>
        </div>

        <div class="vsq-login-right"></div>

    </div>

    <script>
        function togglePassword(inputId, iconElement) {
            const input = document.getElementById(inputId);
            const iconClosed = iconElement.querySelector('.icon-eye-closed');
            const iconOpen = iconElement.querySelector('.icon-eye-open');
            
            if (input.type === 'password') {
                input.type = 'text';
                iconClosed.style.display = 'none';
                iconOpen.style.display = 'block';
            } else {
                input.type = 'password';
                iconClosed.style.display = 'block';
                iconOpen.style.display = 'none';
            }
        }
    </script>

</body>
</html>