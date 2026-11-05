<?php
// ============================================================
// helpers/booking_helper.php
// Logika bersama booking: validasi jam, hitung selesai, cek bentrok
// ============================================================

if (!function_exists('timeToMinutes')) {
    /** "HH:MM" / "HH:MM:SS" -> menit sejak 00:00. return null jika format salah. */
    function timeToMinutes($time) {
        if (!preg_match('/^(\d{1,2}):(\d{2})(?::\d{2})?$/', trim($time), $m)) return null;
        $h = (int)$m[1]; $i = (int)$m[2];
        if ($h > 23 || $i > 59) return null;
        return $h * 60 + $i;
    }
}

if (!function_exists('minutesToTime')) {
    function minutesToTime($minutes) {
        return sprintf('%02d:%02d:00', intdiv($minutes, 60), $minutes % 60);
    }
}

if (!function_exists('findBookingConflicts')) {
    /**
     * Cari booking / blok jadwal yang bentrok.
     * @param string $start 'HH:MM:SS'  @param string $end 'HH:MM:SS' (boleh '24:00:00')
     * @return array daftar bentrok (kosong = tersedia)
     */
    function findBookingConflicts($conn, $id_lapangan, $tanggal, $start, $end) {
        $conflicts = [];

        // 1) Booking aktif yang jamnya bertabrakan
        $stmt = mysqli_prepare($conn,
            "SELECT id, kode_booking, jam_mulai, durasi, status
               FROM booking
              WHERE id_lapangan = ? AND tanggal = ?
                AND status IN ('pending','confirmed')
                AND jam_mulai < ?
                AND ADDTIME(jam_mulai, SEC_TO_TIME(durasi * 3600)) > ?
              ORDER BY jam_mulai ASC");
        mysqli_stmt_bind_param($stmt, "isss", $id_lapangan, $tanggal, $end, $start);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        while ($r = mysqli_fetch_assoc($res)) {
            $conflicts[] = [
                'tipe'                  => 'booking',
                'id'                    => (int)$r['id'],
                'kode_booking'          => $r['kode_booking'],
                'status'                => $r['status'],
                'jam_mulai'             => substr($r['jam_mulai'], 0, 5),
                'jam_mulai_formatted'   => substr($r['jam_mulai'], 0, 5),
                'jam_selesai_formatted' => substr(minutesToTime(timeToMinutes($r['jam_mulai']) + ((int)$r['durasi'] * 60)), 0, 5),
                'durasi'                => (int)$r['durasi'],
            ];
        }

        // 2) Slot yang diblok admin (maintenance / booked manual tanpa booking_id)
        $stmt = mysqli_prepare($conn,
            "SELECT id, jam, status
               FROM jadwal
              WHERE id_lapangan = ? AND tanggal = ?
                AND booking_id IS NULL
                AND status IN ('booked','maintenance')
                AND jam < ?
                AND ADDTIME(jam, '01:00:00') > ?
              ORDER BY jam ASC");
        mysqli_stmt_bind_param($stmt, "isss", $id_lapangan, $tanggal, $end, $start);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        while ($r = mysqli_fetch_assoc($res)) {
            $conflicts[] = [
                'tipe'      => 'jadwal',
                'id'        => (int)$r['id'],
                'status'    => $r['status'],
                'jam_mulai' => substr($r['jam'], 0, 5),
                'durasi'    => 1,
            ];
        }

        return $conflicts;
    }
}
?>
