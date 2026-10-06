<?php
// detail_lapangan.php - Detail Lapangan
session_start();
include 'config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = "SELECT * FROM lapangan WHERE id = $id AND status = 'aktif'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: lapangan.php");
    exit();
}

$lapangan = mysqli_fetch_assoc($result);
$title = $lapangan['nama_lapangan'] . ' - Vincent Arena';
include 'includes/header.php';

// Ambil pengaturan
$setting = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pengaturan WHERE id = 1"));
$jam_buka = $setting['jam_buka'] ?? '08:00:00';
$jam_tutup = $setting['jam_tutup'] ?? '22:00:00';
?>

<style>
    .detail-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin: 30px 0;
    }
    
    .lapangan-info {
        background: white;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .lapangan-img {
        width: 100%;
        height: 300px;
        object-fit: cover;
        border-radius: 16px;
        margin-bottom: 20px;
    }
    
    .lapangan-info h1 {
        color: #0a2b4e;
        margin-bottom: 10px;
    }
    
    .harga {
        font-size: 28px;
        font-weight: bold;
        color: #e63946;
        margin: 15px 0;
    }
    
    .deskripsi {
        color: #666;
        line-height: 1.6;
        margin: 15px 0;
    }
    
    .booking-form {
        background: white;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .booking-form h3 {
        color: #0a2b4e;
        margin-bottom: 20px;
        border-left: 4px solid #e63946;
        padding-left: 12px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }
    
    input, select {
        width: 100%;
        padding: 12px;
        border: 2px solid #e1e1e1;
        border-radius: 12px;
        font-size: 14px;
    }
    
    input:focus, select:focus {
        outline: none;
        border-color: #e63946;
    }
    
    .btn-submit {
        background: #e63946;
        color: white;
        padding: 14px;
        border: none;
        border-radius: 30px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        width: 100%;
        transition: all 0.3s;
    }
    
    .btn-submit:hover {
        background: #c1121f;
        transform: translateY(-2px);
    }
    
    .alert {
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    
    .alert-error {
        background: #fee;
        color: #e63946;
        border-left: 3px solid #e63946;
    }
    
    .alert-success {
        background: #d4edda;
        color: #155724;
        border-left: 3px solid #28a745;
    }
    
    .info-jam {
        background: #f0f2f5;
        padding: 10px;
        border-radius: 10px;
        font-size: 12px;
        color: #666;
        margin-top: 10px;
    }
    
    @media (max-width: 768px) {
        .detail-container {
            grid-template-columns: 1fr;
        }
    }

    .map-container {
    margin: 25px 0;
    }
</style>

<div class="container">
    <div class="detail-container">
        <!-- Kolom Kiri: Info Lapangan -->
        <div class="lapangan-info">
            <img src="assets/uploads/lapangan/<?= $lapangan['foto'] ?? 'default.jpg' ?>" class="lapangan-img" alt="<?= $lapangan['nama_lapangan'] ?>">
            <h1><?= $lapangan['nama_lapangan'] ?></h1>
            <div class="harga">Rp <?= number_format($lapangan['harga_per_jam'], 0, ',', '.') ?> <span style="font-size: 14px;">/ jam</span></div>
            <div class="deskripsi"><?= nl2br($lapangan['deskripsi']) ?></div>
            <?php if ($lapangan['lokasi']): ?>
            <div class="map-container">
                <h3>📍 Lokasi Lapangan</h3>
                <iframe 
                    src="<?= $lapangan['lokasi'] ?>" 
                    width="100%" 
                    height="300" 
                    style="border:0; border-radius: 16px;" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Kolom Kanan: Form Booking -->
        <div class="booking-form">
            <h3>📅 Booking Lapangan</h3>
            
            <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="alert alert-error">
                    ⚠️ Silakan <a href="login.php" style="color: #e63946;">login</a> terlebih dahulu untuk melakukan booking.
                </div>
            <?php else: ?>
                <form method="POST" action="proses_booking.php">
                    <input type="hidden" name="id_lapangan" value="<?= $lapangan['id'] ?>">
                    <input type="hidden" name="harga_per_jam" value="<?= $lapangan['harga_per_jam'] ?>">
                    
                    <div class="form-group">
                        <label>📅 Tanggal Main</label>
                        <input type="date" name="tanggal" id="tanggal" required min="<?= date('Y-m-d') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>⏰ Jam Mulai</label>
                        <select name="jam_mulai" id="jam_mulai" required>
                            <option value="">Pilih jam</option>
                            <?php
                            $start = strtotime($jam_buka);
                            $end = strtotime($jam_tutup);
                            for ($i = $start; $i < $end; $i += 3600):
                                $jam = date('H:i', $i);
                            ?>
                            <option value="<?= $jam ?>"><?= $jam ?> WIB</option>
                            <?php endfor; ?>
                        </select>
                        <div class="info-jam">⏰ Jam operasional: <?= date('H:i', strtotime($jam_buka)) ?> - <?= date('H:i', strtotime($jam_tutup)) ?> WIB</div>
                    </div>
                    
                    <div class="form-group">
                        <label>⏱️ Durasi (jam)</label>
                        <select name="durasi" id="durasi" required>
                            <option value="1">1 jam</option>
                            <option value="2">2 jam</option>
                            <option value="3">3 jam</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>📝 Catatan (opsional)</label>
                        <textarea name="catatan" rows="3" placeholder="Catatan tambahan..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>💰 Total Harga</label>
                        <div style="font-size: 24px; font-weight: bold; color: #e63946;" id="total_harga_display">
                            Rp <?= number_format($lapangan['harga_per_jam'], 0, ',', '.') ?>
                        </div>
                        <input type="hidden" name="total_harga" id="total_harga" value="<?= $lapangan['harga_per_jam'] ?>">
                    </div>

                    <div class="form-group">
                        <label>💰 Metode Pembayaran</label>
                        <select name="metode_pembayaran" id="metode_pembayaran" required>
                            <option value="transfer">Transfer Bank (Manual)</option>
                            <option value="cash">Bayar di Tempat (Cash)</option>
                        </select>
                    </div>

                    <div id="uploadBukti" style="display: <?= ($_SERVER['REQUEST_METHOD'] != 'POST' ? 'block' : 'none') ?>;">
                        <div class="form-group">
                            <label>📎 Upload Bukti Transfer</label>
                            <input type="file" name="bukti" accept="image/*">
                            <div class="info-jam">*Upload screenshot/foto bukti transfer</div>
                        </div>
                    </div>

                    <div id="infoRekening" style="background: #f0f2f5; padding: 15px; border-radius: 12px; margin-bottom: 20px; display: <?= ($_SERVER['REQUEST_METHOD'] != 'POST' ? 'block' : 'none') ?>;">
                        <h4>🏦 Rekening Tujuan</h4>
                        <p>Bank Mandiri: 123-456-7890<br>a.n. Vincent Arena</p>
                        <p>Bank BCA: 987-654-3210<br>a.n. Vincent Arena</p>
                        <p style="font-size: 12px; color: #e63946;">*Transfer sesuai total harga, upload bukti untuk konfirmasi admin</p>
                    </div>
                    
                    <button type="submit" class="btn-submit">✅ Booking Sekarang</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Update total harga berdasarkan durasi
    const hargaPerJam = <?= $lapangan['harga_per_jam'] ?>;
    const durasiSelect = document.getElementById('durasi');
    const totalHargaDisplay = document.getElementById('total_harga_display');
    const totalHargaInput = document.getElementById('total_harga');
    
    if (durasiSelect) {
        durasiSelect.addEventListener('change', function() {
            const durasi = parseInt(this.value);
            const total = hargaPerJam * durasi;
            totalHargaDisplay.innerHTML = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            totalHargaInput.value = total;
        });
    }
    
    // Cek ketersediaan saat tanggal/jam berubah
    const tanggalInput = document.getElementById('tanggal');
    const jamSelect = document.getElementById('jam_mulai');
    
    function cekKetersediaan() {
        const tanggal = tanggalInput ? tanggalInput.value : '';
        const jam = jamSelect ? jamSelect.value : '';
        
        if (tanggal && jam) {
            fetch('cek_ketersediaan.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id_lapangan=<?= $lapangan['id'] ?>&tanggal=' + tanggal + '&jam=' + jam
            })
            .then(response => response.json())
            .then(data => {
                if (!data.available) {
                    alert('Jam ' + jam + ' sudah dibooking! Silakan pilih jam lain.');
                    if (jamSelect) jamSelect.value = '';
                }
            });
        }
    }
    
    if (tanggalInput) tanggalInput.addEventListener('change', cekKetersediaan);
    if (jamSelect) jamSelect.addEventListener('change', cekKetersediaan);

   
    const metode = document.getElementById('metode_pembayaran');
    const uploadBukti = document.getElementById('uploadBukti');
    const infoRekening = document.getElementById('infoRekening');
    
    metode.addEventListener('change', function() {
        if (this.value == 'transfer') {
            uploadBukti.style.display = 'block';
            infoRekening.style.display = 'block';
        } else {
            uploadBukti.style.display = 'none';
            infoRekening.style.display = 'none';
        }
    });

</script>

<?php include 'includes/footer.php'; ?>