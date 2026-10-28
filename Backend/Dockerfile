FROM php:8.2-apache

# Install ekstensi mysqli untuk koneksi database
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy semua file backend ke folder server
COPY . /var/www/html/

# Beri izin akses agar PHP bisa upload file gambar
RUN chown -R www-data:www-data /var/www/html