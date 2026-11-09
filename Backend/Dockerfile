FROM php:8.2-apache

# Instal ekstensi PHP cURL untuk komunikasi dengan Brevo API
RUN apt-get update \
 && apt-get install -y --no-install-recommends libcurl4-openssl-dev \
 && docker-php-ext-install curl \
 && rm -rf /var/lib/apt/lists/*

# Ekstensi database + modul Apache (rewrite untuk .htaccess, headers)
# Satu MPM saja (prefork, wajib untuk mod_php) -> mencegah error "More than one MPM loaded" di Railway
RUN docker-php-ext-install mysqli pdo pdo_mysql \
 && a2dismod -f mpm_event mpm_worker || true \
 && a2enmod mpm_prefork rewrite headers

COPY docker/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/apache.conf /etc/apache2/conf-available/app.conf
RUN a2enconf app

# Kode backend
COPY . /var/www/html/

# Skrip internal ditaruh DI LUAR web root (tidak bisa diakses lewat URL)
RUN mkdir -p /opt/app \
 && mv /var/www/html/scripts /opt/app/scripts \
 && mv /var/www/html/database /opt/app/database \
 && rm -rf /var/www/html/docker /var/www/html/dokumen /var/www/html/entrypoint.sh /var/www/html/Dockerfile \
 && mkdir -p /opt/seed-uploads && cp -r /var/www/html/uploads/. /opt/seed-uploads/

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh \
 && chown -R www-data:www-data /var/www/html

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
