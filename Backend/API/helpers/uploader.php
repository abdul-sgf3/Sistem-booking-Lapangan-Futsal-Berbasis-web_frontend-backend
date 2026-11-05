<?php
// ============================================================
// helpers/uploader.php
// Upload file dengan validasi MIME ASLI (bukan dari header client)
// ============================================================

if (!function_exists('detectUploadMime')) {
    function detectUploadMime($tmp_path) {
        if (function_exists('finfo_open')) {
            $f = finfo_open(FILEINFO_MIME_TYPE);
            if ($f) {
                $mime = finfo_file($f, $tmp_path);
                finfo_close($f);
                if ($mime) return $mime;
            }
        }
        $info = @getimagesize($tmp_path);
        return $info['mime'] ?? '';
    }
}

if (!function_exists('uploadErrorMessage')) {
    function uploadErrorMessage($code) {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE: return 'Ukuran file melebihi batas server';
            case UPLOAD_ERR_PARTIAL:   return 'File hanya terupload sebagian';
            case UPLOAD_ERR_NO_FILE:   return 'Tidak ada file yang diupload';
            default:                   return 'File tidak valid atau gagal diupload';
        }
    }
}

if (!function_exists('saveUploadedFile')) {
    /** Proses inti: cek ukuran, MIME asli, simpan dengan nama acak. */
    function saveUploadedFile($file, $upload_dir, $prefix, $max_size, array $mime_to_ext, $err_format) {
        if (!isset($file) || !is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => uploadErrorMessage($file['error'] ?? UPLOAD_ERR_NO_FILE)];
        }
        if ($file['size'] > $max_size) {
            $max_mb = round($max_size / 1024 / 1024, 1);
            return ['success' => false, 'error' => "Ukuran file maksimal {$max_mb}MB"];
        }

        $mime = detectUploadMime($file['tmp_name']);
        if (!isset($mime_to_ext[$mime])) {
            return ['success' => false, 'error' => $err_format];
        }
        // Gambar harus benar-benar gambar
        if (strpos($mime, 'image/') === 0 && @getimagesize($file['tmp_name']) === false) {
            return ['success' => false, 'error' => 'File bukan gambar valid'];
        }

        if (!is_dir($upload_dir) && !@mkdir($upload_dir, 0775, true) && !is_dir($upload_dir)) {
            error_log('[UPLOAD] Gagal membuat folder: ' . $upload_dir);
            return ['success' => false, 'error' => 'Gagal membuat folder upload'];
        }

        $ext      = $mime_to_ext[$mime];   // ekstensi ditentukan dari MIME asli, bukan nama file user
        $filename = $prefix . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

        if (!move_uploaded_file($file['tmp_name'], rtrim($upload_dir, '/') . '/' . $filename)) {
            error_log('[UPLOAD] move_uploaded_file gagal ke: ' . $upload_dir . ' (cek permission folder)');
            return ['success' => false, 'error' => 'Gagal menyimpan file'];
        }
        return ['success' => true, 'filename' => $filename];
    }
}

if (!function_exists('handleImageUpload')) {
    /**
     * Upload gambar (JPG, PNG, WEBP)
     * @return array ['success'=>bool, 'filename'=>string, 'error'=>string]
     */
    function handleImageUpload($file, $upload_dir, $prefix = 'img', $max_size = 2097152) {
        return saveUploadedFile($file, $upload_dir, $prefix, $max_size, [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ], 'Format harus JPG, PNG, atau WEBP');
    }
}

if (!function_exists('handleDocumentUpload')) {
    /** Upload dokumen (JPG, PNG, PDF) */
    function handleDocumentUpload($file, $upload_dir, $prefix = 'doc', $max_size = 5242880) {
        return saveUploadedFile($file, $upload_dir, $prefix, $max_size, [
            'image/jpeg'      => 'jpg',
            'image/png'       => 'png',
            'application/pdf' => 'pdf',
        ], 'Format harus JPG, PNG, atau PDF');
    }
}

if (!function_exists('deleteFile')) {
    function deleteFile($path) {
        if (!empty($path) && is_file($path)) {
            return @unlink($path);
        }
        return false;
    }
}
?>
