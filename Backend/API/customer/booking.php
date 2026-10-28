<?php
// ============================================================
// customer/booking.php - Booking User
// 
// POST /api/customer/booking.php → Buat booking baru
// GET  /api/customer/booking.php → Lihat riwayat booking user
// ============================================================

require_once '../config.php';

// Cek login (baik GET maupun POST)
if (!isset($_SESSION['user_id'])) {
    sendError('Anda belum login. Silakan login terlebih dahulu.', 401);
}

$id_user = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

// ============================================================
// POST: Buat Booking Baru
// ============================================================
if ($method === 'POST') {
    // Ambil input (support JSON & Form Data)
    $content_type = $_SERVER['CONTENT_TYPE'] ?? '';
    
    if (strpos($content_type, 'application/json') !== false) {
        $input = getJsonInput();
    } else {
        $input = $_POST; // Form data biasa
    }
    
    $id_lapangan = (int)($input['id_lapangan'] ?? 0);
    $tanggal     = trim($input['tanggal'] ?? '');
    $jam_mulai   = trim($input['jam_mulai'] ?? '');
    $durasi      = (int)($input['durasi'] ?? 1);
    $catatan     = trim($input['catatan'] ?? '');
    $metode_pembayaran = $input['metode_pembayaran'] ?? 'transfer';
    
    // ===== VALIDASI =====
    if ($id_lapangan <= 0) {
        sendError('Lapangan tidak valid', 400);
    }
    if (empty($tanggal) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
        sendError('Format tanggal tidak valid (YYYY-MM-DD)', 400);
    }
    if (empty($jam_mulai) || !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $jam_mulai)) {
        sendError('Format jam tidak valid (HH:MM)', 400);
    }
    if ($durasi < 1 || $durasi > 5) {
        sendError('Durasi harus antara 1-5 jam', 400);
    }
    
    // Tanggal tidak boleh masa lalu
    if (strtotime($tanggal) < strtotime(date('Y-m-d'))) {
        sendError('Tanggal tidak boleh di masa lalu', 400);
    }
    
    // ===== AMBIL PENGATURAN =====
    $setting = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pengaturan LIMIT 1"));
    if ($setting) {
        $jam_buka = substr($setting['jam_buka'], 0, 5);
        $jam_tutup = substr($setting['jam_tutup'], 0, 5);
        $maks_durasi = (int)$setting['maksimal_durasi'];
        
        $jam_mulai_short = substr($jam_mulai, 0, 5);
        
        if ($jam_mulai_short < $jam_buka || $jam_mulai_short > $jam_tutup) {
            sendError("Jam booking harus antara $jam_buka - $jam_tutup", 400);
        }
        if ($maks_durasi > 0 && $durasi > $maks_durasi) {
            sendError("Maksimal durasi $maks_durasi jam", 400);
        }
    }
    
    // ===== AMBIL HARGA LAPANGAN =====
    $stmt = mysqli_prepare($conn, 
        "SELECT id, nama_lapangan, harga_per_jam FROM lapangan 
         WHERE id = ? AND status = 'aktif' LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id_lapangan);
    mysqli_stmt_execute($stmt);
    $lapangan = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    
    if (!$lapangan) {
        sendError('Lapangan tidak ditemukan atau tidak aktif', 404);
    }
    
    $harga_per_jam = (int)$lapangan['harga_per_jam'];
    $total_harga   = $harga_per_jam * $durasi;
    
    // ===== CEK BENTROK JADWAL =====
    // Overlap terjadi jika:
    // (jam_mulai_baru < jam_selesai_lama) AND (jam_selesai_baru > jam_mulai_lama)
    $jam_selesai = date('H:i:s', strtotime($jam_mulai) + ($durasi * 3600));
    $jam_mulai_db = date('H:i:s', strtotime($jam_mulai));
    
    $stmt = mysqli_prepare($conn,
        "SELECT id, kode_booking, jam_mulai, durasi 
         FROM booking 
         WHERE id_lapangan = ? 
           AND tanggal = ? 
           AND status NOT IN ('canceled', 'completed')
           AND (jam_mulai < ? AND ADDTIME(jam_mulai, SEC_TO_TIME(durasi * 3600)) > ?)");
    mysqli_stmt_bind_param($stmt, "isss", $id_lapangan, $tanggal, $jam_selesai, $jam_mulai_db);
    mysqli_stmt_execute($stmt);
    $bentrok_result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($bentrok_result) > 0) {
        $bentrok_list = [];
        while ($b = mysqli_fetch_assoc($bentrok_result)) {
            $bentrok_list[] = [
                'kode_booking' => $b['kode_booking'],
                'jam_mulai' => substr($b['jam_mulai'], 0, 5),
                'durasi' => (int)$b['durasi']
            ];
        }
        sendResponse([
            'tersedia' => false,
            'bentrok' => $bentrok_list
        ], 'Jadwal sudah dibooking. Silakan pilih jam lain.', 409);
    }
    
    // ===== GENERATE KODE BOOKING =====
    $kode_booking = 'BSK' . date('Ymd') . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
    
    // Pastikan kode unik
    $max_try = 5;
    while ($max_try > 0) {
        $cek = mysqli_prepare($conn, "SELECT id FROM booking WHERE kode_booking = ?");
        mysqli_stmt_bind_param($cek, "s", $kode_booking);
        mysqli_stmt_execute($cek);
        if (mysqli_stmt_get_result($cek)->num_rows === 0) break;
        $kode_booking = 'BSK' . date('Ymd') . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        $max_try--;
    }
    
    // ===== INSERT BOOKING =====
    $stmt = mysqli_prepare($conn,
        "INSERT INTO booking 
         (id_user, id_lapangan, kode_booking, tanggal, jam_mulai, durasi, total_harga, catatan, metode_pembayaran, status) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
mysqli_stmt_bind_param($stmt, "iisssiiiss", 
    $id_user, $id_lapangan, $kode_booking, $tanggal, 
    $jam_mulai, $durasi, $total_harga, $catatan, $metode_pembayaran);
    
    if (!mysqli_stmt_execute($stmt)) {
        sendError('Gagal membuat booking: ' . mysqli_error($conn), 500);
    }
    
    $booking_id = mysqli_insert_id($conn);
    
    // ===== HANDLE UPLOAD BUKTI (jika ada) =====
    $bukti_filename = null;
    if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($_FILES['bukti']['type'], $allowed)) {
            sendError('Format bukti harus JPG, PNG, atau PDF', 400);
        }
        if ($_FILES['bukti']['size'] > $max_size) {
            sendError('Ukuran bukti maksimal 5MB', 400);
        }
        
        $upload_dir = __DIR__ . '/../../Frontend/assets/uploads/bukti/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $ext = strtolower(pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION));
        $bukti_filename = 'bukti_' . $booking_id . '_' . time() . '.' . $ext;
        
        if (move_uploaded_file($_FILES['bukti']['tmp_name'], $upload_dir . $bukti_filename)) {
            $stmt = mysqli_prepare($conn, 
                "UPDATE booking SET bukti_pembayaran = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "si", $bukti_filename, $booking_id);
            mysqli_stmt_execute($stmt);
        }
    }
    
    // ===== RESPONSE =====
    sendResponse([
        'booking_id' => $booking_id,
        'kode_booking' => $kode_booking,
        'lapangan' => $lapangan['nama_lapangan'],
        'tanggal' => $tanggal,
        'jam_mulai' => $jam_mulai,
        'durasi' => $durasi,
        'harga_per_jam' => $harga_per_jam,
        'total_harga' => $total_harga,
        'status' => 'pending',
        'bukti_pembayaran' => $bukti_filename,
        'redirect' => '/pages/riwayat_booking.php'
    ], 'Booking berhasil dibuat');
}

// ============================================================
// GET: Lihat Riwayat Booking User
// ============================================================
elseif ($method === 'GET') {
    // Filter opsional
    $status = $_GET['status'] ?? 'all';
    $limit  = isset($_GET['limit']) ? max(1, min(50, (int)$_GET['limit'])) : 20;
    $page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $offset = ($page - 1) * $limit;
    
    $sql = "SELECT 
                b.id, b.kode_booking, b.tanggal, b.jam_mulai, b.durasi, 
                b.total_harga, b.catatan, b.bukti_pembayaran, b.status, b.created_at,
                l.id AS id_lapangan, l.nama_lapangan, l.foto AS foto_lapangan
            FROM booking b 
            JOIN lapangan l ON b.id_lapangan = l.id 
            WHERE b.id_user = ?";
    
    $params = [$id_user];
    $types = "i";
    
    if ($status !== 'all') {
        $sql .= " AND b.status = ?";
        $params[] = $status;
        $types .= "s";
    }
    
    $sql .= " ORDER BY b.created_at DESC LIMIT $limit OFFSET $offset";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $bookings = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['id'] = (int)$row['id'];
        $row['id_lapangan'] = (int)$row['id_lapangan'];
        $row['durasi'] = (int)$row['durasi'];
        $row['total_harga'] = (int)$row['total_harga'];
        $row['jam_mulai_formatted'] = substr($row['jam_mulai'], 0, 5) . ' WIB';
        $row['tanggal_formatted'] = date('d/m/Y', strtotime($row['tanggal']));
        $row['total_harga_formatted'] = 'Rp ' . number_format($row['total_harga'], 0, ',', '.');
        
        // URL foto & bukti
        $row['foto_lapangan_url'] = fotoLapanganUrl($row['foto_lapangan']);
        $row['bukti_url'] = buktiUrl($row['bukti_pembayaran'] ?? '');
        
        // Status label & warna (untuk frontend)
        $status_map = [
            'pending'   => ['label' => 'Menunggu', 'color' => 'warning'],
            'confirmed' => ['label' => 'Dikonfirmasi', 'color' => 'success'],
            'completed' => ['label' => 'Selesai', 'color' => 'info'],
            'canceled'  => ['label' => 'Dibatalkan', 'color' => 'error'],
        ];
        $row['status_label'] = $status_map[$row['status']]['label'] ?? $row['status'];
        $row['status_color'] = $status_map[$row['status']]['color'] ?? 'default';
        
        $bookings[] = $row;
    }
    
    // Hitung total untuk pagination
    $count_sql = "SELECT COUNT(*) as total FROM booking WHERE id_user = ?";
    if ($status !== 'all') $count_sql .= " AND status = ?";
    $count_stmt = mysqli_prepare($conn, $count_sql);
    if ($status !== 'all') {
        mysqli_stmt_bind_param($count_stmt, "is", $id_user, $status);
    } else {
        mysqli_stmt_bind_param($count_stmt, "i", $id_user);
    }
    mysqli_stmt_execute($count_stmt);
    $total = mysqli_fetch_assoc(mysqli_stmt_get_result($count_stmt))['total'];
    
    sendResponse([
        'bookings' => $bookings,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => (int)$total,
            'total_pages' => ceil($total / $limit)
        ]
    ], 'Riwayat booking berhasil diambil');
}

// ============================================================
// Method lain tidak diizinkan
// ============================================================
else {
    sendError('Method tidak diizinkan. Gunakan GET atau POST.', 405);
}
?>