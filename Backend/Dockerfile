# ... (kode Dockerfile Anda sebelumnya) ...

# Copy entrypoint script dan beri izin eksekusi
COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

# Gunakan entrypoint script kita
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]