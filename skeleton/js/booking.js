document.addEventListener('DOMContentLoaded', async () => {
    const params = new URLSearchParams(location.search);
    const id = params.get('id');
    if (!id) { location.href = 'lapangan.html'; return; }

    const result = await fetchAPI('/public/lapangan_detail.php?id=' + id);
    if (result.data) {
        document.getElementById('lapangan-nama').textContent = result.data.nama_lapangan;
        document.getElementById('lapangan-harga').textContent = formatRupiah(result.data.harga_per_jam) + ' / jam';
        document.getElementById('lapangan-img').style.backgroundImage = `url('${result.data.foto_url}')`;
    }

    const form = document.getElementById('form-booking');
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = {
                id_lapangan: id,
                tanggal: form.tanggal.value,
                jam_mulai: form.jam_mulai.value,
                durasi: form.durasi.value
            };
            const result = await fetchAPI('/customer/booking.php', 'POST', data);
            if (result.status === 'success') {
                alert('Booking berhasil!');
                window.location.href = 'riwayat_booking.html';
            } else {
                alert(result.message);
            }
        });
    }
});