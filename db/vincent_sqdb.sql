-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 13, 2026 at 09:52 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.33

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
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `id_lapangan` int NOT NULL,
  `kode_booking` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `durasi` int NOT NULL DEFAULT '1',
  `total_harga` int NOT NULL,
  `catatan` text COLLATE utf8mb4_general_ci,
  `bukti_pembayaran` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('pending','confirmed','completed','canceled') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`id`, `id_user`, `id_lapangan`, `kode_booking`, `tanggal`, `jam_mulai`, `durasi`, `total_harga`, `catatan`, `bukti_pembayaran`, `status`, `created_at`) VALUES
(3, 2, 1, 'BSK20260510315', '2026-05-12', '10:00:00', 2, 240000, '', '', 'canceled', '2026-05-11 01:51:38'),
(4, 2, 2, 'BSK20260511976', '2026-05-21', '10:00:00', 1, 150000, '', '', 'completed', '2026-05-11 11:00:44');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal`
--

CREATE TABLE `jadwal` (
  `id` int NOT NULL,
  `id_lapangan` int NOT NULL,
  `tanggal` date NOT NULL,
  `jam` time NOT NULL,
  `status` enum('available','booked','maintenance') COLLATE utf8mb4_general_ci DEFAULT 'available',
  `booking_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jadwal`
--

INSERT INTO `jadwal` (`id`, `id_lapangan`, `tanggal`, `jam`, `status`, `booking_id`) VALUES
(1, 2, '2026-05-10', '09:00:00', 'booked', NULL),
(2, 2, '2026-05-21', '10:00:00', 'booked', 4);

-- --------------------------------------------------------

--
-- Table structure for table `lapangan`
--

CREATE TABLE `lapangan` (
  `id` int NOT NULL,
  `nama_lapangan` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci,
  `lokasi` text COLLATE utf8mb4_general_ci,
  `harga_per_jam` int NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('aktif','nonaktif') COLLATE utf8mb4_general_ci DEFAULT 'aktif',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lapangan`
--

INSERT INTO `lapangan` (`id`, `nama_lapangan`, `deskripsi`, `lokasi`, `harga_per_jam`, `foto`, `status`, `created_at`) VALUES
(1, 'Lapangan1', 'Lapangan futsal premium bertema modern futuristik dengan pencahayaan LED profesional dan desain eksklusif. Sangat cocok untuk pertandingan kompetitif dan pengalaman bermain kelas profesional.', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15849.280058054552!2d108.51720518715817!3d-6.730755099999994!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6ee20c7bee47bd%3A0x21163df67073f97b!2sTaman%20Cipto%20Sport%20Centre!5e0!3m2!1sid!2sid!4v1778769842575!5m2!1sid!2sid', 200000, 'Lapangan1.jpeg', 'aktif', '2026-05-08 21:14:20'),
(2, 'Lapangan 2', 'Lapangan futsal bernuansa alam dengan rumput sintetis berkualitas tinggi dan sirkulasi udara terbuka. Memberikan pengalaman bermain yang segar, nyaman, dan aman untuk semua pemain.', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15849.280058054552!2d108.51720518715817!3d-6.730755099999994!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6f1df112d84fef%3A0xbdd3db3590b4b3e9!2sGOR%20Bima%20Cirebon!5e0!3m2!1sid!2sid!4v1778769782634!5m2!1sid!2sid', 150000, 'Lapangan2.jpeg', 'aktif', '2026-05-08 21:14:20');

-- --------------------------------------------------------

--
-- Table structure for table `makanan_minuman`
--

CREATE TABLE `makanan_minuman` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kategori` enum('makanan','minuman') NOT NULL,
  `harga` int NOT NULL,
  `deskripsi` text,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `makanan_minuman`
--

INSERT INTO `makanan_minuman` (`id`, `nama`, `kategori`, `harga`, `deskripsi`, `foto`) VALUES
(1, 'Nasi Goreng', 'makanan', 15000, 'Nasi goreng spesial dengan telur', 'nasi_goreng.jpg'),
(2, 'Mie Ayam', 'makanan', 12000, 'Mie ayam gurih dengan pangsit', 'mie_ayam.jpg'),
(3, 'Es Teh Manis', 'minuman', 5000, 'Es teh segar', 'es_teh.jpg'),
(4, 'Air Mineral', 'minuman', 4000, 'Air mineral dingin', 'air_mineral.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id` int NOT NULL,
  `jam_buka` time DEFAULT '08:00:00',
  `jam_tutup` time DEFAULT '22:00:00',
  `maksimal_durasi` int DEFAULT '2',
  `biaya_booking` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengaturan`
--

INSERT INTO `pengaturan` (`id`, `jam_buka`, `jam_tutup`, `maksimal_durasi`, `biaya_booking`) VALUES
(1, '08:00:00', '22:00:00', 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `no_hp` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` enum('admin','user') COLLATE utf8mb4_general_ci DEFAULT 'user',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `email`, `password`, `no_hp`, `role`, `created_at`, `is_active`) VALUES
(1, 'King Nasir', 'admin', 'admin@tsubasa.com', '0192023a7bbd73250516f069df18b500', NULL, 'admin', '2026-05-08 21:14:20', 1),
(2, 'Kurumi', 'kurumi', 'kurumi12@email.com', '5c21086fea6fae64471b7b37e80825dd', '083123456123', 'user', '2026-05-08 23:39:37', 1),
(4, 'erreenn', 'asyaf', 'ashfahani@gmail.com', '4bd78d853053f1b7a83d7c5207f604ce', '93298r27427727', 'user', '2026-05-14 21:56:55', 1),
(5, 'Asyaf', 'asyaf87', 'asyaf@gmail.com', '4bd78d853053f1b7a83d7c5207f604ce', '', 'user', '2026-10-09 10:36:52', 1),
(6, 'Ashfahani77', 'ashfahani77', 'ashfahani77@gmail.com', 'efe8bf6dbc8ae0da501a40943bfa74de', '', 'user', '2026-10-09 10:46:30', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_booking` (`kode_booking`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_lapangan` (`id_lapangan`);

--
-- Indexes for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_jadwal` (`id_lapangan`,`tanggal`,`jam`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `lapangan`
--
ALTER TABLE `lapangan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `makanan_minuman`
--
ALTER TABLE `makanan_minuman`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lapangan`
--
ALTER TABLE `lapangan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `makanan_minuman`
--
ALTER TABLE `makanan_minuman`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`id_lapangan`) REFERENCES `lapangan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `jadwal_ibfk_1` FOREIGN KEY (`id_lapangan`) REFERENCES `lapangan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jadwal_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
