<footer class="bg-slate-200/70 py-10">
  <div class="max-w-6xl mx-auto px-4 sm:px-6">
    <div class="flex items-center gap-1 text-xl font-extrabold text-navy">
      <svg class="w-5 h-5 text-orange-500" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2 4 14h6l-1 8 9-12h-6l1-8z"/></svg>
      Vincent <span class="text-accent">Arena</span>
    </div>
    <p class="mt-1 text-[11px] font-semibold tracking-[0.25em] text-slate-500">BOOKING LAPANGAN FUTSAL</p>
  </div>
</footer>

<script>
  // Fungsi toggle menu mobile
  document.getElementById('menuBtn')?.addEventListener('click', () => {
    document.getElementById('mobileMenu').classList.toggle('hidden');
  });

  // ==== MATIKAN MENU YANG BELUM SIAP ====
  document.addEventListener('DOMContentLoaded', function () {
    // Daftar halaman yang BELUM SIAP (tambahkan sesuai kebutuhan)
    const halamanBelumSiap = [
      'lapangan.php',
      'riwayat_booking.php',
      'makanan.php',
      'riwayat.php'
    ];

    // Cari semua link di dalam header
    const semuaLink = document.querySelectorAll('header a');

    semuaLink.forEach(function (link) {
      const href = link.getAttribute('href') || '';

      // Cek apakah link mengarah ke halaman yang belum siap
      const isBelumSiap = halamanBelumSiap.some(function (file) {
        return href.includes(file);
      });

      if (isBelumSiap) {
        // Ganti href jadi kosong agar tidak bisa pindah
        link.setAttribute('href', 'javascript:void(0);');
        link.setAttribute('onclick', 'return false;');

        // Style visual: abu-abu, kursor larangan
        link.style.pointerEvents = 'auto';   // biar cursor not-allowed muncul
        link.style.cursor = 'not-allowed';
        link.style.color = '#94a3b8';
        link.style.opacity = '0.6';

        // Hapus efek hover
        link.classList.remove('hover:text-navy', 'hover:bg-slate-100');
      }
    });
  });
</script>
</body>
</html>