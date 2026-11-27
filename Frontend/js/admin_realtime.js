/* admin_realtime.js — logika bersama halaman admin (auth, API, polling realtime) */
(function () {
  const API_BASE = /localhost|127\.0\.0\.1/.test(location.hostname)
    ? 'http://localhost/vincent-arena/Backend/API'
    : 'https://sistem-booking-lapangan-futsal-berbasis-webfron-production.up.railway.app/API';

  // >>> SESUAIKAN dengan nama file endpoint admin di backend Anda <<<
  const EP = {
    bookings: '/admin/booking_list.php',        // GET daftar booking (alternatif: /admin/bookings.php)
    bookingDetail: '/admin/booking_detail.php', // GET ?id=
    bookingStatus: '/admin/booking_status.php', // POST {booking_id, status}
    lapangan: '/admin/lapangan_list.php',       // GET daftar lapangan
    lapanganCreate: '/admin/lapangan_create.php',
    lapanganUpdate: '/admin/lapangan_update.php',
    lapanganDelete: '/admin/lapangan_delete.php',
    statistik: '/admin/statistik.php'
  };
  const POLL_MS = 5000;

  const STATUS = {
    menunggu_pembayaran: ['Menunggu Pembayaran', '#b45309', '#fef3c7'],
    menunggu_konfirmasi: ['Menunggu Konfirmasi', '#1d4ed8', '#dbeafe'],
    dikonfirmasi: ['Dikonfirmasi', '#15803d', '#dcfce7'],
    ditolak: ['Ditolak', '#b91c1c', '#fee2e2'],
    dibatalkan: ['Dibatalkan', '#555', '#e5e7eb']
  };

  /* ---- identitas & guard ---- */
  const rd = k => { try { return JSON.parse(localStorage.getItem(k)); } catch (e) { return null; } };
  const admin = rd('vsq_admin'), cust = rd('vsq_user'), user = admin || cust;   // sesuaikan key jika login admin Anda menyimpan di key lain
  // Akses admin ditentukan oleh sesi PHP (requireAdminLogin) — lihat penanganan 401/403 di api()

  /* ---- API (otomatis multipart jika body = FormData) ---- */
  async function api(path, opt) {
    opt = opt || {};
    const hd = opt.body instanceof FormData ? {} : { 'Content-Type': 'application/json' };
    const res = await fetch(API_BASE + path, Object.assign({ credentials: 'include', cache: 'no-store', headers: hd }, opt));
    if (res.status === 401) { location.replace('login.html'); throw new Error('Sesi berakhir'); }
    if (res.status === 403) { location.replace(cust ? 'akses_ditolak.html' : 'login.html'); throw new Error('Akses ditolak'); }
    const j = await res.json().catch(() => ({}));
    if (!res.ok || j.success === false) throw new Error(j.message || 'HTTP ' + res.status);
    api.msg = j.message || '';
    return j.data !== undefined ? j.data : j;
  }
  async function logout() {
    try { await fetch(API_BASE + '/auth/logout.php', { method: 'POST', credentials: 'include', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ type: 'admin' }) }); } catch (e) {}
    localStorage.clear(); location.href = 'login.html';
  }

  /* ---- normalisasi baris booking (menerima beberapa kemungkinan nama kolom) ---- */
  const pad = n => String(n).padStart(2, '0');
  function nb(r) {
    const jm = String(r.jam_mulai || '').slice(0, 5), d = +r.durasi || 1, h = parseInt(jm, 10) || 0;
    let b = r.bukti_url || r.bukti_pembayaran || r.bukti || '';
    if (b && !/^(https?:|data:)/.test(b)) b = API_BASE.replace(/\/API$/, '') + '/' + b.replace(/^\/+/, '');
    return {
      id: +(r.booking_id || r.id) || 0, kode: r.kode_booking || r.kode || '-',
      nama: r.nama_pemesan || String(r.catatan || '').replace(/^Atas nama:\s*/i, '') || r.nama || '-',
      lap: r.nama_lapangan || r.lapangan || 'Lapangan ' + (r.id_lapangan || r.lapangan_id),
      lapId: String(r.id_lapangan || r.lapangan_id), tgl: String(r.tanggal || '').slice(0, 10),
      jam: jm, jamSelesai: pad(h + d) + ':00', durasi: d, h,
      total: +(r.total_harga || r.total || 0),
      status: String(r.status || '').toLowerCase().trim().replace(/\s+/g, '_'), bukti: b
    };
  }

  /* ---- utilitas UI ---- */
  const esc = s => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const rp = n => 'Rp' + (+n || 0).toLocaleString('id-ID');
  const st = document.createElement('style');
  st.textContent = '.vt{position:fixed;top:20px;left:50%;transform:translateX(-50%);background:#131C2F;color:#fff;padding:12px 20px;border-radius:12px;font-size:14px;z-index:9999;box-shadow:0 8px 24px rgba(0,0,0,.2);max-width:90%}.vt.e{background:#b91c1c}';
  document.head.appendChild(st);
  let tt;
  function toast(m, err) {
    let t = document.getElementById('vt');
    if (!t) { t = document.createElement('div'); t.id = 'vt'; document.body.appendChild(t); }
    t.className = 'vt' + (err ? ' e' : ''); t.textContent = m; t.style.display = 'block';
    clearTimeout(tt); tt = setTimeout(() => t.style.display = 'none', 4500);
  }
  function live(ok) {
    const el = document.getElementById('live'); if (!el) return;
    el.classList.toggle('off', !ok);
    el.querySelector('span').textContent = ok ? 'Live · diperbarui ' + new Date().toLocaleTimeString('id-ID') : 'Terputus · mencoba lagi...';
  }

  /* ---- realtime: polling + sinkron antar tab ---- */
  const bc = 'BroadcastChannel' in window ? new BroadcastChannel('vsq') : null;
  function poll(fn, ms) {
    let busy = false;
    const run = async () => { if (busy || document.hidden) return; busy = true; try { await fn(); } finally { busy = false; } };
    run(); setInterval(run, ms || POLL_MS);
    document.addEventListener('visibilitychange', run);
    window.addEventListener('online', run);
    if (bc) bc.onmessage = run;
    return run;
  }
  const ping = () => bc && bc.postMessage('refresh');

  /* ---- isi data user & tanggal di sidebar/header ---- */
  document.addEventListener('DOMContentLoaded', () => {
    const lo = document.getElementById('lo'); if (lo) lo.onclick = logout;
    if (!user) return;
    const nama = user.nama || user.name || user.username || 'Admin';
    document.querySelectorAll('.ucard b').forEach(e => e.textContent = nama.split(' ')[0]);
    document.querySelectorAll('.ucard .av,.dd .av').forEach(e => e.textContent = nama.charAt(0).toUpperCase());
    document.querySelectorAll('.dd div').forEach(e => { if (e.firstChild && e.firstChild.nodeType === 3) e.firstChild.textContent = nama.split(' ')[0]; });
    const p = document.querySelector('header p');
    if (p) p.textContent = new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
  });

  window.VSQ = { logout, api, EP, STATUS, nb, esc, rp, toast, live, poll, ping, pad };
})();
