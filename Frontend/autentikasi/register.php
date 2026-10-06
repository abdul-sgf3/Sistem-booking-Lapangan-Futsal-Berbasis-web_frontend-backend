<?php
// register.php - Halaman registrasi USER
session_start();
include 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $password = md5($_POST['password']);
    $confirm_password = md5($_POST['confirm_password']);
    
    // Validasi password match
    if ($password != $confirm_password) {
        $error = "❌ Password dan konfirmasi password tidak sama!";
    } else {
        // Cek username unik
        $check_username = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
        if (mysqli_num_rows($check_username) > 0) {
            $error = "❌ Username sudah terdaftar!";
        }
        
        // Cek email unik
        $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check_email) > 0) {
            $error = "❌ Email sudah terdaftar!";
        }
        
        if (empty($error)) {
            $query = "INSERT INTO users (nama, username, email, no_hp, password, role) 
                      VALUES ('$nama', '$username', '$email', '$no_hp', '$password', 'user')";
            
            if (mysqli_query($conn, $query)) {
                $success = "✅ Registrasi berhasil! Silakan login.";
            } else {
                $error = "❌ Registrasi gagal: " . mysqli_error($conn);
            }
        }
    }
}

$title = 'Register - Tsubasa Arena';
include 'includes/header.php';
?>

<div class="container" style="max-width: 500px; margin: 60px auto;">
    <div style="background: white; border-radius: 24px; padding: 35px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-top: 5px solid #e63946;">
        
        <h2 style="text-align: center; color: #0a2b4e; margin-bottom: 30px;">⚡ REGISTER ⚡</h2>
        
        <?php if ($error): ?>
            <div style="background: #fee; color: #e63946; padding: 12px; border-radius: 12px; margin-bottom: 20px; text-align: center;">
                <?= $error ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 12px; margin-bottom: 20px; text-align: center;">
                <?= $success ?> <a href="login.php" style="color: #e63946;">Login di sini</a>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div style="margin-bottom: 18px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">👤 Nama Lengkap</label>
                <input type="text" name="nama" required style="width: 100%; padding: 14px; border: 2px solid #e1e1e1; border-radius: 12px;">
            </div>
            
            <div style="margin-bottom: 18px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">🔑 Username</label>
                <input type="text" name="username" required placeholder="Username untuk login" style="width: 100%; padding: 14px; border: 2px solid #e1e1e1; border-radius: 12px;">
            </div>
            
            <div style="margin-bottom: 18px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">📧 Email</label>
                <input type="email" name="email" required style="width: 100%; padding: 14px; border: 2px solid #e1e1e1; border-radius: 12px;">
            </div>
            
            <div style="margin-bottom: 18px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">📱 No. HP</label>
                <input type="text" name="no_hp" style="width: 100%; padding: 14px; border: 2px solid #e1e1e1; border-radius: 12px;">
            </div>
            
            <div style="margin-bottom: 18px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">🔒 Password</label>
                <input type="password" name="password" required style="width: 100%; padding: 14px; border: 2px solid #e1e1e1; border-radius: 12px;">
            </div>
            
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">🔒 Konfirmasi Password</label>
                <input type="password" name="confirm_password" required style="width: 100%; padding: 14px; border: 2px solid #e1e1e1; border-radius: 12px;">
            </div>
            
            <button type="submit" style="width: 100%; padding: 14px; background: linear-gradient(135deg, #e63946 0%, #c1121f 100%); color: white; border: none; border-radius: 30px; font-size: 16px; font-weight: 600; cursor: pointer;">⚡ REGISTER ⚡</button>
        </form>
        
        <p style="text-align: center; margin-top: 25px;">
            Sudah punya akun? <a href="login.php" style="color: #e63946; text-decoration: none; font-weight: 600;">Login di sini</a>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>