<?php
// proses_verifikasi.php - Placeholder FE
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_input = $_POST['otp_code'] ?? '';
    
    // SEMENTARA: Kita anggap kode "123456" adalah kode yang benar
    // (Nanti ini akan diganti dengan pengecekan ke Database oleh BE)
    $kode_benar = '123456'; 

    if ($kode_input === $kode_benar) {
        // Jika benar, arahkan ke halaman lengkapi profil atau login
        $_SESSION['register_success'] = "Verifikasi berhasil!";
        header("Location: lengkapi_profil.php");
        exit;
    } else {
        // Jika salah, kembalikan ke halaman verifikasi dengan pesan error
        $_SESSION['verify_error'] = "Kode OTP salah! Silakan coba lagi.";
        header("Location: register_sesi2.php");
        exit;
    }
} else {
    header("Location: register_sesi2.php");
    exit;
}
?>