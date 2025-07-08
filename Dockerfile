FROM php:8.1-apache

# Instala extensiones necesarias
RUN docker-php-ext-install mysqli

# Copia todo el contenido del proyecto
COPY . /var/www/html/

# Habilita mod_rewrite
RUN a2enmod rewrite

# Configura Apache
COPY .htaccess /var/www/html/.htaccess

RUN mkdir -p /var/www/html/application/logs && chmod -R 777 /var/www/html/application/logs
