# Etapa base: PHP + extensiones necesarias
FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl libpq-dev libzip-dev unzip zip \
    && docker-php-ext-install pdo pdo_pgsql zip \
    && docker-php-ext-enable pdo_pgsql

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar composer.json y composer.lock primero para aprovechar cache
COPY ./app/composer.json ./app/composer.lock ./

# Instalar dependencias de PHP
RUN composer install --no-scripts --no-autoloader

# Copiar el código del proyecto
COPY ./app ./

# Ejecutar autoload
RUN composer dump-autoload --optimize

# Asignar permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer puerto interno PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]

# Asegurar permisos durante el runtime
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

