<?php
// lupa_password.php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Vincent'SQ Arena</title>
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
            background: #FFFFFF;
        }

        /* Tombol Kembali */
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
            max-width: 515px; /* Sesuai Figma */
        }

        /* Logo (Ukuran Jumbo Sesuai Figma) */
        .vsq-logo { text-align: center; margin-bottom: 20px; }
        .vsq-logo h1 { 
            font-size: 51px; 
            font-weight: 700; 
            color: #2C437B; 
            margin: 0; 
            line-height: 1; 
            letter-spacing: -0.45px;
        }
        .vsq-logo p { 
            font-size: 38px; 
            font-weight: 700; 
            color: #FF0000; 
            letter-spacing: 0.6px; 
            margin-top: 8px; 
            text-transform: uppercase; 
            line-height: 1;
        }

        /* Judul */
        .vsq-title { text-align: center; margin-bottom: 30px; }
        .vsq-title h2 { font-size: 24px; font-weight: 700; color: #000; margin: 0 0 8px 0; }
        .vsq-title p { font-size: 18px; color: #333; margin: 0; }

        /* Ilustrasi */
        .vsq-illustration {
            text-align: center;
            margin-bottom: 30px;
        }
        .vsq-illustration img {
            width: 250px; /* Ukuran ilustrasi disesuaikan */
            height: auto;
        }

        /* Form */
        .vsq-form-group { margin-bottom: 24px; text-align: left; }
        .vsq-form-group label {
            display: block;
            font-size: 20px;
            font-weight: 400;
            color: #000;
            margin-bottom: 8px;
            line-height: 40px;
        }
        .vsq-form-group label span { color: var(--vsq-red); }
        
        .vsq-form-control {
            width: 100%;
            height: 56px;
            padding: 0 16px;
            font-size: 20px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #000;
            background-color: #FFFFFF;
            border: 1px solid #D9D9D9;
            border-radius: 15px;
            outline: none;
            transition: all 0.2s;
        }
        .vsq-form-control::placeholder { color: #848484; }
        .vsq-form-control:focus { border-color: var(--vsq-blue); box-shadow: 0 0 0 3px rgba(71, 127, 235, 0.15); }

        .vsq-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 53px;
            font-size: 20px;
            font-weight: 600;
            color: #FFFFFF;
            background-color: var(--vsq-blue);
            border: none;
            border-radius: 15px;
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
            .vsq-logo h1 { font-size: 36px; }
            .vsq-logo p { font-size: 24px; }
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

            <div class="vsq-title">
                <h2>Lupa Password?</h2>
                <p>Jangan khawatir, kami akan membantumu mereset password</p>
            </div>

            <div class="vsq-illustration">
                <!-- Sesuaikan src dengan nama file gambar ilustrasi Anda -->
                <img src="/vincent-arena/Frontend/assets/images/ilustrasi-forgot-password.png" alt="Ilustrasi Lupa Password" onerror="this.src='https://illustrations.popsy.co/blue/forgot-password.svg'">
            </div>

            <form action="proses_lupa_password.php" method="POST">
                <div class="vsq-form-group">
                    <label for="email">Email<span>*</span></label>
                    <input type="email" id="email" name="email" class="vsq-form-control" placeholder="Masukkan email..." required>
                </div>

                <button type="submit" class="vsq-btn">Masuk</button>
            </form>
        </div>
    </div>

    <div class="vsq-right"></div>

</body>
</html>