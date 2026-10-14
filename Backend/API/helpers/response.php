<?php
// ============================================================
// helpers/response.php
// Fungsi standar untuk response API
// ============================================================

if (!function_exists('sendResponse')) {
    function sendResponse($data = null, $message = 'Success', $code = 200) {
        http_response_code($code);
        echo json_encode([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }
}

if (!function_exists('sendError')) {
    function sendError($message, $code = 400) {
        http_response_code($code);
        echo json_encode([
            'status'  => 'error',
            'message' => $message,
            'data'    => null
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }
}

if (!function_exists('sendValidationError')) {
    function sendValidationError($errors, $message = 'Validasi gagal') {
        http_response_code(422);
        echo json_encode([
            'status'  => 'error',
            'message' => $message,
            'errors'  => $errors,
            'data'    => null
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }
}
?>