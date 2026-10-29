<?php
// File ini hanya untuk mengetes apakah Backend di Railway sudah berjalan
header('Content-Type: application/json');

echo json_encode([
    "status"  => "success",
    "message" => "Backend Vincent SQ Arena berhasil berjalan di Railway!",
    "data"    => [
        "waktu_server" => date('Y-m-d H:i:s'),
        "status"       => "Online"
    ]
], JSON_PRETTY_PRINT);
?>