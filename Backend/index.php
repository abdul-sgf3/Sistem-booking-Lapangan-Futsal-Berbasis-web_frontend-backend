<?php
header('Content-Type: application/json; charset=UTF-8');
echo json_encode(['status' => 'success', 'message' => 'Vincent SQ Arena API berjalan', 'data' => ['api' => '/API/', 'health' => '/API/health.php']], JSON_UNESCAPED_SLASHES);
