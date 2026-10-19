<?php
// register.php - Halaman Register User (Sesuai Figma 100%)
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
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Semua field wajib diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } elseif ($password !== $confirm_password) {
        $error = "Password dan Konfirmasi Password tidak cocok!";
    } else {
        $cek_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
        if ($cek_email && mysqli_num_rows($cek_email) > 0) {
            $error = "Email sudah terdaftar! Silakan gunakan email lain.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $nama = ucfirst(explode('@', $email)[0]);
            $username = explode('@', $email)[0];
            $no_hp = '';
            
            $query = "INSERT INTO users (nama, username, email, no_hp, password, role) 
                      VALUES ('$nama', '$username', '$email', '$no_hp', '$hashed_password', 'user')";
            
            if (mysqli_query($conn, $query)) {
                $_SESSION['register_email'] = $email;
                $success = "Akun berhasil dibuat!";
                header("refresh:2;url=register_sesi2.php");
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
    <title>Register - Vincent'SQ Arena</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --vsq-navy: #131B2E;
            --vsq-blue: #477FEB;
            --vsq-red: #FF0000;
            --vsq-gray: #848484;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background: 
                linear-gradient(rgba(19, 27, 46, 0.4), rgba(19, 27, 46, 0.4)),
                url('<?= htmlspecialchars($hero_url) ?>') center/cover no-repeat fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .vsq-back-btn {
            position: absolute;
            top: 40px;
            left: 40px;
            color: #FFFFFF;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .vsq-back-btn svg {
            width: 32px; height: 32px;
            stroke: #FFFFFF; stroke-width: 2.5;
            fill: none; stroke-linecap: round; stroke-linejoin: round;
        }

        .vsq-register-card {
            background: #FFFFFF;
            width: 100%;
            max-width: 380px;
            border-radius: 12px;
            padding: 35px 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .vsq-logo { text-align: center; margin-bottom: 24px; }
        .vsq-logo h1 {
            font-size: 24px;
            font-weight: 800;
            color: var(--vsq-navy);
            margin: 0;
        }
        .vsq-logo p {
            font-size: 12px;
            font-weight: 800;
            color: var(--vsq-red);
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .vsq-form-group { margin-bottom: 18px; text-align: left; }
        .vsq-form-group label {
            display: block;
            font-size: 12px;
            font-weight: 500;
            color: #000;
            margin-bottom: 6px;
        }
        .vsq-form-group label span { color: var(--vsq-red); }

        .vsq-input-wrapper { position: relative; width: 100%; }
        .vsq-form-control {
            width: 100%;
            padding: 10px 12px;
            padding-right: 40px;
            font-size: 13px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #FFFFFF;
            border: 1px solid var(--vsq-gray);
            border-radius: 6px;
            outline: none;
            transition: all 0.2s;
        }
        .vsq-form-control:focus {
            border-color: var(--vsq-blue);
        }

        /* Validasi Error */
        .vsq-form-control.is-invalid {
            border-color: var(--vsq-red);
        }
        .error-message {
            color: var(--vsq-red);
            font-size: 10px;
            margin-top: 4px;
            display: none;
            align-items: center;
            gap: 4px;
        }
        .error-message::before {
            content: "ⓘ";
            font-size: 12px;
        }
        .vsq-form-control.is-invalid ~ .error-message {
            display: flex;
        }

        .vsq-toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #000;
            background: transparent;
            border: none;
        }
        .vsq-toggle-password svg {
            width: 18px; height: 18px;
            stroke: currentColor; stroke-width: 2;
            fill: none; stroke-linecap: round; stroke-linejoin: round;
        }

        .vsq-btn-submit {
            display: block;
            width: 100%;
            padding: 12px;
            font-size: 14px;
            font-weight: 600;
            color: #FFFFFF;
            background-color: var(--vsq-blue);
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.2s;
        }
        .vsq-btn-submit:hover { background-color: #3B6AC4; }

        .vsq-login-link {
            text-align: center;
            font-size: 11px;
            margin-top: 16px;
            color: #666;
        }
        .vsq-login-link a { color: var(--vsq-blue); text-decoration: none; font-weight: 600; }

        .vsq-alert {
            padding: 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 16px;
            text-align: center;
        }
        .vsq-alert-error { background-color: #FEE2E2; color: #991B1B; }
        .vsq-alert-success { background-color: #DCFCE7; color: #166534; }

    </style>
</head>
<body>

    <a href="../pages/index.php" class="vsq-back-btn">
        <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
    </a>

    <div class="vsq-register-card">
        
        <div class="vsq-logo">
            <h1>Vincent'SQ</h1>
            <p>SPORTS ARENA</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="vsq-alert vsq-alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="vsq-alert vsq-alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form action="" method="POST" id="registerForm">
            
            <!-- Email -->
            <div class="vsq-form-group">
                <label for="email">Email<span>*</span></label>
                <div class="vsq-input-wrapper">
                    <input type="email" id="email" name="email" class="vsq-form-control" placeholder="Masukkan email..." required>
                </div>
                <div class="error-message" id="error-email">Email tidak boleh kosong atau format salah</div>
            </div>

            <!-- Password -->
            <div class="vsq-form-group">
                <label for="password">Password<span>*</span></label>
                <div class="vsq-input-wrapper">
                    <input type="password" id="password" name="password" class="vsq-form-control" placeholder="Masukkan Password..." required>
                    <button type="button" class="vsq-toggle-password" onclick="togglePassword('password', this)">
                        <svg viewBox="0 0 24 24" class="icon-eye-closed">
                            <path d="M3 10 Q12 18 21 10" /><line x1="3" y1="10" x2="2" y2="13" /><line x1="7" y1="13" x2="5.5" y2="16" /><line x1="12" y1="15" x2="12" y2="18" /><line x1="17" y1="13" x2="18.5" y2="16" /><line x1="21" y1="10" x2="22" y2="13" />
                        </svg>
                        <svg viewBox="0 0 24 24" class="icon-eye-open" style="display:none;">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
                <div class="error-message" id="error-password">Password minimal 6 karakter</div>
            </div>

            <!-- Konfirmasi Password -->
            <div class="vsq-form-group">
                <label for="confirm_password">Konfirmasi Password<span>*</span></label>
                <div class="vsq-input-wrapper">
                    <input type="password" id="confirm_password" name="confirm_password" class="vsq-form-control" placeholder="Ulangi Password..." required>
                    <button type="button" class="vsq-toggle-password" onclick="togglePassword('confirm_password', this)">
                        <svg viewBox="0 0 24 24" class="icon-eye-closed">
                            <path d="M3 10 Q12 18 21 10" /><line x1="3" y1="10" x2="2" y2="13" /><line x1="7" y1="13" x2="5.5" y2="16" /><line x1="12" y1="15" x2="12" y2="18" /><line x1="17" y1="13" x2="18.5" y2="16" /><line x1="21" y1="10" x2="22" y2="13" />
                        </svg>
                        <svg viewBox="0 0 24 24" class="icon-eye-open" style="display:none;">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
                <div class="error-message" id="error-confirm">Konfirmasi password tidak cocok</div>
            </div>

            <div class="vsq-login-link">
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
                input.type = 'text';
                iconClosed.style.display = 'none';
                iconOpen.style.display = 'block';
            } else {
                input.type = 'password';
                iconClosed.style.display = 'block';
                iconOpen.style.display = 'none';
            }
        }

        // Validasi Real-time
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('confirm_password');
        const form = document.getElementById('registerForm');

        function validateEmail() {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!re.test(emailInput.value)) {
                emailInput.classList.add('is-invalid');
                return false;
            } else {
                emailInput.classList.remove('is-invalid');
                return true;
            }
        }

        function validatePassword() {
            if (passwordInput.value.length < 6) {
                passwordInput.classList.add('is-invalid');
                return false;
            } else {
                passwordInput.classList.remove('is-invalid');
                return true;
            }
        }

        function validateConfirm() {
            if (confirmInput.value !== passwordInput.value || confirmInput.value === '') {
                confirmInput.classList.add('is-invalid');
                return false;
            } else {
                confirmInput.classList.remove('is-invalid');
                return true;
            }
        }

        emailInput.addEventListener('input', validateEmail);
        passwordInput.addEventListener('input', validatePassword);
        confirmInput.addEventListener('input', validateConfirm);

        form.addEventListener('submit', function(e) {
            if (!validateEmail() || !validatePassword() || !validateConfirm()) {
                e.preventDefault();
            }
        });
    </script>

</body>
</html>