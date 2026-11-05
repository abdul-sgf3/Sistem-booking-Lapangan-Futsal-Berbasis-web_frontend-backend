-- Data awal (hanya dijalankan saat database masih kosong)
INSERT IGNORE INTO `lapangan` (`id`, `nama_lapangan`, `deskripsi`, `lokasi`, `harga_per_jam`, `foto`, `status`, `created_at`) VALUES
(1, 'Lapangan Futsal 1', 'Lapangan futsal premium bertema modern futuristik dengan pencahayaan LED profesional dan desain eksklusif. Sangat cocok untuk pertandingan kompetitif dan pengalaman bermain kelas profesional.', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15849.280058054552!2d108.51720518715817!3d-6.730755099999994!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6ee20c7bee47bd%3A0x21163df67073f97b!2sTaman%20Cipto%20Sport%20Centre!5e0!3m2!1sid!2sid!4v1778769842575!5m2!1sid!2sid', 200000, 'Lapangan1.jpeg', 'aktif', '2026-05-08 21:14:20'),
(2, 'Lapangan Futsal 2', 'Lapangan futsal bernuansa alam dengan rumput sintetis berkualitas tinggi dan sirkulasi udara terbuka. Memberikan pengalaman bermain yang segar, nyaman, dan aman untuk semua pemain.', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15849.280058054552!2d108.51720518715817!3d-6.730755099999994!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6f1df112d84fef%3A0xbdd3db3590b4b3e9!2sGOR%20Bima%20Cirebon!5e0!3m2!1sid!2sid!4v1778769782634!5m2!1sid!2sid', 150000, 'Lapangan2.jpeg', 'aktif', '2026-05-08 21:14:20');

INSERT IGNORE INTO `makanan_minuman` (`id`, `nama`, `kategori`, `harga`, `deskripsi`, `foto`) VALUES
(1, 'Nasi Goreng', 'makanan', 15000, 'Nasi goreng spesial dengan telur', 'nasi_goreng.jpg'),
(2, 'Mie Ayam', 'makanan', 12000, 'Mie ayam gurih dengan pangsit', 'mie_ayam.jpg'),
(3, 'Es Teh Manis', 'minuman', 5000, 'Es teh segar', 'es_teh.jpg'),
(4, 'Air Mineral', 'minuman', 4000, 'Air mineral dingin', 'air_mineral.jpg');

INSERT IGNORE INTO `pengaturan` (`id`, `jam_buka`, `jam_tutup`, `maksimal_durasi`, `biaya_booking`) VALUES
(1, '08:00:00', '22:00:00', 2, 0);
