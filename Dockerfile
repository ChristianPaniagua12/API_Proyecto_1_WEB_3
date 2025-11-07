# Imagen base de PHP con Apache
FROM php:8.2-apache

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo pdo_mysql

# Copiar configuración personalizada de Apache
COPY apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Copiar la app al contenedor
COPY . /var/www/html/

# Dar permisos
RUN chown -R www-data:www-data /var/www/html

# Habilitar módulos y mantener Apache activo
RUN a2enmod rewrite
EXPOSE 80
CMD ["apache2-foreground"]
