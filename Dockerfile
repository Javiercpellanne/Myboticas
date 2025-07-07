FROM php:8.1-apache

# Instala extensiones necesarias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copia el contenido de tu proyecto
COPY . /var/www/html/

# Da permisos
RUN chown -R www-data:www-data /var/www/html

# Habilita Apache rewrite module (CodeIgniter lo usa)
RUN a2enmod rewrite

# Configura Apache para usar index.php
RUN echo '<Directory /var/www/html/>\n\
    AllowOverride All\n\
</Directory>' > /etc/apache2/conf-available/override.conf \
    && a2enconf override

EXPOSE 80
