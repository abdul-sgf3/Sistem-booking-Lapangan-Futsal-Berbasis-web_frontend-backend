<?php
// ============================================================
// auth/captcha.php - Generate CAPTCHA Sederhana
// GET /api/auth/captcha.php → Generate captcha
// POST /api/auth/captcha.php → Verifikasi captcha
// ============================================================

require_once '../config.php';

$method = $_SERVER['REQUEST_METHOD'];

// ===== GET: Generate CAPTCHA =====
if ($method === 'GET') {
    // Generate 2 angka random
    $angka1 = random_int(1, 20);
    $angka2 = random_int(1, 20);
    
    // Simpan ke session
    $_SESSION['captcha_angka1'] = $angka1;
    $_SESSION['captcha_angka2'] = $angka2;
    $_SESSION['captcha_hasil'] = $angka1 + $angka2;
    $_SESSION['captcha_time'] = time();
    
    sendResponse([
        'angka1' => $angka1,
        'angka2' => $angka2,
        'pertanyaan' => "$angka1 + $angka2 = ?"
    ], 'CAPTCHA berhasil digenerate');
}

// ===== POST: Verifikasi CAPTCHA =====
elseif ($method === 'POST') {
    $input = getJsonInput();
    $jawaban = (int)($input['jawaban'] ?? $input['captcha'] ?? 0);
    
    if (!isset($_SESSION['captcha_hasil'])) {
        sendError('CAPTCHA tidak ditemukan. Refresh halaman.', 400);
    }
    
    // Cek kadaluarsa (5 menit)
    if (time() - $_SESSION['captcha_time'] > 300) {
        unset($_SESSION['captcha_hasil']);
        sendError('CAPTCHA kadaluarsa. Refresh halaman.', 400);
    }
    
    // Cek jawaban
    if ($jawaban !== $_SESSION['captcha_hasil']) {
        sendError('Jawaban CAPTCHA salah', 400);
    }
    
    // Tandai captcha sudah diverifikasi
    $_SESSION['captcha_verified'] = true;
    unset($_SESSION['captcha_hasil']);
    
    sendResponse(['verified' => true], 'CAPTCHA berhasil diverifikasi');
}

else {
    sendError('Method tidak diizinkan', 405);
}
?>