<?php
// ============================================================
// admin/user_status.php - Aktif / nonaktifkan user
// POST /API/admin/user_status.php       Body: { id, is_active: 0|1 }
// POST /API/admin/users/{id}/status     Body: { is_active }
// ============================================================

require_once '../config.php';

requireMethod('POST');
requireAdminLogin();

$input     = getJsonInput();
$id        = getRequestId($input, ['id']);
$is_active = (int)(!empty($input['is_active']) && $input['is_active'] !== '0' && $input['is_active'] !== 'false');

if ($id <= 0) sendError('ID tidak valid', 400);

$stmt = mysqli_prepare($conn, "UPDATE users SET is_active = ? WHERE id = ? AND role = 'user'");
mysqli_stmt_bind_param($stmt, "ii", $is_active, $id);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) === 0) {
    // 0 baris bisa berarti: user tidak ada / bukan role user / status sama
    $cek = mysqli_prepare($conn, "SELECT id FROM users WHERE id = ? AND role = 'user'");
    mysqli_stmt_bind_param($cek, "i", $id);
    mysqli_stmt_execute($cek);
    if (!mysqli_fetch_assoc(mysqli_stmt_get_result($cek))) sendError('User tidak ditemukan', 404);
}

sendResponse(['id' => $id, 'is_active' => $is_active], 'Status user diperbarui');
?>
