FROM php:8.1-apache

# Instala extensiones necesarias para MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilita mod_rewrite de Apache
RUN a2enmod rewrite

# Copia todos los archivos al servidor
COPY . /var/www/html/

# Establece la carpeta "public" como raíz del sitio
WORKDIR /var/www/html/public

# Corrige permisos si es necesario
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
