# =============================================================================
# Dockerfile para Backend - Laravel + PHP-FPM
# =============================================================================
FROM php:8.3-fpm

# Variables de entorno
ENV DEBIAN_FRONTEND=noninteractive
ENV PHP_OPCACHE_ENABLE=1
ENV PHP_OPCACHE_MEMORY_CONSUMPTION=256
ENV PHP_PM_MAX_CHILDREN=50
ENV PHP_PM_START_SERVERS=10
ENV PHP_PM_MIN_SPARE_SERVERS=5
ENV PHP_PM_MAX_SPARE_SERVERS=20

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del backend
COPY backend/ .

# Instalar dependencias de Composer (producción)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Configurar PHP-FPM
RUN echo "pm.max_children = ${PHP_PM_MAX_CHILDREN}" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm.start_servers = ${PHP_PM_START_SERVERS}" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm.min_spare_servers = ${PHP_PM_MIN_SPARE_SERVERS}" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm.max_spare_servers = ${PHP_PM_MAX_SPARE_SERVERS}" >> /usr/local/etc/php-fpm.d/www.conf

# Exponer puerto 9000
EXPOSE 9000

CMD ["php-fpm"]
