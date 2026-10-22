<?php
require_once '../config.php';
requireMethod('GET');

if (!isset($_SESSION['admin_id'])) sendError('Akses ditolak', 403);

$result = mysqli_query($conn, "SELECT id, nama, email, whatsapp, role, is_active, created_at FROM users WHERE role = 'user' ORDER BY id DESC");
$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}
sendResponse($users, 'Daftar user berhasil diambil');
?>