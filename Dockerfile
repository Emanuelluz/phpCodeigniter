# Dockerfile
FROM shinsenter/codeigniter4:latest

# Copia TODO o projeto (app/, public/, writable/, etc.)
COPY . /var/www/html

# Garante permissões no writable
RUN chown -R www-data:www-data /var/www/html/writable \
 && chmod -R 775 /var/www/html/writable

# Opcional: rodar composer install se não tiver vendor/
#COPY composer.lock composer.json /var/www/html/
#RUN composer install --no-dev --optimize-autoloader