document.addEventListener('DOMContentLoaded', async () => {
    const container = document.getElementById('menu-container');
    if (!container) return;

    const result = await fetchAPI('/public/makanan.php');
    const data = result.data || [];

    container.innerHTML = data.map(item => `
        <div class="menu-card p-4 border rounded-xl">
            <h3 class="font-bold">${item.nama}</h3>
            <p class="text-sm text-slate-500">${item.deskripsi || ''}</p>
            <div class="text-accent font-bold mt-2">${formatRupiah(item.harga)}</div>
        </div>
    `).join('');
});