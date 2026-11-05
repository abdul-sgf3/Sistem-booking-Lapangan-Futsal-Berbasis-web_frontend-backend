<?php
header('Content-Type: application/json; charset=UTF-8');
echo json_encode(['status' => 'success', 'message' => 'Vincent SQ Arena API berjalan', 'data' => ['health' => 'health.php']], JSON_UNESCAPED_SLASHES);
