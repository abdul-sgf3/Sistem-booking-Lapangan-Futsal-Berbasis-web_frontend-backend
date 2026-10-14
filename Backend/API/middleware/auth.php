<?php
// ============================================================
// middleware/auth.php
// Middleware untuk cek login
// ============================================================

if (!function_exists('requireLogin')) {
    function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            sendError('Anda belum login. Silakan login terlebih dahulu.', 401);
        }
        return $_SESSION['user_id'];
    }
}

if (!function_exists('requireAdmin')) {
    function requireAdmin() {
        if (!isset($_SESSION['admin_id'])) {
            sendError('Akses ditolak. Anda bukan admin.', 403);
        }
        return $_SESSION['admin_id'];
    }
}

if (!function_exists('optionalLogin')) {
    function optionalLogin() {
        return $_SESSION['user_id'] ?? null;
    }
}

if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('getCurrentUser')) {
    function getCurrentUser($conn) {
        if (!isset($_SESSION['user_id'])) return null;
        
        $id = (int)$_SESSION['user_id'];
        $stmt = mysqli_prepare($conn, 
            "SELECT id, email, nama, whatsapp, foto_profil, role, is_verified 
             FROM users WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    }
}
?>