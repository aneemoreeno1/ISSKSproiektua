FROM php:7.2.2-apache

# Instala la extensión mysqli requerida por la app
RUN docker-php-ext-install mysqli

# Habilita mod_rewrite, permite .htaccess (AllowOverride All) y añade ServerName
# para evitar el warning sobre FQDN. Esto hace la configuración persistente en la imagen.
RUN a2enmod rewrite \
 && sed -ri "s/AllowOverride None/AllowOverride All/g" /etc/apache2/apache2.conf \
 && printf 'ServerName localhost\n' > /etc/apache2/conf-available/servername.conf \
 && a2enconf servername