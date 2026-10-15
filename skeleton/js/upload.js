document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-upload');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData(form);
        // TODO: Kirim ke API
        alert('Upload — TODO: Integrasi API');
    });
});