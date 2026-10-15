// faq.js - FAQ Accordion

document.addEventListener('DOMContentLoaded', function() {
    const faqData = [
        { no: '1.', q: 'Apakah booking dapat dibatalkan?', a: 'Bisa, pada menu "Riwayat Booking" pilih lapangan lalu klik "Batalkan".' },
        { no: '2.', q: 'Bagaimana cara saya mengetahui booking berhasil?', a: 'Status booking akan terlihat di menu "Riwayat Booking".' },
        { no: '3.', q: 'Apakah saya bisa mengubah jadwal?', a: 'Bisa, hubungi kontak kami untuk perubahan jadwal.' },
        { no: '4.', q: 'Apakah bisa booking beberapa jadwal sekaligus?', a: 'Bisa, pilih beberapa jadwal yang kosong/tersedia.' }
    ];
    
    const container = document.getElementById('faq-container');
    if (!container) return;
    
    container.innerHTML = faqData.map(function(f) {
        return `
        <div class="vsq-faq-item" onclick="toggleFaq(this)">
            <div class="vsq-faq-header">
                <div class="faq-text">
                    <span class="faq-number">${f.no}</span>
                    <span>${f.q}</span>
                </div>
                <span class="faq-arrow">⌄</span>
            </div>
            <div class="vsq-faq-body">${f.a}</div>
        </div>`;
    }).join('');
});

function toggleFaq(element) {
    const isActive = element.classList.contains('active');
    document.querySelectorAll('.vsq-faq-item').forEach(function(item) {
        item.classList.remove('active');
    });
    if (!isActive) element.classList.add('active');
}