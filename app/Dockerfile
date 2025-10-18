FROM php:8.1-apache

# Instalar extensiones comunes de PHP y MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Activar mod_rewrite (necesario para proyectos con .htaccess)
RUN a2enmod rewrite

# Copiar el contenido de la carpeta "app" al contenedor
COPY . /var/www/html/

# Ajustar permisos
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
