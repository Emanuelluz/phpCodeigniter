# Dockerfile
FROM php:8.3-apache

# Dependências de sistema e libs para compilar extensões
# - bookworm usa libjpeg62-turbo-dev (não libjpeg-dev)
# - intl precisa de libicu-dev e toolchain (g++)
# - mbstring pode exigir libonig-dev em algumas bases
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libzip-dev \
        libicu-dev \
        libonig-dev \
        g++ make autoconf pkg-config \
        git unzip zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd mysqli pdo_mysql zip intl mbstring \
    && rm -rf /var/lib/apt/lists/*

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Ativa o mod_rewrite para CodeIgniter
RUN a2enmod rewrite

# Configura o document root para public/
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Permite overrides no .htaccess
RUN echo '<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

# Permite overrides no .htaccess em public/
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

# Copia a aplicação
WORKDIR /var/www/html
COPY . .

# Configura arquivo .env para Docker (produção/container)
RUN chmod +x scripts/setup-env-docker.sh && scripts/setup-env-docker.sh

# Instala dependências
RUN git config --global --add safe.directory /var/www/html
# Ao adicionar novas dependências (ex.: Shield) o composer.lock precisa ser atualizado
# Use update para sincronizar o lock e instalar dependências, depois otimiza autoloader
RUN composer update --no-dev --prefer-dist --no-interaction \
    && composer dump-autoload -o

# Configura CodeIgniter Shield (instala configs no app/Config)
RUN php spark shield:setup || true

# Configura permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80
