<?php
// register_sesi2.php - Verifikasi Email (Sesuai Figma 100%)
session_start();

$email = $_SESSION['register_email'] ?? '';
if (empty($email)) {
    header("Location: register.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - Vincent'SQ Arena</title>
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
                url('/vincent-arena/Frontend/assets/uploads/lapangan/hero.jpeg') center/cover no-repeat fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Tombol Kembali di Pojok Kiri Atas */
        .vsq-back-btn {
            position: absolute;
            top: 40px;
            left: 40px;
            color: #FFFFFF;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }
        .vsq-back-btn svg {
            width: 32px; height: 32px;
            stroke: #FFFFFF; stroke-width: 2.5;
            fill: none; stroke-linecap: round; stroke-linejoin: round;
        }

        .vsq-verify-card {
            background: #FFFFFF;
            width: 100%;
            max-width: 400px;
            border-radius: 16px;
            padding: 40px 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            text-align: center;
            position: relative;
        }

        .vsq-logo { text-align: center; margin-bottom: 20px; }
        .vsq-logo h1 {
            font-size: 28px;
            font-weight: 800;
            color: var(--vsq-navy);
            margin: 0;
        }
        .vsq-logo p {
            font-size: 14px;
            font-weight: 800;
            color: var(--vsq-red);
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        /* Ikon Ilustrasi Kurir (Menggunakan File Asli) */
        .verify-icon {
            margin: 10px auto 24px;
            width: 180px; /* Ukuran disesuaikan */
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .verify-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .verify-text {
            font-size: 14px;
            color: #333;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .verify-text strong {
            color: var(--vsq-navy);
            font-weight: 700;
            display: block;
            margin-top: 4px;
            font-size: 15px;
        }

        .otp-container {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .otp-input {
            width: 42px;
            height: 52px;
            text-align: center;
            font-size: 20px;
            font-weight: 600;
            color: #333;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            outline: none;
            transition: all 0.2s;
            background: #FFFFFF;
        }
        .otp-input:focus {
            border-color: var(--vsq-blue);
            box-shadow: 0 0 0 2px rgba(71, 127, 235, 0.2);
        }

        .timer-text {
            font-size: 13px;
            color: #6B7280;
            margin-bottom: 24px;
        }

        .btn-verify {
            display: block;
            width: 100%;
            padding: 14px;
            font-size: 15px;
            font-weight: 600;
            color: #FFFFFF;
            background-color: var(--vsq-blue);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-verify:hover { background-color: #3B6AC4; }

        .resend-link {
            display: block;
            margin-top: 20px;
            font-size: 14px;
            color: var(--vsq-blue);
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <!-- Tombol Kembali -->
    <a href="register.php" class="vsq-back-btn">
        <svg viewBox="0 0 24 24">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
    </a>

    <div class="vsq-verify-card">
        
        <!-- Logo -->
        <div class="vsq-logo">
            <h1>Vincent'SQ</h1>
            <p>SPORTS ARENA</p>
        </div>

        <!-- Ilustrasi Kurir Asli -->
        <div class="verify-icon">
            <img src="/vincent-arena/Frontend/assets/images/ilustrasi-kurir.jpg" alt="Ilustrasi Kurir">
        </div>

        <!-- Teks -->
        <div class="verify-text">
            Kami telah mengirimkan kode verifikasi ke
            <strong><?= htmlspecialchars($email) ?></strong>
        </div>

        <!-- Form OTP -->
        <form action="proses_verifikasi.php" method="POST">
            <div class="otp-container">
                <input type="text" maxlength="1" class="otp-input" oninput="moveFocus(this, 1)" autofocus>
                <input type="text" maxlength="1" class="otp-input" oninput="moveFocus(this, 2)">
                <input type="text" maxlength="1" class="otp-input" oninput="moveFocus(this, 3)">
                <input type="text" maxlength="1" class="otp-input" oninput="moveFocus(this, 4)">
                <input type="text" maxlength="1" class="otp-input" oninput="moveFocus(this, 5)">
                <input type="text" maxlength="1" class="otp-input" oninput="moveFocus(this, 6)">
            </div>
            <input type="hidden" name="otp_code" id="otp_code_hidden">

            <div class="timer-text">
                Kode akan kadaluwarsa dalam <span id="timer">01:32</span>
            </div>

            <button type="submit" class="btn-verify" onclick="combineOTP()">Verifikasi</button>
        </form>

        <a href="#" class="resend-link">Kirim Ulang Kode</a>

    </div>

    <script>
        function moveFocus(current, nextIndex) {
            if (current.value.length === 1) {
                const inputs = document.querySelectorAll('.otp-input');
                if (nextIndex < inputs.length) {
                    inputs[nextIndex].focus();
                }
            }
        }

        function combineOTP() {
            const inputs = document.querySelectorAll('.otp-input');
            let code = '';
            inputs.forEach(input => code += input.value);
            document.getElementById('otp_code_hidden').value = code;
        }

        // Timer disetel ke 01:32 sesuai screenshot
        let timeLeft = 92; 
        const timerEl = document.getElementById('timer');
        
        const countdown = setInterval(() => {
            if (timeLeft <= 0) {
                clearInterval(countdown);
                timerEl.textContent = "00:00";
                return;
            }
            timeLeft--;
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerEl.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }, 1000);
    </script>

</body>
</html>