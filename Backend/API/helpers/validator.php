<?php
// ============================================================
// helpers/validator.php
// Fungsi validasi input
// ============================================================

if (!function_exists('validateEmail')) {
    function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (!function_exists('validatePhone')) {
    function validatePhone($phone) {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        return strlen($clean) >= 10 && strlen($clean) <= 15;
    }
}

if (!function_exists('normalizePhone')) {
    function normalizePhone($phone) {
        return preg_replace('/[^0-9]/', '', $phone);
    }
}

if (!function_exists('validateDate')) {
    function validateDate($date) {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) return false;
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}

if (!function_exists('validateTime')) {
    function validateTime($time) {
        return preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $time) === 1;
    }
}

if (!function_exists('validateOtp')) {
    function validateOtp($otp) {
        return preg_match('/^\d{6}$/', $otp) === 1;
    }
}

if (!function_exists('validateDateNotPast')) {
    function validateDateNotPast($date) {
        return strtotime($date) >= strtotime(date('Y-m-d'));
    }
}

if (!function_exists('sanitizeString')) {
    function sanitizeString($str) {
        return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('validateLength')) {
    function validateLength($str, $min, $max) {
        $len = strlen(trim($str));
        return $len >= $min && $len <= $max;
    }
}

/**
 * Aturan password (sama dengan validasi di Frontend register):
 * minimal 8 karakter, ada huruf besar, huruf kecil, dan angka.
 * return string pesan error, atau NULL jika valid.
 */
if (!function_exists('validatePasswordStrength')) {
    function validatePasswordStrength($password) {
        if (strlen($password) < 8) {
            return 'Password minimal 8 karakter.';
        }
        if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            return 'Password harus mengandung huruf besar, huruf kecil, dan angka.';
        }
        return null;
    }
}
?>
