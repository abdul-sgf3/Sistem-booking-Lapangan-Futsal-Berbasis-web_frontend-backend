document.addEventListener('DOMContentLoaded', async () => {
    const container = document.getElementById('riwayat-container');
    if (!container) return;

    const result = await fetchAPI('/customer/booking_list.php');
    const data = result.data || [];

    if (data.length === 0) {
        container.innerHTML = '<p class="loading-text">Belum ada riwayat.</p>';
        return;
    }

    container.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
    // TODO: Render tabel riwayat booking
});