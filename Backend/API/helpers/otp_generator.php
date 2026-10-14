<?php
// ============================================================
// helpers/otp_generator.php
// Fungsi generate & verifikasi OTP
// ============================================================

if (!function_exists('generateOtp')) {
    /**
     * Generate kode OTP 6 digit
     * @return string
     */
    function generateOtp() {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('getOtpExpiredTime')) {
    /**
     * Hitung waktu kadaluarsa OTP
     * @param int $minutes Menit dari sekarang
     * @return string Format: Y-m-d H:i:s
     */
    function getOtpExpiredTime($minutes = 5) {
        return date('Y-m-d H:i:s', strtotime("+$minutes minutes"));
    }
}

if (!function_exists('isOtpExpired')) {
    /**
     * Cek apakah OTP sudah kadaluarsa
     * @param string $expired_at Format: Y-m-d H:i:s
     * @return bool
     */
    function isOtpExpired($expired_at) {
        if (empty($expired_at)) return true;
        return strtotime($expired_at) < time();
    }
}

if (!function_exists('saveOtpToUser')) {
    /**
     * Simpan OTP ke database user
     */
    function saveOtpToUser($conn, $user_id, $otp, $expired_at) {
        $stmt = mysqli_prepare($conn, 
            "UPDATE users SET otp_code = ?, otp_expired_at = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $otp, $expired_at, $user_id);
        return mysqli_stmt_execute($stmt);
    }
}

if (!function_exists('clearOtpFromUser')) {
    /**
     * Hapus OTP setelah verifikasi berhasil
     */
    function clearOtpFromUser($conn, $user_id) {
        $stmt = mysqli_prepare($conn, 
            "UPDATE users SET otp_code = NULL, otp_expired_at = NULL, verified_at = NOW() WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        return mysqli_stmt_execute($stmt);
    }
}

if (!function_exists('sendOtpEmail')) {
    /**
     * Kirim OTP via email (placeholder)
     * Untuk production, gunakan PHPMailer atau SMTP
     * 
     * @param string $email
     * @param string $otp
     * @param string $nama
     * @return bool
     */
    function sendOtpEmail($email, $otp, $nama = 'User') {
        // TODO: Implementasi kirim email dengan PHPMailer
        // Untuk development, log ke file
        $log = date('Y-m-d H:i:s') . " | OTP untuk $email: $otp\n";
        @file_put_contents(__DIR__ . '/../otp_log.txt', $log, FILE_APPEND);
        
        // Return true (anggap berhasil untuk development)
        return true;
    }
}
?>