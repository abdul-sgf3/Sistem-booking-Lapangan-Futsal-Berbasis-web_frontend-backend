<?php
// ============================================================
// auth/logout.php - Logout untuk User DAN Admin
// POST /API/auth/logout.php
// Body (opsional): { type: "user" | "admin" | "all" }  default: "all"
// ============================================================

require_once '../config.php';

requireMethod('POST');

$input = getJsonInput();
$type  = $input['type'] ?? $_GET['type'] ?? 'all';

function destroyWholeSession() {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires'  => time() - 42000,
            'path'     => $p['path'],
            'domain'   => $p['domain'],
            'secure'   => $p['secure'],
            'httponly' => $p['httponly'],
            'samesite' => $p['samesite'] ?: 'Lax',
        ]);
    }
    session_destroy();
}

switch ($type) {
    case 'user':
        $nama = $_SESSION['nama'] ?? 'User';
        unset($_SESSION['user_id'], $_SESSION['user_email'], $_SESSION['nama'],
              $_SESSION['whatsapp'], $_SESSION['foto_profil']);
        if (!isset($_SESSION['admin_id'])) destroyWholeSession();
        sendResponse(['redirect' => 'index.html'], "Sampai jumpa, $nama! Logout berhasil.");
        break;

    case 'admin':
        unset($_SESSION['admin_id'], $_SESSION['admin_email'], $_SESSION['admin_nama']);
        if (!isset($_SESSION['user_id'])) destroyWholeSession();
        sendResponse(['redirect' => 'login.html'], 'Logout admin berhasil.');
        break;

    case 'all':
    default:
        destroyWholeSession();
        sendResponse(['redirect' => 'index.html'], 'Logout berhasil.');
        break;
}
?>
