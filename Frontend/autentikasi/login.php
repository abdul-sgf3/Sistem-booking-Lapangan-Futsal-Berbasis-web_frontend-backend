<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Vincent'SQ Arena</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <script>
    tailwind.config = {
      theme: { extend: {
        fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
        colors: { navy: '#0B2A52', accent: '#E8394A', ok: '#16A34A' },
      }},
    };
  </script>
</head>
<body class="font-sans text-slate-800 bg-slate-50 min-h-screen flex items-center justify-center p-4"><?php
// login.php - Halaman login USER dengan Username + CAPTCHA
session_start();
include '../../config/database.php';

// Generate CAPTCHA matematika
function generateCaptcha() {
    $angka1 = rand(1, 20);
    $angka2 = rand(1, 20);
    $_SESSION['user_captcha_angka1'] = $angka1;
    $_SESSION['user_captcha_angka2'] = $angka2;
    $_SESSION['user_captcha_hasil'] = $angka1 + $angka2;
    return [$angka1, $angka2];
}

// Cek apakah CAPTCHA sudah ada
if (!isset($_SESSION['user_captcha_angka1'])) {
    generateCaptcha();
}

$angka1 = $_SESSION['user_captcha_angka1'];
$angka2 = $_SESSION['user_captcha_angka2'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);
    $captcha = (int)$_POST['captcha'];
    
    // Validasi CAPTCHA
    if ($captcha != $_SESSION['user_captcha_hasil']) {
        $error = "❌ Kode keamanan salah!";
        generateCaptcha();
        $angka1 = $_SESSION['user_captcha_angka1'];
        $angka2 = $_SESSION['user_captcha_angka2'];
    } else {
        // Cek user dengan username dan role 'user'
        $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password' AND role = 'user' AND is_active = 1";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nama'] = $user['nama'];
            $_SESSION['user_username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['login_success'] = true;
            
            generateCaptcha(); // Reset captcha untuk next login
            header("Location: index.php");
            exit();
        } else {
            $error = "❌ Username atau password salah!";
            generateCaptcha();
            $angka1 = $_SESSION['user_captcha_angka1'];
            $angka2 = $_SESSION['user_captcha_angka2'];
        }
    }
}

$title = 'Login - Vincent\'sQ Arena';
?>

<div class="container" style="max-width: 500px; margin: 60px auto;">
    <div style="background: white; border-radius: 24px; padding: 35px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-top: 5px solid #e63946;">
        
        <h2 style="text-align: center; color: #0a2b4e; margin-bottom: 30px;">⚡ LOGIN USER ⚡</h2>
        
        <?php if ($error): ?>
            <div style="background: #fee; color: #e63946; padding: 12px; border-radius: 12px; margin-bottom: 20px; text-align: center; border-left: 3px solid #e63946;">
                <?= $error ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" id="loginForm">
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">👤 USERNAME</label>
                <input type="text" name="username" id="username" required placeholder="Masukkan username" autocomplete="off" style="width: 100%; padding: 14px; border: 2px solid #e1e1e1; border-radius: 12px; font-size: 14px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">🔒 PASSWORD</label>
                <input type="password" name="password" id="password" required placeholder="Masukkan password" style="width: 100%; padding: 14px; border: 2px solid #e1e1e1; border-radius: 12px;">
            </div>
            
            <!-- CAPTCHA Matematika -->
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">🔐 CAPTCHA</label>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 12px; text-align: center;">
                    <div id="captchaQuestion" style="font-size: 24px; font-weight: bold; background: #0a2b4e; color: white; display: inline-block; padding: 8px 20px; border-radius: 40px; margin-bottom: 12px;">
                        <?= $angka1 ?> + <?= $angka2 ?> = ?
                    </div>
                    <input type="number" name="captcha" id="captcha" required placeholder="Masukkan hasil penjumlahan" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 12px; text-align: center; font-size: 16px;">
                    <button type="button" id="refreshCaptcha" style="background: #0a2b4e; color: white; padding: 8px 20px; margin-top: 12px; border: none; border-radius: 30px; cursor: pointer;">🔄 Refresh Captcha</button>
                </div>
            </div>
            
            <button type="submit" style="width: 100%; padding: 14px; background: linear-gradient(135deg, #e63946 0%, #c1121f 100%); color: white; border: none; border-radius: 30px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s;">⚡ LOGIN ⚡</button>
        </form>
        
        <p style="text-align: center; margin-top: 25px;">
            Belum punya akun? <a href="register.php" style="color: #e63946; text-decoration: none; font-weight: 600;">Register di sini</a>
        </p>
    </div>
</div>

<script>
    // Refresh CAPTCHA dengan AJAX
    const refreshBtn = document.getElementById('refreshCaptcha');
    const captchaQuestion = document.getElementById('captchaQuestion');
    const captchaInput = document.getElementById('captcha');
    
    if (refreshBtn) {
        refreshBtn.addEventListener('click', async function() {
            const originalText = refreshBtn.innerHTML;
            refreshBtn.innerHTML = '🔄 Loading...';
            refreshBtn.disabled = true;
            
            try {
                const response = await fetch('refresh_captcha_user.php');
                const data = await response.json();
                
                if (data.success) {
                    captchaQuestion.innerHTML = data.angka1 + ' + ' + data.angka2 + ' = ?';
                    captchaInput.value = '';
                    captchaQuestion.style.transform = 'scale(1.05)';
                    setTimeout(() => {
                        captchaQuestion.style.transform = 'scale(1)';
                    }, 200);
                } else {
                    alert('Gagal mengganti soal, silakan reload halaman.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan, silakan reload halaman.');
            } finally {
                refreshBtn.innerHTML = originalText;
                refreshBtn.disabled = false;
            }
        });
    }
</script>
</body>
</html>