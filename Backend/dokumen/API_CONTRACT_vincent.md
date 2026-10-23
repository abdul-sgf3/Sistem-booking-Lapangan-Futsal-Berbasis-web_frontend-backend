
API CONTRACT

Sistem Booking Lapangan Futsal Berbasis Web

TSUBASA ARENA

| Keterangan | Detail |
| --- | --- |
| Dokumen | API Contract |
| Proyek | Sistem Booking Lapangan Futsal Berbasis Web |
| Nama Sistem | Tsubasa Arena |
| Frontend | HTML5, CSS3, JavaScript |
| Backend Aktual | PHP Native |
| Database | MySQL / MariaDB |
| Basis Pemetaan | File PHP dan database dari ZIP Booking-Futsal |
| Status | Draft rancangan API untuk integrasi Frontend–Backend |
| Catatan | Endpoint /api/* pada dokumen ini merupakan kontrak API yang diusulkan; ZIP saat ini masih menggunakan halaman/proses PHP langsung. |

Dokumen ini disusun dengan mempertahankan ruang lingkup booking yang benar-benar tersedia pada proyek.

# 1. Tujuan Dokumen

API Contract merupakan kesepakatan teknis antara tim frontend dan backend untuk menentukan cara pertukaran data pada Sistem Booking Lapangan Futsal Tsubasa Arena. Kontrak ini menetapkan endpoint, HTTP method, parameter, request, response, status code, validasi, autentikasi, dan aturan akses.

Pemetaan dibuat berdasarkan implementasi aktual pada ZIP proyek. Proyek saat ini menggunakan halaman PHP langsung seperti login.php, register.php, detail_lapangan.php, proses_booking.php, Riwayat_Booking.php, upload_bukti.php, batalkan_booking.php, serta modul admin. Karena itu, endpoint /api/* di bawah diperlakukan sebagai rancangan lapisan API yang dapat digunakan bila frontend dan backend dipisahkan.

Struktur database yang menjadi dasar kontrak terdiri dari tabel users, lapangan, booking, jadwal, dan pengaturan. Relasi utama booking mengacu kepada user dan lapangan, sedangkan jadwal menyimpan slot yang telah digunakan.

# 2. Ruang Lingkup API

| Kelompok | Pengguna | Fungsi |
| --- | --- | --- |
| Public API | Pengunjung | Melihat daftar lapangan aktif, detail lapangan, harga, lokasi, pengaturan jam operasional, dan ketersediaan jadwal. |
| Customer API | User terautentikasi | Registrasi, login, membuat booking, melihat riwayat/detail booking, upload bukti pembayaran, dan membatalkan booking. |
| Admin API | Admin terautentikasi | Login admin, mengelola lapangan, mengelola booking/status, mengelola user, dan melihat statistik. |
| Utility API | Pengunjung/User/Admin | Refresh CAPTCHA yang pada implementasi ZIP sudah mengembalikan JSON. |

Batasan penting: modul makanan/minuman tidak dimasukkan sebagai endpoint utama karena pada SQL database yang diberikan tidak ditemukan tabel makanan_minuman, meskipun halaman makanan.php melakukan query terhadap tabel tersebut.

# 3. Aturan Umum API

Format response API menggunakan JSON.

GET digunakan untuk mengambil data.

POST digunakan untuk membuat data baru, login, dan upload file melalui multipart/form-data jika diperlukan.

PUT/PATCH digunakan untuk memperbarui data. Kontrak menggunakan PUT untuk pembaruan penuh dan PATCH untuk perubahan status sederhana.

DELETE digunakan untuk menghapus data.

Setiap response menggunakan struktur konsisten: status, message, dan/atau data.

ID record berupa integer dan menjadi identitas unik data.

Endpoint Customer dan Admin memerlukan autentikasi. Implementasi aktual menggunakan PHP Session; jika sistem dipisahkan menjadi REST API, mekanisme dapat diganti Bearer Token sesuai kesepakatan tim.

Backend tetap melakukan validasi walaupun frontend sudah melakukan validasi.

Tanggal menggunakan format YYYY-MM-DD dan waktu menggunakan HH:MM:SS.

## 3.1 Struktur Response Standar

{
  "status": "success",
  "message": "Data berhasil diproses",
  "data": {}
}

## 3.2 Struktur Error

{
  "status": "error",
  "message": "Pesan kesalahan",
  "data": null
}

# 4. Ringkasan Endpoint

| No | Fitur | Method | Endpoint | Akses | Sumber implementasi |
| --- | --- | --- | --- | --- | --- |
| 1 | Daftar lapangan aktif | GET | /api/lapangan | Public | lapangan.php / index.php |
| 2 | Detail lapangan | GET | /api/lapangan/{id} | Public | detail_lapangan.php |
| 3 | Ketersediaan jadwal | GET | /api/jadwal | Public | detail_lapangan.php + tabel jadwal |
| 4 | Pengaturan booking | GET | /api/pengaturan | Public | detail_lapangan.php + tabel pengaturan |
| 5 | Refresh CAPTCHA user | GET | /api/auth/captcha | Public | refresh_captcha_user.php |
| 6 | Registrasi user | POST | /api/auth/register | Public | register.php |
| 7 | Login user | POST | /api/auth/login | Public | login.php |
| 8 | Logout user | POST | /api/auth/logout | Customer | logout.php |
| 9 | Buat booking | POST | /api/bookings | Customer | proses_booking.php |
| 10 | Riwayat booking | GET | /api/bookings | Customer | Riwayat_Booking.php |
| 11 | Detail booking | GET | /api/bookings/{id} | Customer | Riwayat_Booking.php |
| 12 | Upload bukti pembayaran | POST | /api/bookings/{id}/payment-proof | Customer | upload_bukti.php |
| 13 | Batalkan booking | PATCH | /api/bookings/{id}/cancel | Customer | batalkan_booking.php |
| 14 | Login admin | POST | /api/admin/login | Public | admin/login.php |
| 15 | Refresh CAPTCHA admin | GET | /api/admin/captcha | Public | admin/refresh_captcha.php |
| 16 | Daftar booking admin | GET | /api/admin/bookings | Admin | admin/booking.php |
| 17 | Detail booking admin | GET | /api/admin/bookings/{id} | Admin | admin/detail_booking.php |
| 18 | Ubah status booking | PATCH | /api/admin/bookings/{id}/status | Admin | admin/konfirmasi_booking.php |
| 19 | Daftar lapangan admin | GET | /api/admin/lapangan | Admin | admin/lapangan.php |
| 20 | Tambah lapangan | POST | /api/admin/lapangan | Admin | admin/tambah_lapangan.php |
| 21 | Edit lapangan | PUT | /api/admin/lapangan/{id} | Admin | admin/edit_lapangan.php |
| 22 | Hapus lapangan | DELETE | /api/admin/lapangan/{id} | Admin | admin/hapus_lapangan.php |
| 23 | Daftar user | GET | /api/admin/users | Admin | admin/user.php |
| 24 | Aktif/nonaktif user | PATCH | /api/admin/users/{id}/status | Admin | admin/toggle_user.php |
| 25 | Hapus user | DELETE | /api/admin/users/{id} | Admin | admin/hapus_user.php |
| 26 | Statistik dashboard | GET | /api/admin/statistik | Admin | admin/statistik.php |

# 5. Public API

## 5.1 Get Daftar Lapangan

Method: GET

Endpoint: /api/lapangan

Tujuan: Mengambil daftar lapangan yang berstatus aktif untuk ditampilkan pada beranda dan halaman daftar lapangan.

Parameter: page (opsional), limit (opsional).

{
  "status": "success",
  "data": [
    {
      "id": 1,
      "nama_lapangan": "Champions Futsal",
      "deskripsi": "Lapangan futsal premium ...",
      "lokasi": "https://www.google.com/maps/embed?...",
      "harga_per_jam": 200000,
      "foto": "1778439634_6a00d5d21270b.png",
      "status": "aktif"
    }
  ]
}

## 5.2 Get Detail Lapangan

Method: GET

Endpoint: /api/lapangan/{id}

Parameter: id (Integer, wajib). Hanya data lapangan berstatus aktif yang ditampilkan pada sisi publik.

{
  "status": "success",
  "data": {
    "id": 1,
    "nama_lapangan": "Champions Futsal",
    "deskripsi": "Lapangan futsal premium ...",
    "lokasi": "https://www.google.com/maps/embed?...",
    "harga_per_jam": 200000,
    "foto": "1778439634_6a00d5d21270b.png",
    "status": "aktif"
  }
}

## 5.3 Get Ketersediaan Jadwal

Method: GET

Endpoint: /api/jadwal

Parameter wajib: id_lapangan (Integer), tanggal (YYYY-MM-DD).

Tujuan: Menyediakan slot jam untuk membantu frontend menentukan jam yang dapat dipilih. Tabel jadwal pada database memiliki status available, booked, atau maintenance.

GET /api/jadwal?id_lapangan=2&tanggal=2026-05-21

{
  "status": "success",
  "data": [
    {
      "id": 2,
      "id_lapangan": 2,
      "tanggal": "2026-05-21",
      "jam": "10:00:00",
      "status": "booked",
      "booking_id": 4
    }
  ]
}

## 5.4 Get Pengaturan Booking

Method: GET

Endpoint: /api/pengaturan

Sumber: tabel pengaturan yang digunakan oleh detail_lapangan.php.

{
  "status": "success",
  "data": {
    "id": 1,
    "jam_buka": "08:00:00",
    "jam_tutup": "22:00:00",
    "maksimal_durasi": 2,
    "biaya_booking": 0
  }
}

# 6. Customer API

## 6.1 Registrasi Pengguna

Method: POST

Endpoint: /api/auth/register

Akses: Public.

| Field | Tipe | Wajib | Keterangan |
| --- | --- | --- | --- |
| nama | String | Ya | Nama pengguna |
| username | String | Ya | Username unik |
| email | String | Ya | Email unik dan valid |
| no_hp | String | Tidak | Nomor telepon |
| password | String | Ya | Password |
| confirm_password | String | Ya | Konfirmasi password |

{
  "nama": "Nama User",
  "username": "userbaru",
  "email": "user@email.com",
  "no_hp": "081234567890",
  "password": "********",
  "confirm_password": "********"
}

{
  "status": "success",
  "message": "Registrasi berhasil",
  "data": {
    "id": 5
  }
}

## 6.2 Login Pengguna

Method: POST

Endpoint: /api/auth/login

| Field | Tipe | Wajib | Keterangan |
| --- | --- | --- | --- |
| username | String | Ya | Username pengguna |
| password | String | Ya | Password |
| captcha | Integer | Ya | Hasil CAPTCHA |

{
  "username": "kurumi",
  "password": "********",
  "captcha": 15
}

{
  "status": "success",
  "message": "Login berhasil",
  "data": {
    "user_id": 2,
    "nama": "Kurumi",
    "username": "kurumi",
    "role": "user"
  }
}

## 6.3 Logout Pengguna

Method: POST | Endpoint: /api/auth/logout | Akses: Customer terautentikasi.

{
  "status": "success",
  "message": "Logout berhasil"
}

## 6.4 Buat Booking

Method: POST

Endpoint: /api/bookings

Akses: Customer terautentikasi.

Request dapat berupa multipart/form-data apabila bukti pembayaran dikirim bersamaan dengan booking.

| Field | Tipe | Wajib | Keterangan |
| --- | --- | --- | --- |
| id_lapangan | Integer | Ya | ID lapangan yang dipilih |
| tanggal | Date | Ya | Tanggal booking, format YYYY-MM-DD |
| jam_mulai | Time | Ya | Jam mulai, format HH:MM:SS |
| durasi | Integer | Ya | Durasi dalam jam |
| total_harga | Integer | Ya | Total biaya booking |
| catatan | String | Tidak | Catatan tambahan |
| metode_pembayaran | String | Ya | Metode pembayaran; implementasi saat ini menangani transfer dan non-transfer |
| bukti | File | Kondisional | Bukti gambar jika menggunakan transfer |

{
  "id_lapangan": 2,
  "tanggal": "2026-05-21",
  "jam_mulai": "10:00:00",
  "durasi": 1,
  "total_harga": 150000,
  "catatan": "",
  "metode_pembayaran": "transfer"
}

{
  "status": "success",
  "message": "Booking berhasil",
  "data": {
    "id": 4,
    "kode_booking": "BSK20260511976",
    "status": "pending",
    "total_harga": 150000
  }
}

Aturan penting: backend memeriksa apakah lapangan, tanggal, dan jam mulai sudah memiliki booking aktif. Booking dengan status canceled tidak dianggap bentrok.

## 6.5 Riwayat Booking

Method: GET | Endpoint: /api/bookings | Akses: Customer.

Data dikembalikan hanya untuk user yang sedang login.

{
  "status": "success",
  "data": [
    {
      "id": 4,
      "kode_booking": "BSK20260511976",
      "id_lapangan": 2,
      "nama_lapangan": "Green Futsal Park",
      "tanggal": "2026-05-21",
      "jam_mulai": "10:00:00",
      "durasi": 1,
      "total_harga": 150000,
      "catatan": "",
      "bukti_pembayaran": "",
      "status": "completed",
      "created_at": "2026-05-11 11:00:44"
    }
  ]
}

## 6.6 Detail Booking

Method: GET | Endpoint: /api/bookings/{id} | Akses: Customer.

ID booking wajib milik user yang sedang login. Jika bukan miliknya, backend mengembalikan 403 atau 404 sesuai kebijakan implementasi.

## 6.7 Upload Bukti Pembayaran

Method: POST | Endpoint: /api/bookings/{id}/payment-proof | Content-Type: multipart/form-data.

| Field | Tipe | Wajib | Keterangan |
| --- | --- | --- | --- |
| bukti | File | Ya | File gambar bukti pembayaran |

{
  "status": "success",
  "message": "Bukti pembayaran berhasil diupload",
  "data": {
    "booking_id": 4,
    "bukti_pembayaran": "1778260475_xxxxx.jpg"
  }
}

## 6.8 Batalkan Booking

Method: PATCH | Endpoint: /api/bookings/{id}/cancel | Akses: Customer.

{
  "status": "success",
  "message": "Booking berhasil dibatalkan",
  "data": {
    "id": 4,
    "status": "canceled"
  }
}

Saat pembatalan, implementasi aktual mengubah status booking menjadi canceled dan menghapus record jadwal yang terkait dengan booking tersebut.

# 7. Admin API

## 7.1 Login Admin

Method: POST | Endpoint: /api/admin/login | Akses: Public.

| Field | Tipe | Wajib | Keterangan |
| --- | --- | --- | --- |
| username | String | Ya | Username admin |
| password | String | Ya | Password admin |
| captcha | Integer | Ya | Hasil CAPTCHA |

{
  "username": "admin",
  "password": "********",
  "captcha": 20
}

{
  "status": "success",
  "message": "Login admin berhasil",
  "data": {
    "admin_id": 1,
    "nama": "King Nasir",
    "username": "admin",
    "role": "admin"
  }
}

## 7.2 Daftar Booking Admin

Method: GET | Endpoint: /api/admin/bookings | Akses: Admin.

Query parameter filter bersifat opsional: all, pending, confirmed, completed, canceled.

GET /api/admin/bookings?filter=pending

{
  "status": "success",
  "data": [
    {
      "id": 4,
      "kode_booking": "BSK20260511976",
      "user_nama": "Kurumi",
      "no_hp": "083123456123",
      "nama_lapangan": "Green Futsal Park",
      "tanggal": "2026-05-21",
      "jam_mulai": "10:00:00",
      "durasi": 1,
      "total_harga": 150000,
      "status": "pending"
    }
  ]
}

## 7.3 Detail Booking Admin

Method: GET | Endpoint: /api/admin/bookings/{id} | Akses: Admin.

Response dapat memuat data booking, user (nama/email/no_hp), nama lapangan, dan harga per jam.

## 7.4 Ubah Status Booking

Method: PATCH | Endpoint: /api/admin/bookings/{id}/status | Akses: Admin.

| Field | Tipe | Wajib | Nilai |
| --- | --- | --- | --- |
| status | String | Ya | confirmed / canceled / completed |

{
  "status": "confirmed"
}

{
  "status": "success",
  "message": "Status booking berhasil diperbarui",
  "data": {
    "id": 4,
    "status": "confirmed"
  }
}

Efek khusus dari implementasi aktual: ketika status menjadi confirmed, jadwal terkait dibuat atau diperbarui menjadi booked. Ketika status menjadi canceled, jadwal dengan booking_id tersebut dihapus.

## 7.5 Daftar Lapangan Admin

Method: GET | Endpoint: /api/admin/lapangan | Akses: Admin.

Berbeda dengan public API, admin dapat melihat lapangan aktif maupun nonaktif.

## 7.6 Tambah Lapangan

Method: POST | Endpoint: /api/admin/lapangan | Content-Type: multipart/form-data jika mengirim foto.

| Field | Tipe | Wajib | Keterangan |
| --- | --- | --- | --- |
| nama_lapangan | String | Ya | Nama lapangan |
| deskripsi | String | Tidak | Deskripsi |
| lokasi | String | Tidak | URL/embed lokasi |
| harga_per_jam | Integer | Ya | Harga per jam |
| foto | File | Tidak | Foto lapangan |
| status | String | Ya | aktif / nonaktif |

{
  "nama_lapangan": "Arena 1",
  "deskripsi": "Lapangan futsal indoor",
  "lokasi": "https://www.google.com/maps/embed?...",
  "harga_per_jam": 100000,
  "status": "aktif"
}

## 7.7 Edit Lapangan

Method: PUT | Endpoint: /api/admin/lapangan/{id} | Akses: Admin.

Field mengikuti endpoint tambah lapangan. Foto bersifat opsional dan dapat disertai flag hapus_foto sesuai kebutuhan implementasi aktual.

## 7.8 Hapus Lapangan

Method: DELETE | Endpoint: /api/admin/lapangan/{id} | Akses: Admin.

{
  "status": "success",
  "message": "Lapangan berhasil dihapus"
}

## 7.9 Daftar User

Method: GET | Endpoint: /api/admin/users | Akses: Admin.

Query parameter search bersifat opsional dan pada implementasi aktual mencari berdasarkan nama, email, atau no HP. Data admin tidak ditampilkan pada daftar user.

## 7.10 Aktif/Nonaktif User

Method: PATCH | Endpoint: /api/admin/users/{id}/status | Akses: Admin.

{
  "is_active": false
}

{
  "status": "success",
  "message": "Status user berhasil diperbarui"
}

## 7.11 Hapus User

Method: DELETE | Endpoint: /api/admin/users/{id} | Akses: Admin.

Penghapusan user perlu memperhatikan foreign key booking.id_user yang pada database menggunakan ON DELETE CASCADE.

## 7.12 Statistik Dashboard

Method: GET | Endpoint: /api/admin/statistik | Akses: Admin.

{
  "status": "success",
  "data": {
    "total_lapangan": 6,
    "total_user": 2,
    "total_booking": 2,
    "total_pendapatan": 390000,
    "booking_by_status": {
      "pending": 0,
      "confirmed": 0,
      "completed": 1,
      "canceled": 1
    }
  }
}

Nilai statistik dihitung dari database. Pendapatan pada implementasi statistik dihitung dari booking dengan status confirmed atau completed.

# 8. Utility API — CAPTCHA

## 8.1 CAPTCHA User

Method: GET | Endpoint: /api/auth/captcha | Sumber: refresh_captcha_user.php.

{
  "success": true,
  "angka1": 7,
  "angka2": 8
}

Hasil penjumlahan disimpan pada session server dan tidak dikirim sebagai field hasil.

## 8.2 CAPTCHA Admin

Method: GET | Endpoint: /api/admin/captcha | Sumber: admin/refresh_captcha.php.

{
  "success": true,
  "angka1": 9,
  "angka2": 6
}

# 9. Struktur Data Utama

## 9.1 Users

| Field | Tipe | Keterangan |
| --- | --- | --- |
| id | INT | Primary key |
| nama | VARCHAR(100) | Nama pengguna |
| username | VARCHAR(50) | Username unik |
| email | VARCHAR(100) | Email unik |
| password | VARCHAR(255) | Password tersimpan pada database |
| no_hp | VARCHAR(15) | Nomor telepon |
| role | ENUM | admin / user |
| created_at | DATETIME | Waktu pembuatan |
| is_active | TINYINT | 1 aktif, 0 nonaktif |

## 9.2 Lapangan

| Field | Tipe | Keterangan |
| --- | --- | --- |
| id | INT | Primary key |
| nama_lapangan | VARCHAR(100) | Nama lapangan |
| deskripsi | TEXT | Deskripsi lapangan |
| lokasi | TEXT | URL/embed lokasi |
| harga_per_jam | INT | Tarif per jam |
| foto | VARCHAR(255) | Nama file foto |
| status | ENUM | aktif / nonaktif |
| created_at | DATETIME | Waktu pembuatan |

## 9.3 Booking

| Field | Tipe | Keterangan |
| --- | --- | --- |
| id | INT | Primary key |
| id_user | INT | FK ke users.id |
| id_lapangan | INT | FK ke lapangan.id |
| kode_booking | VARCHAR(20) | Kode booking unik |
| tanggal | DATE | Tanggal bermain |
| jam_mulai | TIME | Jam mulai |
| durasi | INT | Durasi jam |
| total_harga | INT | Total biaya |
| catatan | TEXT | Catatan user |
| bukti_pembayaran | VARCHAR(255) | Nama file bukti |
| status | ENUM | pending / confirmed / completed / canceled |
| created_at | DATETIME | Waktu booking |

## 9.4 Jadwal

| Field | Tipe | Keterangan |
| --- | --- | --- |
| id | INT | Primary key |
| id_lapangan | INT | FK ke lapangan.id |
| tanggal | DATE | Tanggal slot |
| jam | TIME | Jam slot |
| status | ENUM | available / booked / maintenance |
| booking_id | INT | FK ke booking.id, dapat NULL |

## 9.5 Pengaturan

| Field | Tipe | Keterangan |
| --- | --- | --- |
| id | INT | Primary key |
| jam_buka | TIME | Jam operasional mulai |
| jam_tutup | TIME | Jam operasional selesai |
| maksimal_durasi | INT | Durasi maksimal booking |
| biaya_booking | INT | Biaya booking |

# 10. HTTP Status Code

| Status | Nama | Penggunaan |
| --- | --- | --- |
| 200 | OK | Request berhasil, misalnya GET, update, login, atau pembatalan. |
| 201 | Created | Data baru berhasil dibuat, misalnya registrasi atau booking. |
| 400 | Bad Request | Parameter wajib tidak lengkap atau format salah. |
| 401 | Unauthorized | Belum login atau session/token tidak valid. |
| 403 | Forbidden | Sudah login tetapi tidak memiliki hak akses. |
| 404 | Not Found | ID/data/endpoint tidak ditemukan. |
| 409 | Conflict | Slot booking sudah digunakan atau username/email sudah terdaftar. |
| 422 | Unprocessable Entity | Data lolos format dasar tetapi gagal validasi bisnis. |
| 500 | Internal Server Error | Kesalahan proses backend/database. |

# 11. Aturan Validasi

nama, username, email, password, dan field wajib lainnya tidak boleh kosong.

Username dan email harus unik sesuai constraint database.

Password dan confirm_password harus sama pada proses registrasi.

CAPTCHA harus sesuai dengan nilai yang disimpan pada session.

id_lapangan dan id booking harus berupa integer positif.

Lapangan publik harus berstatus aktif.

harga_per_jam dan total_harga harus berupa angka dan tidak boleh negatif.

durasi harus bilangan positif dan tidak melebihi maksimal_durasi dari tabel pengaturan.

Tanggal booking harus mengikuti format YYYY-MM-DD dan tidak boleh melewati aturan tanggal minimum yang diterapkan frontend.

Jam booking harus berada dalam jam operasional.

Booking tidak boleh bentrok dengan booking aktif pada lapangan, tanggal, dan jam yang sama.

Status booking hanya boleh pending, confirmed, completed, atau canceled.

Status lapangan hanya boleh aktif atau nonaktif.

Status jadwal hanya boleh available, booked, atau maintenance.

File bukti pembayaran dan foto lapangan harus diperiksa tipe/ekstensi serta ukuran sebelum disimpan.

Backend wajib memverifikasi kepemilikan booking sebelum customer mengunggah bukti atau membatalkan booking.

Endpoint admin wajib menolak request dari user biasa.

# 12. Autentikasi dan Keamanan

Implementasi ZIP saat ini menggunakan PHP Session. User login menyimpan user_id, user_nama, user_username, dan user_role. Admin login menyimpan admin_logged_in, admin_id, admin_nama, dan admin_username.

Untuk versi API terpisah, kontrak dapat menggunakan Bearer Token. Perubahan mekanisme autentikasi harus disepakati sebelum frontend dihubungkan.

Jangan mengembalikan password pada response API.

Validasi role harus dilakukan di backend, bukan hanya disembunyikan pada frontend.

Akses booking customer harus dibatasi berdasarkan id_user.

Akses file upload harus menggunakan nama file server-side yang aman.

Query database sebaiknya menggunakan prepared statement pada seluruh endpoint API.

Password pada implementasi baru sebaiknya menggunakan password_hash/password_verify; source ZIP saat ini menggunakan MD5 sehingga bagian ini merupakan rekomendasi keamanan untuk implementasi API baru, bukan klaim bahwa source saat ini sudah menerapkannya.

# 13. Alur Utama Sistem

## 13.1 Alur Booking Customer

GET /api/lapangan

GET /api/lapangan/{id}

GET /api/pengaturan

GET /api/jadwal?id_lapangan={id}&tanggal={tanggal}

Customer login melalui POST /api/auth/login

Customer mengirim POST /api/bookings

Backend memeriksa bentrok jadwal dan menyimpan booking dengan status pending

Jika pembayaran transfer, customer mengirim POST /api/bookings/{id}/payment-proof

Customer dapat melihat GET /api/bookings

Admin memproses PATCH /api/admin/bookings/{id}/status

Jika confirmed, jadwal terkait menjadi booked.

Jika canceled, booking menjadi canceled dan jadwal terkait dihapus.

## 13.2 Alur Admin

POST /api/admin/login

GET /api/admin/statistik

GET /api/admin/bookings

GET /api/admin/bookings/{id}

PATCH /api/admin/bookings/{id}/status

GET/POST/PUT/DELETE /api/admin/lapangan untuk CRUD lapangan

GET/PATCH/DELETE /api/admin/users untuk pengelolaan user

# 14. Pemetaan Endpoint terhadap File ZIP

| Endpoint API | File aktual | Operasi aktual | Keterangan pemetaan |
| --- | --- | --- | --- |
| /api/lapangan | lapangan.php / index.php | SELECT lapangan WHERE status='aktif' | Public list |
| /api/lapangan/{id} | detail_lapangan.php | SELECT lapangan berdasarkan id | Public detail |
| /api/jadwal | tabel jadwal + detail_lapangan.php | SELECT jadwal | Kontrak menormalisasi pengecekan slot |
| /api/pengaturan | detail_lapangan.php | SELECT pengaturan id=1 | Jam buka/tutup, durasi, biaya |
| /api/auth/register | register.php | INSERT users | Registrasi |
| /api/auth/login | login.php | SELECT users | Login user + session |
| /api/bookings | proses_booking.php / Riwayat_Booking.php | INSERT/SELECT booking | Create + history |
| /api/bookings/{id}/payment-proof | upload_bukti.php | UPDATE booking | Upload bukti |
| /api/bookings/{id}/cancel | batalkan_booking.php | UPDATE booking + DELETE jadwal | Pembatalan |
| /api/admin/login | admin/login.php | SELECT users role=admin | Login admin + session |
| /api/admin/bookings | admin/booking.php | SELECT JOIN | Daftar/filter booking |
| /api/admin/bookings/{id}/status | admin/konfirmasi_booking.php | UPDATE booking + jadwal | Confirm/cancel/complete |
| /api/admin/lapangan | admin/lapangan.php | SELECT lapangan | CRUD lapangan |
| /api/admin/users | admin/user.php | SELECT users | Daftar user |
| /api/admin/users/{id}/status | admin/toggle_user.php | UPDATE users | Aktif/nonaktif |
| /api/admin/statistik | admin/statistik.php | COUNT/SUM/GROUP BY | Dashboard statistik |

# 15. Batasan API

API tidak mencakup payment gateway online; sistem hanya menyimpan/menangani bukti pembayaran.

API tidak mencakup POS atau integrasi mesin kasir.

API tidak mencakup transaksi penjualan makanan/minuman karena tabel makanan_minuman tidak terdapat pada SQL yang disertakan.

API tidak mencakup aplikasi mobile Android/iOS.

API tidak menjadikan proses booking sebagai reservasi pihak ketiga/WhatsApp; booking disimpan langsung pada database proyek.

Endpoint REST /api/* belum menjadi endpoint aktual pada source ZIP; dokumen ini adalah kontrak integrasi yang dapat dijadikan acuan refactor backend.

# 16. Kesepakatan Tim

| Pihak | Tanggung Jawab |
| --- | --- |
| Backend Developer | Menyediakan endpoint sesuai kontrak, validasi, autentikasi, proses database, upload file, dan response JSON. |
| Frontend Developer | Mengonsumsi endpoint sesuai method, parameter, field, dan response yang telah disepakati. |
| Software Quality Assurance | Menguji endpoint berdasarkan request, response, status code, validasi, autentikasi, dan skenario konflik booking. |
| Project Manager / Scrum Master | Memastikan perubahan endpoint dan field dikomunikasikan serta terdokumentasi. |

# 17. Catatan Implementasi

Dokumen ini sengaja membedakan antara implementasi aktual dan endpoint API yang diusulkan. Source ZIP yang dianalisis merupakan aplikasi PHP server-rendered dengan form POST/GET dan PHP Session, bukan REST API dengan folder /api. Karena itu, endpoint pada kontrak tidak boleh dianggap sudah tersedia sebelum backend mengimplementasikannya.

Kontrak ini menggunakan nama field yang sedekat mungkin dengan field pada database dan form aktual agar proses migrasi dari PHP langsung ke API lebih mudah. Bila tim memutuskan untuk tetap menggunakan PHP Session, frontend dapat menggunakan cookie/session; bila dipisahkan menjadi SPA, kontrak dapat diadaptasi ke Bearer Token.

# 18. Referensi Implementasi yang Dianalisis

Booking-Futsal-main/tsubasa_arena_db.sql

Booking-Futsal-main/tsubasa_arena/login.php

Booking-Futsal-main/tsubasa_arena/register.php

Booking-Futsal-main/tsubasa_arena/lapangan.php

Booking-Futsal-main/tsubasa_arena/detail_lapangan.php

Booking-Futsal-main/tsubasa_arena/proses_booking.php

Booking-Futsal-main/tsubasa_arena/Riwayat_Booking.php

Booking-Futsal-main/tsubasa_arena/upload_bukti.php

Booking-Futsal-main/tsubasa_arena/batalkan_booking.php

Booking-Futsal-main/tsubasa_arena/admin/login.php

Booking-Futsal-main/tsubasa_arena/admin/booking.php

Booking-Futsal-main/tsubasa_arena/admin/konfirmasi_booking.php

Booking-Futsal-main/tsubasa_arena/admin/lapangan.php

Booking-Futsal-main/tsubasa_arena/admin/user.php

Booking-Futsal-main/tsubasa_arena/admin/statistik.php

# 19. Persetujuan Kontrak

| Peran | Nama | Tanda Tangan | Tanggal |
| --- | --- | --- | --- |
| Backend Developer | ________________ | ________________ | ________________ |
| Frontend Developer | ________________ | ________________ | ________________ |
| Software QA | ________________ | ________________ | ________________ |
| Project Manager / Scrum Master | ________________ | ________________ | ________________ |