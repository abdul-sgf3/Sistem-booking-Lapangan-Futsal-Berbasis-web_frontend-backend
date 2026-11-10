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
    if (empty($expired_at)) {
        return true;
    }

    $expired_timestamp = strtotime($expired_at);

    if ($expired_timestamp === false) {
        return true;
    }

    return $expired_timestamp <= time();
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
            "UPDATE users
             SET otp_code = ?, otp_expired_at = ?
             WHERE id = ?"
        );

        if (!$stmt) {
            error_log('[OTP] Gagal prepare save OTP: ' . mysqli_error($conn));
            return false;
        }

        mysqli_stmt_bind_param(
            $stmt,
            "ssi",
            $otp,
            $expired_at,
            $user_id
        );

        $success = mysqli_stmt_execute($stmt);

        if (!$success) {
            error_log('[OTP] Gagal menyimpan OTP: ' . mysqli_stmt_error($stmt));
        }

        mysqli_stmt_close($stmt);

        return $success;
    }
}

if (!function_exists('clearOtpFromUser')) {
    function clearOtpFromUser($conn, $user_id) {

        $stmt = mysqli_prepare($conn,
            "UPDATE users
             SET otp_code = NULL, otp_expired_at = NULL
             WHERE id = ?"
        );

        if (!$stmt) {
            error_log('[OTP] Gagal prepare clear OTP: ' . mysqli_error($conn));
            return false;
        }

        mysqli_stmt_bind_param($stmt, "i", $user_id);

        $success = mysqli_stmt_execute($stmt);

        if (!$success) {
            error_log('[OTP] Gagal menghapus OTP: ' . mysqli_stmt_error($stmt));
        }

        mysqli_stmt_close($stmt);

        return $success;
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

        // Ambil konfigurasi Brevo
        $api_key    = defined('BREVO_API_KEY') ? BREVO_API_KEY : '';
        $from_email = defined('BREVO_FROM_EMAIL') ? BREVO_FROM_EMAIL : '';
        $from_name  = defined('BREVO_FROM_NAME') ? BREVO_FROM_NAME : 'Vincent SQ Arena';

        // Validasi API Key
        if (trim($api_key) === '') {
            error_log('[OTP] BREVO_API_KEY belum dikonfigurasi.');
            return false;
        }

        // Validasi sender
        if (!filter_var($from_email, FILTER_VALIDATE_EMAIL)) {
            error_log('[OTP] BREVO_FROM_EMAIL tidak valid.');
            return false;
        }

        // Validasi email penerima
        $email = trim($email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error_log('[OTP] Email penerima tidak valid.');
            return false;
        }

        // Validasi ekstensi cURL
        if (!function_exists('curl_init')) {
            error_log('[OTP] Ekstensi cURL tidak tersedia.');
            return false;
        }

        // Persiapkan isi email
        $nama_html = htmlspecialchars($nama, ENT_QUOTES, 'UTF-8');
        $otp_html  = htmlspecialchars((string)$otp, ENT_QUOTES, 'UTF-8');

        $isReset = ($purpose === 'reset');

        $subject = $isReset
            ? 'Kode OTP Reset Password - Vincent SQ Arena'
            : 'Kode OTP Verifikasi - Vincent SQ Arena';

        $intro = $isReset
            ? 'Kami menerima permintaan reset password untuk akun Anda. Berikut adalah kode verifikasi Anda:'
            : 'Terima kasih telah mendaftar di Vincent SQ Arena. Berikut adalah kode verifikasi Anda:';

        // Data email Brevo
        $data = [
            'sender' => [
                'name'  => $from_name,
                'email' => $from_email
            ],

            'to' => [[
                'email' => $email,
                'name'  => $nama
            ]],

            'subject' => $subject,

            'htmlContent' => "
                <div style='font-family: Arial, sans-serif; max-width: 500px; margin: auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; background: #FAFAFA;'>

                    <div style='text-align: center; padding-bottom: 15px; border-bottom: 2px solid #477FEB;'>
                        <h1 style='color: #131B2E; margin: 0;'>Vincent'SQ</h1>
                        <p style='color: #FF0000; margin: 5px 0 0 0; font-weight: bold; letter-spacing: 1px;'>
                            SPORTS ARENA
                        </p>
                    </div>

                    <div style='padding: 20px 0;'>
                        <h3 style='color: #131B2E;'>Halo, <strong>$nama_html</strong>!</h3>

                        <p style='color: #333; line-height: 1.6;'>
                            $intro
                        </p>

                        <div style='background: #FFFFFF; padding: 20px; text-align: center; border-radius: 8px; margin: 20px 0; border: 1px dashed #477FEB;'>
                            <span style='font-size: 36px; font-weight: bold; color: #477FEB; letter-spacing: 8px; font-family: monospace;'>
                                $otp_html
                            </span>
                        </div>

                        <p style='color: #666; font-size: 13px;'>
                            Kode ini berlaku selama <strong>" . OTP_VALID_MINUTES . " menit</strong>.
                            Jangan bagikan kode ini kepada siapa pun.
                        </p>
                    </div>

                    <div style='text-align: center; padding-top: 15px; border-top: 1px solid #EEE; font-size: 11px; color: #999;'>
                        &copy; " . date('Y') . " Vincent'SQ Arena. All Rights Reserved.
                    </div>

                </div>
            "
        ];

        // Konversi data menjadi JSON
        $json_data = json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        if ($json_data === false) {
            error_log('[OTP] Gagal mengubah data email menjadi JSON: ' . json_last_error_msg());
            return false;
        }

        // Hubungkan ke Brevo API
        $ch = curl_init('https://api.brevo.com/v3/smtp/email');

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $json_data,
            CURLOPT_CONNECTTIMEOUT => 6,
            CURLOPT_TIMEOUT        => 12,

            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,

            CURLOPT_HTTPHEADER => [
                'api-key: ' . $api_key,
                'Content-Type: application/json',
                'Accept: application/json'
            ]
        ]);

        // Eksekusi pengiriman email
        $response  = curl_exec($ch);
        $http_code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_err  = curl_error($ch);

        curl_close($ch);

        // HTTP 201 berarti Brevo menerima permintaan pengiriman
        $success = ($response !== false && $http_code === 201);

        // Catat hasil pengiriman di Railway Logs
        if ($success) {
            error_log('[OTP] Brevo BERHASIL menerima email ke ' . $email);
        } else {
            error_log(
                '[OTP] Brevo GAGAL mengirim email ke ' . $email .
                ' | HTTP: ' . $http_code .
                ($curl_err ? ' | cURL: ' . $curl_err : '') .
                ' | Response: ' . substr((string)$response, 0, 500)
            );
        }

        return $success;
    }
}
?>
