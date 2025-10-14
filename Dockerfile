# Usa imagem oficial do PHP com Apache
FROM php:8.2-apache

# Instala extensões necessárias para CodeIgniter 4
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo mbstring gd xml

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia apenas os arquivos de dependência
COPY composer.json composer.lock ./

# Instala dependências (sem dev)
RUN composer install --no-dev --optimize-autoloader

# Copia o restante da aplicação
COPY . .

# Ajusta permissões do diretório writable
RUN chown -R www-data:www-data /var/www/html/writable \
 && chmod -R 775 /var/www/html/writable

# Habilita mod_rewrite (importante para CI4)
RUN a2enmod rewrite

# Expose porta (opcional, Apache já expõe 80)
EXPOSE 80
