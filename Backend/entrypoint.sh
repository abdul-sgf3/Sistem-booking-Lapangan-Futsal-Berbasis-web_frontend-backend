#!/bin/bash
set -e

# Nonaktifkan MPM yang konflik dan pastikan hanya prefork yang aktif
a2dismod mpm_event mpm_worker 2>/dev/null || true
rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.*
a2enmod mpm_prefork 2>/dev/null || true

# Jalankan Apache
exec apache2-foreground