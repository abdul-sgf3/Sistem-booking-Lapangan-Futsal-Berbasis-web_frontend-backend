<?php
// verifikasi_email.php - Halaman Verifikasi Email
session_start();

$email = $_SESSION['register_email'] ?? 'sdsh@gmail.com';
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
        .vsq-back-btn:hover { background: rgba(255, 255, 255, 0.2); transform: translateX(-4px); }
        .vsq-back-btn svg {
            width: 28px; height: 28px;
            stroke: #FFFFFF; stroke-width: 2.5;
            fill: none; stroke-linecap: round; stroke-linejoin: round;
        }

        /* Kartu Verifikasi */
        .vsq-verify-card {
            background: #FFFFFF;
            width: 100%;
            max-width: 420px;
            border-radius: 24px;
            padding: 40px 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            z-index: 5;
            text-align: center;
        }

        /* Logo */
        .vsq-logo { margin-bottom: 15px; }
        
        /* Font "Vincent'SQ" tidak Bold (600) */
        .vsq-logo h1 {
            font-size: 28px;
            font-weight: 600;
            color: var(--vsq-title-blue);
            letter-spacing: -0.5px;
            margin: 0;
            line-height: 1.2;
        }
        .vsq-logo p {
            font-size: 12px;
            font-weight: 700;
            color: var(--vsq-red);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* Ilustrasi */
        .vsq-illustration {
            margin: 20px 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        /* SVG Ilustrasi */
        .vsq-illustration svg {
            width: 220px;
            height: auto;
            display: block;
        }

        /* Teks Instruksi */
        .vsq-instruction {
            font-size: 14px;
            color: var(--vsq-black);
            line-height: 1.6;
            margin-bottom: 24px;
            font-weight: 400;
        }
        .vsq-instruction strong { font-weight: 700; color: var(--vsq-black); }

        /* Kotak Input OTP */
        .vsq-otp-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .vsq-otp-input {
            width: 48px;
            height: 56px;
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--vsq-black);
            background-color: #FFFFFF;
            border: 1.5px solid var(--vsq-input-border);
            border-radius: 10px;
            outline: none;
            transition: all 0.3s ease;
        }
        .vsq-otp-input:focus {
            border-color: var(--vsq-btn-blue);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
        }

        /* Timer */
        .vsq-timer {
            font-size: 13px;
            color: var(--vsq-gray-text);
            margin-bottom: 12px;
            font-weight: 400;
        }
        
        /* Teks "Kirim ulang kode" berwarna BIRU */
        .vsq-timer span { 
            color: var(--vsq-btn-blue);
            font-weight: 600; 
        }

        /* Link Ganti Email */
        .vsq-change-email {
            font-size: 13px;
            color: var(--vsq-gray-text);
            margin-bottom: 24px;
            font-weight: 400;
        }
        .vsq-change-email a {
            color: var(--vsq-btn-blue);
            text-decoration: none;
            font-weight: 400;
            transition: color 0.2s;
        }
        .vsq-change-email a:hover { color: #1D4ED8; text-decoration: underline; }

        /* Tombol Verifikasi */
        .vsq-btn-submit {
            display: block;
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
            padding: 14px 20px;
            font-size: 16px;
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

        /* Responsive */
        @media (max-width: 480px) {
            .vsq-verify-card { padding: 30px 20px; border-radius: 20px; }
            .vsq-logo h1 { font-size: 24px; }
            .vsq-otp-input { width: 42px; height: 50px; font-size: 20px; }
            .vsq-back-btn { top: 20px; left: 20px; width: 40px; height: 40px; }
            .vsq-back-btn svg { width: 24px; height: 24px; }
            .vsq-illustration svg { width: 180px; }
        }
    </style>
</head>
<body>

    <!-- Tombol Kembali -->
    <a href="register.php" class="vsq-back-btn" title="Kembali ke Register">
        <svg viewBox="0 0 24 24">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
    </a>

    <!-- Kartu Verifikasi -->
    <div class="vsq-verify-card">
        
        <!-- Logo -->
        <div class="vsq-logo">
            <h1>Vincent'SQ</h1>
            <p>Sports Arena</p>
        </div>

        <!-- Ilustrasi SVG Detail (Meniru Gambar Target) -->
        <div class="vsq-illustration">
            <svg viewBox="0 0 500 400" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- === BACKGROUND GEDUNG === -->
                <rect x="100" y="80" width="300" height="240" rx="8" fill="#F8FAFC"/>
                <rect x="100" y="80" width="300" height="15" fill="#E2E8F0"/>
                <!-- Jendela Gedung -->
                <rect x="140" y="130" width="70" height="80" rx="4" fill="#E2E8F0"/>
                <rect x="230" y="130" width="70" height="80" rx="4" fill="#E2E8F0"/>
                <rect x="320" y="130" width="50" height="80" rx="4" fill="#E2E8F0"/>
                <!-- Pintu Gedung -->
                <rect x="200" y="240" width="100" height="80" rx="4" fill="#E2E8F0"/>

                <!-- === KOTAK POS BIRU === -->
                <!-- Tiang Penyangga Kotak Pos -->
                <rect x="140" y="320" width="70" height="60" fill="#2563EB"/>
                <rect x="130" y="370" width="90" height="10" rx="5" fill="#1E40AF"/>
                
                <!-- Badan Kotak Pos (Kanan) -->
                <rect x="220" y="200" width="140" height="130" rx="12" fill="#2563EB"/>
                <!-- Atap Lengkung Kotak Pos -->
                <path d="M50 230 Q160 120 260 230" fill="#3B82F6"/>
                <!-- Badan Kotak Pos (Kiri) -->
                <rect x="50" y="230" width="180" height="100" rx="8" fill="#3B82F6"/>
                
                <!-- Garis Horizontal pada Kotak Pos -->
                <rect x="50" y="260" width="180" height="10" fill="#1D4ED8" opacity="0.5"/>
                <rect x="50" y="290" width="180" height="10" fill="#1D4ED8" opacity="0.5"/>
                <rect x="220" y="260" width="140" height="10" fill="#1D4ED8" opacity="0.5"/>
                <rect x="220" y="290" width="140" height="10" fill="#1D4ED8" opacity="0.5"/>
                
                <!-- Bendera Kotak Pos -->
                <rect x="80" y="190" width="12" height="60" rx="4" fill="#1E293B"/>
                <rect x="92" y="190" width="40" height="30" rx="5" fill="#EF4444"/>
                <path d="M92 205 L132 205" stroke="#DC2626" stroke-width="3"/>

                <!-- === AMPLOP BESAR === -->
                <rect x="250" y="220" width="200" height="140" rx="8" fill="#DBEAFE" stroke="#93C5FD" stroke-width="4"/>
                <!-- Lipatan Amplop Segitiga -->
                <path d="M250 225 L350 310 L450 225" stroke="#93C5FD" stroke-width="4" fill="none" stroke-linejoin="round"/>
                <!-- Segitiga Lipatan Kanan -->
                <path d="M380 220 L450 270 L450 220" fill="#BFDBFE" opacity="0.6"/>

                <!-- === KURIR PRIA === -->
                <!-- Kaki Kiri -->
                <rect x="320" y="310" width="20" height="70" rx="8" fill="#1E293B"/>
                <rect x="315" y="370" width="30" height="15" rx="5" fill="#334155"/>
                <!-- Kaki Kanan -->
                <rect x="365" y="310" width="20" height="70" rx="8" fill="#1E293B"/>
                <rect x="360" y="370" width="30" height="15" rx="5" fill="#334155"/>
                <!-- Sepatu -->
                <rect x="310" y="375" width="40" height="12" rx="4" fill="#94A3B8"/>
                <rect x="355" y="375" width="40" height="12" rx="4" fill="#94A3B8"/>
                
                <!-- Badan (Seragam Biru) -->
                <rect x="310" y="190" width="85" height="130" rx="15" fill="#3B82F6"/>
                <!-- Kerah Baju -->
                <path d="M335 190 L352 225 L370 190" fill="#2563EB"/>
                <!-- Lengan Kiri (Memegang Amplop) -->
                <rect x="280" y="210" width="35" height="75" rx="12" fill="#3B82F6"/>
                <!-- Tangan Kiri -->
                <circle cx="295" cy="285" r="12" fill="#FCD34D"/>
                <!-- Lengan Kanan -->
                <rect x="390" y="210" width="35" height="75" rx="12" fill="#3B82F6"/>
                <!-- Tangan Kanan -->
                <circle cx="408" cy="285" r="12" fill="#FCD34D"/>

                <!-- Leher -->
                <rect x="340" y="170" width="25" height="25" fill="#FCD34D"/>
                
                <!-- Kepala -->
                <circle cx="352" cy="140" r="35" fill="#FCD34D"/>
                <!-- Rambut -->
                <path d="M317 140 Q325 100 352 100 Q379 100 387 140" fill="#78350F"/>
                <!-- Topi Kurir -->
                <path d="M317 135 Q352 95 387 135" fill="#1D4ED8"/>
                <rect x="317" y="125" width="70" height="12" rx="6" fill="#1E3A8A"/>
                <!-- Visor Topi -->
                <rect x="352" y="125" width="35" height="8" rx="4" fill="#1E3A8A"/>
                
                <!-- Wajah -->
                <circle cx="340" cy="140" r="3.5" fill="#1E293B"/>
                <circle cx="365" cy="140" r="3.5" fill="#1E293B"/>
                <path d="M345 155 Q352 165 360 155" stroke="#1E293B" stroke-width="3" fill="none" stroke-linecap="round"/>
            </svg>
        </div>

        <!-- Teks Instruksi -->
        <div class="vsq-instruction">
            Kami telah mengirimkan kode verifikasi ke<br>
            <strong><?= htmlspecialchars($email) ?></strong>
        </div>

        <!-- Kotak Input OTP -->
        <form action="proses_verifikasi.php" method="POST" id="otpForm">
            <div class="vsq-otp-container">
                <input type="text" maxlength="1" class="vsq-otp-input" inputmode="numeric" pattern="[0-9]*" autofocus>
                <input type="text" maxlength="1" class="vsq-otp-input" inputmode="numeric" pattern="[0-9]*">
                <input type="text" maxlength="1" class="vsq-otp-input" inputmode="numeric" pattern="[0-9]*">
                <input type="text" maxlength="1" class="vsq-otp-input" inputmode="numeric" pattern="[0-9]*">
                <input type="text" maxlength="1" class="vsq-otp-input" inputmode="numeric" pattern="[0-9]*">
                <input type="text" maxlength="1" class="vsq-otp-input" inputmode="numeric" pattern="[0-9]*">
            </div>

            <!-- Timer -->
            <div class="vsq-timer">
                Kirim ulang kode dalam <span id="countdown">01:00</span>
            </div>

            <!-- Link Ganti Email -->
            <div class="vsq-change-email">
                Salah Email? <a href="register.php">Ganti email</a>
            </div>

            <!-- Tombol Verifikasi -->
            <button type="submit" class="vsq-btn-submit">Verifikasi</button>
        </form>

    </div>

    <script>
        // ==== FITUR INPUT OTP OTOMATIS PINDAH FOKUS ====
        const otpInputs = document.querySelectorAll('.vsq-otp-input');
        
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
                if (e.target.value.length === 1 && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && e.target.value.length === 0 && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });

            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);
                if (pastedData) {
                    for (let i = 0; i < pastedData.length; i++) {
                        if (otpInputs[i]) {
                            otpInputs[i].value = pastedData[i];
                        }
                    }
                    const lastIndex = Math.min(pastedData.length, otpInputs.length) - 1;
                    if (lastIndex >= 0) {
                        otpInputs[lastIndex].focus();
                    }
                }
            });
        });

        // ==== FITUR TIMER COUNTDOWN 01:00 ====
        let timeLeft = 60;
        const countdownElement = document.getElementById('countdown');

        const timer = setInterval(() => {
            if (timeLeft <= 0) {
                clearInterval(timer);
                countdownElement.textContent = "00:00";
                countdownElement.parentElement.innerHTML = 'Kode telah kadaluarsa. <a href="#" style="color: #2563EB; font-weight: 600;">Kirim ulang</a>';
                return;
            }

            timeLeft--;
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            countdownElement.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }, 1000);
    </script>

</body>
</html>