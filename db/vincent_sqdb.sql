-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Okt 2026 pada 07.35
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vincent_sqdb`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `booking`
--

CREATE TABLE `booking` (
  `id` int(11) NOT NULL,
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
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `booking`
--

INSERT INTO `booking` (`id`, `id_user`, `id_lapangan`, `kode_booking`, `tanggal`, `jam_mulai`, `durasi`, `total_harga`, `catatan`, `metode_pembayaran`, `bukti_pembayaran`, `uploaded_at`, `status`, `canceled_at`, `created_at`) VALUES
(3, 2, 1, 'BSK20260510315', '2026-05-12', '10:00:00', 2, 240000, '', 'transfer', '', NULL, 'canceled', NULL, '2026-05-11 01:51:38'),
(4, 2, 2, 'BSK20260511976', '2026-05-21', '10:00:00', 1, 150000, '', 'transfer', '', NULL, 'completed', NULL, '2026-05-11 11:00:44'),
(5, 7, 1, 'BSK202610206521', '2026-12-01', '10:00:00', 1, 200000, '', 'transfer', NULL, NULL, 'pending', NULL, '2026-10-20 10:59:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal`
--

CREATE TABLE `jadwal` (
  `id` int(11) NOT NULL,
  `id_lapangan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jam` time NOT NULL,
  `status` enum('available','booked','maintenance') DEFAULT 'available',
  `booking_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jadwal`
--

INSERT INTO `jadwal` (`id`, `id_lapangan`, `tanggal`, `jam`, `status`, `booking_id`) VALUES
(1, 2, '2026-05-10', '09:00:00', 'booked', NULL),
(2, 2, '2026-05-21', '10:00:00', 'booked', 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `lapangan`
--

CREATE TABLE `lapangan` (
  `id` int(11) NOT NULL,
  `nama_lapangan` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `lokasi` text DEFAULT NULL,
  `harga_per_jam` int(11) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `lapangan`
--

INSERT INTO `lapangan` (`id`, `nama_lapangan`, `deskripsi`, `lokasi`, `harga_per_jam`, `foto`, `status`, `created_at`) VALUES
(1, 'Lapangan Futsal 1', 'Lapangan futsal premium bertema modern futuristik dengan pencahayaan LED profesional dan desain eksklusif. Sangat cocok untuk pertandingan kompetitif dan pengalaman bermain kelas profesional.', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15849.280058054552!2d108.51720518715817!3d-6.730755099999994!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6ee20c7bee47bd%3A0x21163df67073f97b!2sTaman%20Cipto%20Sport%20Centre!5e0!3m2!1sid!2sid!4v1778769842575!5m2!1sid!2sid', 200000, 'Lapangan1.jpeg', 'aktif', '2026-05-08 21:14:20'),
(2, 'Lapangan Futsal 2', 'Lapangan futsal bernuansa alam dengan rumput sintetis berkualitas tinggi dan sirkulasi udara terbuka. Memberikan pengalaman bermain yang segar, nyaman, dan aman untuk semua pemain.', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15849.280058054552!2d108.51720518715817!3d-6.730755099999994!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6f1df112d84fef%3A0xbdd3db3590b4b3e9!2sGOR%20Bima%20Cirebon!5e0!3m2!1sid!2sid!4v1778769782634!5m2!1sid!2sid', 150000, 'Lapangan2.jpeg', 'aktif', '2026-05-08 21:14:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `makanan_minuman`
--

CREATE TABLE `makanan_minuman` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kategori` enum('makanan','minuman') NOT NULL,
  `harga` int(11) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `makanan_minuman`
--

INSERT INTO `makanan_minuman` (`id`, `nama`, `kategori`, `harga`, `deskripsi`, `foto`) VALUES
(1, 'Nasi Goreng', 'makanan', 15000, 'Nasi goreng spesial dengan telur', 'nasi_goreng.jpg'),
(2, 'Mie Ayam', 'makanan', 12000, 'Mie ayam gurih dengan pangsit', 'mie_ayam.jpg'),
(3, 'Es Teh Manis', 'minuman', 5000, 'Es teh segar', 'es_teh.jpg'),
(4, 'Air Mineral', 'minuman', 4000, 'Air mineral dingin', 'air_mineral.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id` int(11) NOT NULL,
  `jam_buka` time DEFAULT '08:00:00',
  `jam_tutup` time DEFAULT '22:00:00',
  `maksimal_durasi` int(11) DEFAULT 2,
  `biaya_booking` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengaturan`
--

INSERT INTO `pengaturan` (`id`, `jam_buka`, `jam_tutup`, `maksimal_durasi`, `biaya_booking`) VALUES
(1, '08:00:00', '22:00:00', 2, 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(4) DEFAULT 1,
  `is_verified` tinyint(1) DEFAULT 0,
  `otp_code` varchar(6) DEFAULT NULL,
  `otp_expired_at` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `email`, `whatsapp`, `foto_profil`, `password`, `role`, `created_at`, `is_active`, `is_verified`, `otp_code`, `otp_expired_at`, `last_login`, `updated_at`) VALUES
(1, 'King Nasir', 'admin', 'admin@tsubasa.com', NULL, NULL, '$2y$10$OrWCbHcIxD/uSuQTuaeHsO43tvC4UFNQqlUpVhXWQBkqhCuBJzNDW', 'admin', '2026-05-08 21:14:20', 1, 1, NULL, NULL, '2026-10-20 12:00:11', '2026-10-20 12:00:11'),
(2, 'Kurumi', 'kurumi', 'kurumi12@email.com', '083123456123', NULL, '$2y$10$OrWCbHcIxD/uSuQTuaeHsO43tvC4UFNQqlUpVhXWQBkqhCuBJzNDW', 'user', '2026-05-08 23:39:37', 1, 1, NULL, NULL, NULL, '2026-10-20 11:04:15'),
(4, 'erreenn', 'asyaf', 'ashfahani@gmail.com', '93298r27427727', NULL, '$2y$10$OrWCbHcIxD/uSuQTuaeHsO43tvC4UFNQqlUpVhXWQBkqhCuBJzNDW', 'user', '2026-05-14 21:56:55', 1, 1, NULL, NULL, NULL, '2026-10-20 11:04:15'),
(5, 'Asyaf', 'asyaf87', 'asyaf@gmail.com', '', NULL, '$2y$10$OrWCbHcIxD/uSuQTuaeHsO43tvC4UFNQqlUpVhXWQBkqhCuBJzNDW', 'user', '2026-10-09 10:36:52', 1, 1, NULL, NULL, NULL, '2026-10-20 11:04:15'),
(6, 'Ashfahani77', 'ashfahani77', 'ashfahani77@gmail.com', '', NULL, '$2y$10$OrWCbHcIxD/uSuQTuaeHsO43tvC4UFNQqlUpVhXWQBkqhCuBJzNDW', 'user', '2026-10-09 10:46:30', 1, 1, NULL, NULL, NULL, '2026-10-20 11:04:15'),
(7, 'User Tes Lengkap', 'usertes', 'tes@gmail.com', '081234567890', NULL, '$2y$10$OrWCbHcIxD/uSuQTuaeHsO43tvC4UFNQqlUpVhXWQBkqhCuBJzNDW', 'admin', '2026-10-19 17:41:08', 1, 1, NULL, NULL, '2026-10-20 11:59:42', '2026-10-20 11:59:42');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_booking` (`kode_booking`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_lapangan` (`id_lapangan`);

--
-- Indeks untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_jadwal` (`id_lapangan`,`tanggal`,`jam`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indeks untuk tabel `lapangan`
--
ALTER TABLE `lapangan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `makanan_minuman`
--
ALTER TABLE `makanan_minuman`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `booking`
--
ALTER TABLE `booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `lapangan`
--
ALTER TABLE `lapangan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `makanan_minuman`
--
ALTER TABLE `makanan_minuman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`id_lapangan`) REFERENCES `lapangan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `jadwal_ibfk_1` FOREIGN KEY (`id_lapangan`) REFERENCES `lapangan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jadwal_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
