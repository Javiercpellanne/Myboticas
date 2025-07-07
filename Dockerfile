FROM php:8.1-apache

# Instala extensiones necesarias para MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copia el contenido del proyecto al servidor Apache
COPY . /var/www/html/

# Habilita mod_rewrite de Apache si usas .htaccess
RUN a2enmod rewrite

# Exponer el puerto por defecto de Apache
EXPOSE 80
