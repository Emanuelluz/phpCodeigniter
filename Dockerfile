# Dockerfile para aplicação CodeIgniter
FROM shinsenter/codeigniter4:latest

# Instala o Composer caso a imagem não tenha (opcional, pois algumas já vêm com composer)
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copia apenas arquivos de dependência primeiro para camada de cache
COPY composer.json composer.lock /var/www/html/

# Atualiza as dependências do projeto
WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader || composer update --no-dev --optimize-autoloader

# Copia o restante do projeto
COPY . /var/www/html

# Garante permissões no writable
RUN chown -R www-data:www-data /var/www/html/writable \
 && chmod -R 775 /var/www/html/writable
