-- Skema Vincent SQ Arena (idempotent: aman dijalankan berulang)
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(4) DEFAULT 1,
  `is_verified` tinyint(1) DEFAULT 0,
  `otp_code` varchar(6) DEFAULT NULL,
  `otp_expired_at` datetime DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `lapangan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_lapangan` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `lokasi` text DEFAULT NULL,
  `harga_per_jam` int(11) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `booking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_lapangan` int(11) NOT NULL,
  `kode_booking` varchar(20) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `durasi` int(11) NOT NULL DEFAULT 1,
  `total_harga` int(11) NOT NULL,
  `catatan` text DEFAULT NULL,
  `metode_pembayaran` varchar(50) DEFAULT 'transfer',
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT NULL,
  `status` enum('pending','confirmed','completed','canceled') DEFAULT 'pending',
  `canceled_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_booking` (`kode_booking`),
  KEY `id_user` (`id_user`),
  KEY `id_lapangan` (`id_lapangan`),
  CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`id_lapangan`) REFERENCES `lapangan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `jadwal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_lapangan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jam` time NOT NULL,
  `status` enum('available','booked','maintenance') DEFAULT 'available',
  `booking_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_jadwal` (`id_lapangan`,`tanggal`,`jam`),
  KEY `booking_id` (`booking_id`),
  CONSTRAINT `jadwal_ibfk_1` FOREIGN KEY (`id_lapangan`) REFERENCES `lapangan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `jadwal_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `makanan_minuman` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `kategori` enum('makanan','minuman') NOT NULL,
  `harga` int(11) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `pengaturan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jam_buka` time DEFAULT '08:00:00',
  `jam_tutup` time DEFAULT '22:00:00',
  `maksimal_durasi` int(11) DEFAULT 2,
  `biaya_booking` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
