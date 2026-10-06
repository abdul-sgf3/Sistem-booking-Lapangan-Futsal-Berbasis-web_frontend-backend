-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 06 Okt 2026 pada 12.50
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
-- Database: `vincent'sqdb`
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
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `status` enum('pending','confirmed','completed','canceled') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `booking`
--

INSERT INTO `booking` (`id`, `id_user`, `id_lapangan`, `kode_booking`, `tanggal`, `jam_mulai`, `durasi`, `total_harga`, `catatan`, `bukti_pembayaran`, `status`, `created_at`) VALUES
(3, 2, 1, 'BSK20260510315', '2026-05-12', '10:00:00', 2, 240000, '', '', 'canceled', '2026-05-11 01:51:38'),
(4, 2, 2, 'BSK20260511976', '2026-05-21', '10:00:00', 1, 150000, '', '', 'completed', '2026-05-11 11:00:44');

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
(1, 'Champions Futsal', 'Lapangan futsal premium bertema modern futuristik dengan pencahayaan LED profesional dan desain eksklusif. Sangat cocok untuk pertandingan kompetitif dan pengalaman bermain kelas profesional.', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15849.280058054552!2d108.51720518715817!3d-6.730755099999994!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6ee20c7bee47bd%3A0x21163df67073f97b!2sTaman%20Cipto%20Sport%20Centre!5e0!3m2!1sid!2sid!4v1778769842575!5m2!1sid!2sid', 200000, '1778439634_6a00d5d21270b.png', 'aktif', '2026-05-08 21:14:20'),
(2, 'Green Futsal Park', 'Lapangan futsal bernuansa alam dengan rumput sintetis berkualitas tinggi dan sirkulasi udara terbuka. Memberikan pengalaman bermain yang segar, nyaman, dan aman untuk semua pemain.', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15849.280058054552!2d108.51720518715817!3d-6.730755099999994!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6f1df112d84fef%3A0xbdd3db3590b4b3e9!2sGOR%20Bima%20Cirebon!5e0!3m2!1sid!2sid!4v1778769782634!5m2!1sid!2sid', 150000, '1778439550_6a00d57ee5bef.png', 'aktif', '2026-05-08 21:14:20'),
(3, 'Arena 1', 'Lapangan futsal indoor modern dengan lantai vinyl premium berwarna biru yang nyaman untuk permainan cepat dan kontrol bola maksimal. Cocok untuk latihan rutin, sparing, maupun turnamen komunitas.', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15849.629509687287!2d108.53479888715817!3d-6.720042400000007!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6ee27174682cb5%3A0x2c05da5e5b6be451!2sKharisma%20Futsal%20Centre!5e0!3m2!1sid!2sid!4v1778769730375!5m2!1sid!2sid', 100000, '1778439429_6a00d505d0d29.png', 'aktif', '2026-05-08 21:14:20'),
(4, 'Neo Kick Arena', 'Lapangan futsal indoor dengan konsep cyber sport modern, dilengkapi pencahayaan terang dan lantai interlock berkualitas tinggi. Ideal untuk bermain malam hari dengan suasana kompetitif dan energik.', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15849.072805071313!2d108.5341695959761!3d-6.737100584289621!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6f1df507ead1e3%3A0xfb1760f020a10abe!2sPemuda%20Futsal!5e0!3m2!1sid!2sid!4v1778769659782!5m2!1sid!2sid', 120000, '1778575750_6a02e98639aa4.png', 'aktif', '2026-05-12 15:46:58'),
(5, 'Sakura Futsal Zone', 'Lapangan futsal bertema Jepang minimalis dengan area bermain nyaman dan suasana santai. Cocok untuk fun match, latihan tim, maupun bermain bersama teman dan komunitas.', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3962.128817788807!2d108.5711966!3d-6.7541412!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6f1dcbf7790547%3A0xc28c700c136924d!2sFutsal%20Cirebon%20Teratai%20Sport%20Park!5e0!3m2!1sid!2sid!4v1778608787621!5m2!1sid!2sid', 180000, '1778575793_6a02e9b10ace3.png', 'aktif', '2026-05-12 15:47:44'),
(6, 'Victory Futsal Arena', 'Lapangan futsal indoor berkonsep sporty modern dengan pencahayaan premium dan desain elegan bernuansa hitam-oranye. Memiliki area bermain luas, nyaman, dan cocok untuk pertandingan intens maupun event futsal komunitas.', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3962.4939900035133!2d108.53775019999999!3d-6.709404899999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6ee2165397922f%3A0x2ce4ad36da74dd4c!2sTuparev%20Futsal!5e0!3m2!1sid!2sid!4v1778608628445!5m2!1sid!2sid', 200000, '1778576047_6a02eaaf81246.png', 'aktif', '2026-05-12 15:54:07');

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
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `email`, `password`, `no_hp`, `role`, `created_at`, `is_active`) VALUES
(1, 'King Nasir', 'admin', 'admin@tsubasa.com', '0192023a7bbd73250516f069df18b500', NULL, 'admin', '2026-05-08 21:14:20', 1),
(2, 'Kurumi', 'kurumi', 'kurumi12@email.com', '5c21086fea6fae64471b7b37e80825dd', '083123456123', 'user', '2026-05-08 23:39:37', 1),
(4, 'erreenn', 'asyaf', 'ashfahani@gmail.com', '4bd78d853053f1b7a83d7c5207f604ce', '93298r27427727', 'user', '2026-05-14 21:56:55', 1);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- AUTO_INCREMENT untuk tabel `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
