# Dockerfile
FROM php:8.2-apache

# Dependências
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev libzip-dev zip unzip git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql zip

# Ativa o mod_rewrite para CodeIgniter
RUN a2enmod rewrite

# Copia a aplicação
WORKDIR /var/www/html
COPY . .

# Configura permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80
