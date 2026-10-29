FROM php:8.2-apache

# Install ekstensi mysqli untuk koneksi database
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy semua file backend ke folder server
COPY . /var/www/html/

# Beri izin akses agar PHP bisa upload file gambar
RUN chown -R www-data:www-data /var/www/html

# PERBAIKAN: Paksa hanya satu MPM yang aktif saat container menyala
CMD ["bash", "-lc", "a2dismod mpm_event mpm_worker || true; a2enmod mpm_prefork; exec apache2-foreground"]