#!/bin/bash
set -e

# 1) Pastikan hanya MPM prefork aktif
a2dismod -f mpm_event mpm_worker >/dev/null 2>&1 || true
rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.*
a2enmod mpm_prefork >/dev/null 2>&1 || true

# 2) Railway memberi port lewat $PORT -> Apache harus listen di port itu
PORT="${PORT:-80}"
sed -ri "s/^Listen [0-9]+$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s#<VirtualHost \*:[0-9]+>#<VirtualHost *:${PORT}>#" /etc/apache2/sites-available/000-default.conf

# 3) Folder upload (jika memakai Railway Volume di /var/www/html/uploads, folder awalnya kosong)
mkdir -p /var/www/html/uploads/{lapangan,makanan,bukti,profil}
cp -rn /opt/seed-uploads/. /var/www/html/uploads/ 2>/dev/null || true   # foto bawaan, tidak menimpa
chown -R www-data:www-data /var/www/html/uploads

# 4) Siapkan database (tabel + data awal + admin). Gagal tidak menghentikan server.
php /opt/app/scripts/init_db.php || echo "[entrypoint] init_db gagal (diabaikan)"

echo "[entrypoint] Apache listen di port ${PORT}"
exec apache2-foreground
