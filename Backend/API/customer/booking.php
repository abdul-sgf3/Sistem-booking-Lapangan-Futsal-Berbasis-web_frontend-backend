<?php
// ============================================================
// customer/booking.php - Booking User
//
// POST /API/customer/booking.php  (atau POST /API/bookings) -> Buat booking baru
// GET  /API/customer/booking.php  (atau GET  /API/bookings) -> Riwayat booking user
// ============================================================

require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    sendError('Anda belum login. Silakan login terlebih dahulu.', 401);
}

$id_user = (int)$_SESSION['user_id'];
$method  = $_SERVER['REQUEST_METHOD'];

// ============================================================
// POST: Buat Booking Baru
// ============================================================
if ($method === 'POST') {
    $input = getJsonInput();   // JSON atau multipart/form-data

    $id_lapangan = (int)($input['id_lapangan'] ?? 0);
    $tanggal     = trim($input['tanggal'] ?? '');
    $jam_mulai   = trim($input['jam_mulai'] ?? '');
    $durasi      = (int)($input['durasi'] ?? 1);
    $catatan     = trim($input['catatan'] ?? '');
    $metode      = strtolower(trim($input['metode_pembayaran'] ?? 'transfer'));

    // ===== VALIDASI =====
    if ($id_lapangan <= 0) sendError('Lapangan tidak valid', 400);
    if (!validateDate($tanggal)) sendError('Format tanggal tidak valid (YYYY-MM-DD)', 400);
    $start_min = timeToMinutes($jam_mulai);
    if ($start_min === null) sendError('Format jam tidak valid (HH:MM)', 400);
    if ($durasi < 1 || $durasi > 5) sendError('Durasi harus antara 1-5 jam', 400);
    if (!preg_match('/^[a-z0-9 _-]{1,50}$/', $metode)) $metode = 'transfer';
    if (mb_strlen($catatan) > 500) sendError('Catatan maksimal 500 karakter', 400);

    if ($tanggal < date('Y-m-d')) {
        sendError('Tanggal tidak boleh di masa lalu', 400);
    }
    if ($tanggal === date('Y-m-d') && $start_min <= ((int)date('G') * 60 + (int)date('i'))) {
        sendError('Jam booking sudah lewat. Pilih jam yang masih tersedia.', 400);
    }

    $end_min = $start_min + ($durasi * 60);
    if ($end_min > 24 * 60) sendError('Booking tidak boleh melewati tengah malam', 400);

    // ===== PENGATURAN (jam operasional & maksimal durasi) =====
    $setting = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pengaturan LIMIT 1"));
    if ($setting) {
        $buka_min  = timeToMinutes($setting['jam_buka']);
        $tutup_min = timeToMinutes($setting['jam_tutup']);
        if ($tutup_min === 0) $tutup_min = 24 * 60;
        $maks      = (int)$setting['maksimal_durasi'];

        if ($start_min < $buka_min || $end_min > $tutup_min) {
            sendError('Jam booking harus antara ' . substr($setting['jam_buka'], 0, 5) . ' - ' . substr($setting['jam_tutup'], 0, 5)
                . ' (termasuk durasi bermain)', 400);
        }
        if ($maks > 0 && $durasi > $maks) {
            sendError("Maksimal durasi $maks jam", 400);
        }
    }

    $jam_mulai_db = minutesToTime($start_min);
    $jam_selesai  = minutesToTime($end_min);

    // ===== UPLOAD BUKTI (opsional) — divalidasi SEBELUM data disimpan =====
    $bukti_filename = null;
    $uploaded_at    = null;
    if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] !== UPLOAD_ERR_NO_FILE) {
        $up = handleDocumentUpload($_FILES['bukti'], ABS_UPLOAD_BUKTI, 'bukti');
        if (!$up['success']) sendError($up['error'], 400);
        $bukti_filename = $up['filename'];
        $uploaded_at    = date('Y-m-d H:i:s');
    }

    // ===== TRANSAKSI: kunci lapangan agar tidak terjadi double-booking =====
    mysqli_begin_transaction($conn);
    try {
        $stmt = mysqli_prepare($conn,
            "SELECT id, nama_lapangan, harga_per_jam FROM lapangan
              WHERE id = ? AND status = 'aktif' LIMIT 1 FOR UPDATE");
        mysqli_stmt_bind_param($stmt, "i", $id_lapangan);
        mysqli_stmt_execute($stmt);
        $lapangan = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if (!$lapangan) {
            mysqli_rollback($conn);
            if ($bukti_filename) deleteFile(ABS_UPLOAD_BUKTI . $bukti_filename);
            sendError('Lapangan tidak ditemukan atau tidak aktif', 404);
        }

        $harga_per_jam = (int)$lapangan['harga_per_jam'];
        $total_harga   = $harga_per_jam * $durasi;

        $bentrok = findBookingConflicts($conn, $id_lapangan, $tanggal, $jam_mulai_db, $jam_selesai);
        if (!empty($bentrok)) {
            mysqli_rollback($conn);
            if ($bukti_filename) deleteFile(ABS_UPLOAD_BUKTI . $bukti_filename);
            sendErrorData('Jadwal sudah dibooking. Silakan pilih jam lain.', [
                'tersedia' => false,
                'bentrok'  => $bentrok,
            ], 409);
        }

        // Kode booking unik
        $kode_booking = '';
        for ($try = 0; $try < 10; $try++) {
            $kode_booking = 'BSK' . date('Ymd') . str_pad((string)random_int(1, 9999), 4, '0', STR_PAD_LEFT);
            $cek = mysqli_prepare($conn, "SELECT id FROM booking WHERE kode_booking = ?");
            mysqli_stmt_bind_param($cek, "s", $kode_booking);
            mysqli_stmt_execute($cek);
            if (mysqli_num_rows(mysqli_stmt_get_result($cek)) === 0) break;
        }

        // ===== INSERT BOOKING =====
        // 11 parameter: id_user(i) id_lapangan(i) kode(s) tanggal(s) jam(s) durasi(i) total(i) catatan(s) metode(s) bukti(s) uploaded_at(s)
        $stmt = mysqli_prepare($conn,
            "INSERT INTO booking
                (id_user, id_lapangan, kode_booking, tanggal, jam_mulai, durasi, total_harga,
                 catatan, metode_pembayaran, bukti_pembayaran, uploaded_at, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        mysqli_stmt_bind_param($stmt, "iisssiissss",
            $id_user, $id_lapangan, $kode_booking, $tanggal, $jam_mulai_db,
            $durasi, $total_harga, $catatan, $metode, $bukti_filename, $uploaded_at);
        mysqli_stmt_execute($stmt);
        $booking_id = (int)mysqli_insert_id($conn);

        mysqli_commit($conn);
    } catch (Throwable $e) {
        mysqli_rollback($conn);
        if ($bukti_filename) deleteFile(ABS_UPLOAD_BUKTI . $bukti_filename);
        throw $e;
    }

    sendResponse([
        'booking_id'       => $booking_id,
        'kode_booking'     => $kode_booking,
        'lapangan'         => $lapangan['nama_lapangan'],
        'tanggal'          => $tanggal,
        'jam_mulai'        => substr($jam_mulai_db, 0, 5),
        'jam_selesai'      => substr($jam_selesai, 0, 5),
        'durasi'           => $durasi,
        'harga_per_jam'    => $harga_per_jam,
        'total_harga'      => $total_harga,
        'status'           => 'pending',
        'bukti_pembayaran' => $bukti_filename,
        'bukti_url'        => buktiUrl($bukti_filename),
        'redirect'         => 'riwayat.html',
    ], 'Booking berhasil dibuat', 201);
}

// ============================================================
// GET: Riwayat Booking User
// ============================================================
elseif ($method === 'GET') {
    $status = $_GET['status'] ?? 'all';
    $limit  = isset($_GET['limit']) ? max(1, min(50, (int)$_GET['limit'])) : 20;
    $page   = isset($_GET['page'])  ? max(1, (int)$_GET['page']) : 1;
    $offset = ($page - 1) * $limit;

    if ($status !== 'all' && !in_array($status, ['pending', 'confirmed', 'completed', 'canceled'], true)) {
        sendError('Filter status tidak valid', 400);
    }

    $sql = "SELECT b.id, b.kode_booking, b.tanggal, b.jam_mulai, b.durasi,
                   b.total_harga, b.catatan, b.bukti_pembayaran, b.status, b.created_at,
                   l.id AS id_lapangan, l.nama_lapangan, l.foto AS foto_lapangan
              FROM booking b
              JOIN lapangan l ON b.id_lapangan = l.id
             WHERE b.id_user = ?";
    $types  = "i";
    $params = [$id_user];

    if ($status !== 'all') {
        $sql .= " AND b.status = ?";
        $types .= "s";
        $params[] = $status;
    }
    $sql .= " ORDER BY b.created_at DESC, b.id DESC LIMIT $limit OFFSET $offset";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $status_map = [
        'pending'   => ['label' => 'Menunggu',     'color' => 'warning'],
        'confirmed' => ['label' => 'Dikonfirmasi', 'color' => 'success'],
        'completed' => ['label' => 'Selesai',      'color' => 'info'],
        'canceled'  => ['label' => 'Dibatalkan',   'color' => 'error'],
    ];

    $bookings = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['id']          = (int)$row['id'];
        $row['id_lapangan'] = (int)$row['id_lapangan'];
        $row['durasi']      = (int)$row['durasi'];
        $row['total_harga'] = (int)$row['total_harga'];
        $row['jam_mulai_formatted']     = substr($row['jam_mulai'], 0, 5) . ' WIB';
        $row['jam_selesai_formatted']   = substr(minutesToTime(timeToMinutes($row['jam_mulai']) + $row['durasi'] * 60), 0, 5) . ' WIB';
        $row['tanggal_formatted']       = date('d/m/Y', strtotime($row['tanggal']));
        $row['total_harga_formatted']   = 'Rp ' . number_format($row['total_harga'], 0, ',', '.');
        $row['foto_lapangan_url']       = fotoLapanganUrl($row['foto_lapangan']);
        $row['bukti_url']               = buktiUrl($row['bukti_pembayaran'] ?? '');
        $row['status_label']            = $status_map[$row['status']]['label'] ?? $row['status'];
        $row['status_color']            = $status_map[$row['status']]['color'] ?? 'default';
        $bookings[] = $row;
    }

    // Total untuk pagination
    if ($status !== 'all') {
        $cs = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM booking WHERE id_user = ? AND status = ?");
        mysqli_stmt_bind_param($cs, "is", $id_user, $status);
    } else {
        $cs = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM booking WHERE id_user = ?");
        mysqli_stmt_bind_param($cs, "i", $id_user);
    }
    mysqli_stmt_execute($cs);
    $total = (int)mysqli_fetch_assoc(mysqli_stmt_get_result($cs))['total'];

    sendResponse([
        'bookings'   => $bookings,
        'pagination' => [
            'page'        => $page,
            'limit'       => $limit,
            'total'       => $total,
            'total_pages' => (int)ceil($total / $limit),
        ],
    ], 'Riwayat booking berhasil diambil');
}

else {
    sendError('Method tidak diizinkan. Gunakan GET atau POST.', 405);
}
?>
