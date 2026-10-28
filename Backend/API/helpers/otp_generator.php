<?php
// ============================================================
// helpers/otp_generator.php
// Fungsi generate & verifikasi OTP (INTEGRASI BREVO API)
// ============================================================

if (!function_exists('generateOtp')) {
    function generateOtp() {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('getOtpExpiredTime')) {
    function getOtpExpiredTime($minutes = 5) {
        return date('Y-m-d H:i:s', strtotime("+$minutes minutes"));
    }
}

if (!function_exists('isOtpExpired')) {
    function isOtpExpired($expired_at) {
        if (empty($expired_at)) return true;
        return strtotime($expired_at) < time();
    }
}

if (!function_exists('saveOtpToUser')) {
    function saveOtpToUser($conn, $user_id, $otp, $expired_at) {
        $stmt = mysqli_prepare($conn, 
            "UPDATE users SET otp_code = ?, otp_expired_at = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $otp, $expired_at, $user_id);
        return mysqli_stmt_execute($stmt);
    }
}

if (!function_exists('clearOtpFromUser')) {
    function clearOtpFromUser($conn, $user_id) {
        $stmt = mysqli_prepare($conn, 
            "UPDATE users SET otp_code = NULL, otp_expired_at = NULL, verified_at = NOW() WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        return mysqli_stmt_execute($stmt);
    }
}

// ============================================================
// FUNGSI KIRIM EMAIL VIA BREVO API
// ============================================================
if (!function_exists('sendOtpEmail')) {
    function sendOtpEmail($email, $otp, $nama = 'User') {
        
        // Ambil konfigurasi dari config.php
        $api_key    = defined('BREVO_API_KEY') ? BREVO_API_KEY : '';
        $from_email = defined('BREVO_FROM_EMAIL') ? BREVO_FROM_EMAIL : '';
        $from_name  = defined('BREVO_FROM_NAME') ? BREVO_FROM_NAME : 'Vincent SQ Arena';
        
        // Susun payload sesuai dokumentasi Brevo API v3
        $data = [
            "sender" => [
                "name"  => $from_name,
                "email" => $from_email
            ],
            "to" => [
                [
                    "email" => $email,
                    "name"  => $nama
                ]
            ],
            "subject" => "Kode OTP Verifikasi - Vincent SQ Arena",
            "htmlContent" => "
                <div style='font-family: Arial, sans-serif; max-width: 500px; margin: auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; background: #FAFAFA;'>
                    <div style='text-align: center; padding-bottom: 15px; border-bottom: 2px solid #477FEB;'>
                        <h1 style='color: #131B2E; margin: 0;'>Vincent'SQ</h1>
                        <p style='color: #FF0000; margin: 5px 0 0 0; font-weight: bold; letter-spacing: 1px;'>SPORTS ARENA</p>
                    </div>
                    <div style='padding: 20px 0;'>
                        <h3 style='color: #131B2E;'>Halo, <strong>$nama</strong>!</h3>
                        <p style='color: #333; line-height: 1.6;'>Terima kasih telah mendaftar di Vincent SQ Arena. Berikut adalah kode verifikasi Anda:</p>
                        <div style='background: #FFFFFF; padding: 20px; text-align: center; border-radius: 8px; margin: 20px 0; border: 1px dashed #477FEB;'>
                            <span style='font-size: 36px; font-weight: bold; color: #477FEB; letter-spacing: 8px; font-family: monospace;'>$otp</span>
                        </div>
                        <p style='color: #666; font-size: 13px;'>Kode ini berlaku selama <strong>5 menit</strong>. Jangan bagikan kode ini kepada siapa pun.</p>
                    </div>
                    <div style='text-align: center; padding-top: 15px; border-top: 1px solid #EEE; font-size: 11px; color: #999;'>
                        &copy; 2026 Vincent'SQ Arena. All Rights Reserved.
                    </div>
                </div>
            "
        ];
        
        // Kirim request ke Brevo API menggunakan cURL
        $ch = curl_init('https://api.brevo.com/v3/smtp/email');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'api-key: ' . $api_key,
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        
        $response  = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // Brevo mengembalikan status 201 jika email sukses masuk antrean
        $success = ($http_code === 201);
        
        // Simpan log ke file sebagai backup / debug
        $log_status = $success ? 'TERKIRIM' : 'GAGAL';
        $log = date('Y-m-d H:i:s') . " | OTP untuk $email: $otp | Status: $log_status | Code: $http_code | Response: $response\n";
        @file_put_contents(__DIR__ . '/../otp_log.txt', $log, FILE_APPEND);
        
        return $success;
    }
}
?>