# Imagen base de PHP con Apache
FROM php:8.2-apache

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo pdo_mysql

# Copiar el proyecto al contenedor
COPY . /var/www/html/

# Dar permisos al contenido
RUN chown -R www-data:www-data /var/www/html

# Habilitar el módulo de reescritura de Apache
RUN a2enmod rewrite

# Exponer el puerto estándar
EXPOSE 80

# Mantener Apache en ejecución en primer plano
CMD ["apache2-foreground"]
