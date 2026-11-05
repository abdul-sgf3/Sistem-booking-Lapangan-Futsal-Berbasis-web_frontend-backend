<?php
// ============================================================
// helpers/otp_generator.php
// Generate & verifikasi OTP + kirim email via Brevo API
// ============================================================

if (!defined('OTP_LENGTH'))        define('OTP_LENGTH', 6);   // 6 digit (kolom DB: varchar(6))
if (!defined('OTP_VALID_MINUTES')) define('OTP_VALID_MINUTES', 5);
if (!defined('OTP_RESEND_COOLDOWN')) define('OTP_RESEND_COOLDOWN', 30); // detik

if (!function_exists('generateOtp')) {
    function generateOtp() {
        return str_pad((string)random_int(0, (10 ** OTP_LENGTH) - 1), OTP_LENGTH, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('getOtpExpiredTime')) {
    function getOtpExpiredTime($minutes = OTP_VALID_MINUTES) {
        return date('Y-m-d H:i:s', strtotime("+$minutes minutes"));
    }
}

if (!function_exists('isOtpExpired')) {
    function isOtpExpired($expired_at) {
        if (empty($expired_at)) return true;
        return strtotime($expired_at) < time();
    }
}

/** Sisa detik cooldown kirim-ulang OTP (0 = boleh kirim). Waktu kirim dihitung dari expired_at - masa berlaku. */
if (!function_exists('otpCooldownRemaining')) {
    function otpCooldownRemaining($expired_at, $cooldown = OTP_RESEND_COOLDOWN) {
        if (empty($expired_at)) return 0;
        $issued_at = strtotime($expired_at) - (OTP_VALID_MINUTES * 60);
        $remain = $cooldown - (time() - $issued_at);
        return $remain > 0 ? (int)$remain : 0;
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
            "UPDATE users SET otp_code = NULL, otp_expired_at = NULL WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        return mysqli_stmt_execute($stmt);
    }
}

/**
 * Batasi percobaan OTP salah per sesi (anti tebak-tebakan).
 * return TRUE jika masih boleh mencoba.
 */
if (!function_exists('otpAttemptAllowed')) {
    function otpAttemptAllowed($key, $max = 5, $window = 600) {
        $k = 'otp_try_' . md5($key);
        $rec = $_SESSION[$k] ?? ['n' => 0, 't' => time()];
        if (time() - $rec['t'] > $window) $rec = ['n' => 0, 't' => time()];
        return $rec['n'] < $max;
    }
}
if (!function_exists('otpAttemptFail')) {
    function otpAttemptFail($key, $window = 600) {
        $k = 'otp_try_' . md5($key);
        $rec = $_SESSION[$k] ?? ['n' => 0, 't' => time()];
        if (time() - $rec['t'] > $window) $rec = ['n' => 0, 't' => time()];
        $rec['n']++;
        $_SESSION[$k] = $rec;
    }
}
if (!function_exists('otpAttemptReset')) {
    function otpAttemptReset($key) {
        unset($_SESSION['otp_try_' . md5($key)]);
    }
}

// ============================================================
// FUNGSI KIRIM EMAIL VIA BREVO API
// $purpose: 'register' | 'reset'
// return TRUE jika Brevo menerima email (HTTP 201)
// ============================================================
if (!function_exists('sendOtpEmail')) {
    function sendOtpEmail($email, $otp, $nama = 'User', $purpose = 'register') {

        $api_key    = defined('BREVO_API_KEY') ? BREVO_API_KEY : '';
        $from_email = defined('BREVO_FROM_EMAIL') ? BREVO_FROM_EMAIL : '';
        $from_name  = defined('BREVO_FROM_NAME') ? BREVO_FROM_NAME : 'Vincent SQ Arena';

        if ($api_key === '') {
            error_log('[OTP] BREVO_API_KEY belum diisi. Email OTP TIDAK terkirim ke ' . $email
                . (defined('OTP_DEBUG') && OTP_DEBUG ? " (OTP: $otp)" : ''));
            return false;
        }
        if (!function_exists('curl_init')) {
            error_log('[OTP] Ekstensi cURL tidak tersedia di PHP.');
            return false;
        }

        $nama_html = htmlspecialchars($nama, ENT_QUOTES, 'UTF-8');
        $isReset   = ($purpose === 'reset');
        $subject   = $isReset ? 'Kode OTP Reset Password - Vincent SQ Arena' : 'Kode OTP Verifikasi - Vincent SQ Arena';
        $intro     = $isReset
            ? 'Kami menerima permintaan reset password untuk akun Anda. Berikut adalah kode verifikasi Anda:'
            : 'Terima kasih telah mendaftar di Vincent SQ Arena. Berikut adalah kode verifikasi Anda:';

        $data = [
            'sender'      => ['name' => $from_name, 'email' => $from_email],
            'to'          => [['email' => $email, 'name' => $nama]],
            'subject'     => $subject,
            'htmlContent' => "
                <div style='font-family: Arial, sans-serif; max-width: 500px; margin: auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; background: #FAFAFA;'>
                    <div style='text-align: center; padding-bottom: 15px; border-bottom: 2px solid #477FEB;'>
                        <h1 style='color: #131B2E; margin: 0;'>Vincent'SQ</h1>
                        <p style='color: #FF0000; margin: 5px 0 0 0; font-weight: bold; letter-spacing: 1px;'>SPORTS ARENA</p>
                    </div>
                    <div style='padding: 20px 0;'>
                        <h3 style='color: #131B2E;'>Halo, <strong>$nama_html</strong>!</h3>
                        <p style='color: #333; line-height: 1.6;'>$intro</p>
                        <div style='background: #FFFFFF; padding: 20px; text-align: center; border-radius: 8px; margin: 20px 0; border: 1px dashed #477FEB;'>
                            <span style='font-size: 36px; font-weight: bold; color: #477FEB; letter-spacing: 8px; font-family: monospace;'>$otp</span>
                        </div>
                        <p style='color: #666; font-size: 13px;'>Kode ini berlaku selama <strong>" . OTP_VALID_MINUTES . " menit</strong>. Jangan bagikan kode ini kepada siapa pun.</p>
                    </div>
                    <div style='text-align: center; padding-top: 15px; border-top: 1px solid #EEE; font-size: 11px; color: #999;'>
                        &copy; " . date('Y') . " Vincent'SQ Arena. All Rights Reserved.
                    </div>
                </div>
            "
        ];

        $ch = curl_init('https://api.brevo.com/v3/smtp/email');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_CONNECTTIMEOUT => 6,
            CURLOPT_TIMEOUT        => 12,
            CURLOPT_HTTPHEADER     => [
                'api-key: ' . $api_key,
                'Content-Type: application/json',
                'Accept: application/json',
            ],
        ]);

        $response  = curl_exec($ch);
        $http_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_err  = curl_error($ch);
        curl_close($ch);

        $success = ($http_code === 201);

        // Log ke stderr (Railway Logs) — OTP hanya dicetak jika mode debug
        error_log('[OTP] Brevo ' . ($success ? 'TERKIRIM' : 'GAGAL') . " ke $email | HTTP $http_code"
            . ($curl_err ? " | cURL: $curl_err" : '')
            . ($success ? '' : ' | Response: ' . substr((string)$response, 0, 300))
            . (defined('OTP_DEBUG') && OTP_DEBUG ? " | OTP: $otp" : ''));

        return $success;
    }
}
?>
