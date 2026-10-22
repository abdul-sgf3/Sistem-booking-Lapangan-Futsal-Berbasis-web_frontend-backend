<?php
// ============================================================
// auth/logout.php - Logout untuk User DAN Admin
// POST /api/auth/logout.php
// 
// Body (opsional): { type: "user" | "admin" | "all" }
// Default: "all" (logout semua)
// ============================================================

require_once '../config.php';

requireMethod('POST');

$input = getJsonInput();
$type = $input['type'] ?? $_GET['type'] ?? 'all'; // user | admin | all

$nama = '';

switch ($type) {
    case 'user':
        // Logout user saja (admin session tetap)
        $nama = $_SESSION['nama'] ?? 'User';
        unset($_SESSION['user_id'], $_SESSION['user_email'], 
              $_SESSION['nama'], $_SESSION['whatsapp'], 
              $_SESSION['foto_profil']);
        
        // Kalau tidak ada admin session, destroy semua
        if (!isset($_SESSION['admin_id'])) {
            session_destroy();
        }
        
        sendResponse([
            'redirect' => BASE_URL . '/Frontend/pages/index.php'
        ], "Sampai jumpa, $nama! Logout berhasil.");
        break;
    
    case 'admin':
        // Logout admin saja
        $nama = $_SESSION['admin_nama'] ?? 'Admin';
        unset($_SESSION['admin_id'], $_SESSION['admin_email'], 
              $_SESSION['admin_nama']);
        
        if (!isset($_SESSION['user_id'])) {
            session_destroy();
        }
        
        sendResponse([
            'redirect' => BASE_URL . '/Frontend/admin/login.php'
        ], "Logout admin berhasil.");
        break;
    
    case 'all':
    default:
        // Logout semua
        $nama = $_SESSION['nama'] ?? $_SESSION['admin_nama'] ?? 'User';
        
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        
        sendResponse([
            'redirect' => BASE_URL . '/Frontend/pages/index.php'
        ], "Logout berhasil.");
        break;
}
?>