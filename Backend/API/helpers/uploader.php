<?php
// ============================================================
// helpers/uploader.php
// Fungsi upload file
// ============================================================

if (!function_exists('handleImageUpload')) {
    /**
     * Handle upload gambar dengan validasi lengkap
     * 
     * @param array $file $_FILES['field_name']
     * @param string $upload_dir Folder tujuan (absolute path)
     * @param string $prefix Prefix nama file
     * @param int $max_size Max size in bytes
     * @return array ['success' => bool, 'filename' => string, 'error' => string]
     */
    function handleImageUpload($file, $upload_dir, $prefix = 'img', $max_size = 2097152) {
        // Cek error upload
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'File tidak valid atau gagal diupload'];
        }
        
        // Cek ukuran
        if ($file['size'] > $max_size) {
            $max_mb = round($max_size / 1024 / 1024, 1);
            return ['success' => false, 'error' => "Ukuran file maksimal {$max_mb}MB"];
        }
        
        // Cek MIME type
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if (!in_array($file['type'], $allowed_types)) {
            return ['success' => false, 'error' => 'Format harus JPG, PNG, atau WEBP'];
        }
        
        // Cek ekstensi
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $allowed_exts)) {
            return ['success' => false, 'error' => 'Ekstensi file tidak valid'];
        }
        
        // Cek apakah benar-benar gambar
        $image_info = @getimagesize($file['tmp_name']);
        if ($image_info === false) {
            return ['success' => false, 'error' => 'File bukan gambar valid'];
        }
        
        // Buat folder jika belum ada
        if (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                return ['success' => false, 'error' => 'Gagal membuat folder upload'];
            }
        }
        
        // Generate nama file unik
        $filename = $prefix . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $target = $upload_dir . $filename;
        
        // Upload
        if (!move_uploaded_file($file['tmp_name'], $target)) {
            return ['success' => false, 'error' => 'Gagal menyimpan file'];
        }
        
        return ['success' => true, 'filename' => $filename];
    }
}

if (!function_exists('handleDocumentUpload')) {
    /**
     * Handle upload dokumen (JPG, PNG, PDF)
     */
    function handleDocumentUpload($file, $upload_dir, $prefix = 'doc', $max_size = 5242880) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'File tidak valid'];
        }
        
        if ($file['size'] > $max_size) {
            $max_mb = round($max_size / 1024 / 1024, 1);
            return ['success' => false, 'error' => "Ukuran maksimal {$max_mb}MB"];
        }
        
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        if (!in_array($file['type'], $allowed_types)) {
            return ['success' => false, 'error' => 'Format harus JPG, PNG, atau PDF'];
        }
        
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'pdf'];
        if (!in_array($ext, $allowed_exts)) {
            return ['success' => false, 'error' => 'Ekstensi tidak valid'];
        }
        
        if (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                return ['success' => false, 'error' => 'Gagal membuat folder'];
            }
        }
        
        $filename = $prefix . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $target = $upload_dir . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $target)) {
            return ['success' => false, 'error' => 'Gagal menyimpan file'];
        }
        
        return ['success' => true, 'filename' => $filename];
    }
}

if (!function_exists('deleteFile')) {
    /**
     * Hapus file jika ada
     */
    function deleteFile($path) {
        if (!empty($path) && file_exists($path)) {
            return @unlink($path);
        }
        return false;
    }
}
?>