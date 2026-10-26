<?php
require_once '../config.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $angka1 = random_int(1, 20);
    $angka2 = random_int(1, 20);
    
    $_SESSION['admin_captcha_hasil'] = $angka1 + $angka2;
    $_SESSION['admin_captcha_time'] = time();
    
    sendResponse([
        'angka1' => $angka1,
        'angka2' => $angka2,
        'pertanyaan' => "$angka1 + $angka2 = ?"
    ], 'CAPTCHA admin berhasil digenerate');
}

elseif ($method === 'POST') {
    $input = getJsonInput();
    $jawaban = (int)($input['jawaban'] ?? $input['captcha'] ?? 0);
    
    if (!isset($_SESSION['admin_captcha_hasil'])) {
        sendError('CAPTCHA tidak ditemukan', 400);
    }
    if (time() - $_SESSION['admin_captcha_time'] > 300) {
        unset($_SESSION['admin_captcha_hasil']);
        sendError('CAPTCHA kadaluarsa', 400);
    }
    if ($jawaban !== $_SESSION['admin_captcha_hasil']) {
        sendError('Jawaban CAPTCHA salah', 400);
    }
    
    $_SESSION['admin_captcha_verified'] = true;
    unset($_SESSION['admin_captcha_hasil']);
    sendResponse(['verified' => true], 'CAPTCHA berhasil diverifikasi');
}

else {
    sendError('Method tidak diizinkan', 405);
}
?>