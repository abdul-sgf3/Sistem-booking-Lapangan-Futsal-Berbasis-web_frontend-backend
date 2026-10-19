<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - Vincent SQ Arena</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --vsq-navy: #131B2E;
            --vsq-blue: #477FEB;
            --vsq-red: #FF0000;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background: #FFFFFF;
            display: flex;
            overflow: hidden;
        }

        /* KIRI: Area Form */
        .vsq-left {
            width: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
        }

        .vsq-back-btn {
            position: absolute;
            top: 40px;
            left: 40px;
            color: #000;
            text-decoration: none;
        }
        .vsq-back-btn svg {
            width: 32px; height: 32px;
            stroke: #000; stroke-width: 2.5;
            fill: none; stroke-linecap: round; stroke-linejoin: round;
        }

        .vsq-card {
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .vsq-logo { margin-bottom: 24px; }
        .vsq-logo h1 { font-size: 28px; font-weight: 800; color: var(--vsq-navy); margin: 0; }
        .vsq-logo p { font-size: 14px; font-weight: 800; color: var(--vsq-red); letter-spacing: 1.5px; margin-top: 4px; }

        .vsq-illustration img {
            width: 180px;
            height: auto;
            margin-bottom: 24px;
        }

        .vsq-text {
            font-size: 14px;
            color: #333;
            line-height: 1.5;
            margin-bottom: 24px;
        }
        .vsq-text strong {
            color: var(--vsq-navy);
            font-weight: 700;
            display: block;
            margin-top: 4px;
            font-size: 15px;
        }

        /* Form OTP (5 Kotak) */
        .vsq-otp-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 24px;
        }

        .vsq-otp-input {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 22px;
            font-weight: 600;
            color: #333;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            outline: none;
            transition: all 0.2s;
        }
        .vsq-otp-input:focus {
            border-color: var(--vsq-blue);
            box-shadow: 0 0 0 3px rgba(71, 127, 235, 0.15);
        }

        .vsq-timer-text {
            font-size: 13px;
            color: #333;
            margin-bottom: 8px;
        }
        .vsq-timer-text a { color: var(--vsq-blue); text-decoration: none; font-weight: 600; }
        
        .vsq-resend-text {
            font-size: 13px;
            color: #333;
            margin-bottom: 24px;
        }
        .vsq-resend-text a { color: var(--vsq-blue); text-decoration: none; font-weight: 600; }

        .vsq-btn {
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
        .vsq-btn:hover { background-color: #3B6AC4; }

        /* KANAN: Gambar */
        .vsq-right {
            width: 50%;
            background: url('/vincent-arena/Frontend/assets/uploads/lapangan/hero.jpeg') center/cover no-repeat;
            position: relative;
        }
        .vsq-right::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(19, 27, 46, 0.4);
        }

        @media (max-width: 900px) {
            body { flex-direction: column; overflow: auto; }
            .vsq-left { width: 100%; padding: 30px 20px; }
            .vsq-right { width: 100%; height: 200px; order: -1; }
        }
    </style>
</head>
<body>

    <div class="vsq-left">
        <a href="login.php" class="vsq-back-btn">
            <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        </a>

        <div class="vsq-card">
            <div class="vsq-logo">
                <h1>Vincent'SQ</h1>
                <p>SPORTS ARENA</p>
            </div>

            <div class="vsq-illustration">
                <!-- Gunakan gambar ilustrasi kurir yang sudah Anda miliki -->
                <img src="/vincent-arena/Frontend/assets/images/ilustrasi-kurir.jpg" alt="Ilustrasi Kurir">
            </div>

            <div class="vsq-text">
                Kami telah mengirimkan kode verifikasi ke
                <strong>sdsh@gmail.com</strong>
            </div>

            <form action="proses_verifikasi.php" method="POST">
                <!-- 5 Kotak OTP -->
                <div class="vsq-otp-container">
                    <input type="text" maxlength="1" class="vsq-otp-input" oninput="moveFocus(this, 1)" autofocus>
                    <input type="text" maxlength="1" class="vsq-otp-input" oninput="moveFocus(this, 2)">
                    <input type="text" maxlength="1" class="vsq-otp-input" oninput="moveFocus(this, 3)">
                    <input type="text" maxlength="1" class="vsq-otp-input" oninput="moveFocus(this, 4)">
                    <input type="text" maxlength="1" class="vsq-otp-input" oninput="moveFocus(this, 5)">
                </div>
                <input type="hidden" name="otp_code" id="otp_code_hidden">

                <div class="vsq-timer-text">
                    <a href="#">Kirim ulang kode dalam 01:00</a>
                </div>
                <div class="vsq-resend-text">
                    Salah mengirim email? <a href="#">klik disini!</a>
                </div>

                <button type="submit" class="vsq-btn" onclick="combineOTP()">Melanjutkan</button>
            </form>
        </div>
    </div>

    <div class="vsq-right"></div>

    <script>
        // Script untuk auto-focus antar kotak OTP
        function moveFocus(current, nextIndex) {
            if (current.value.length === 1) {
                const inputs = document.querySelectorAll('.vsq-otp-input');
                if (nextIndex < inputs.length) {
                    inputs[nextIndex].focus();
                }
            }
        }

        // Menggabungkan 5 digit OTP menjadi 1 string sebelum submit
        function combineOTP() {
            const inputs = document.querySelectorAll('.vsq-otp-input');
            let code = '';
            inputs.forEach(input => code += input.value);
            document.getElementById('otp_code_hidden').value = code;
        }
    </script>

</body>
</html>