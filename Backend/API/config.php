<?php
// api/config.php - Konfigurasi API

// Header untuk mengizinkan akses dari mana saja (CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight request (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Koneksi Database
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'tsubasa_arena_db';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Koneksi database gagal: ' . mysqli_connect_error()
    ]);
    exit();
}

// Fungsi bantu untuk response JSON
function sendResponse($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit();
}

// Fungsi bantu untuk response error
function sendError($message, $code = 400) {
    http_response_code($code);
    echo json_encode([
        'status' => 'error',
        'message' => $message
    ], JSON_PRETTY_PRINT);
    exit();
}
?>