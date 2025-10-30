FROM php:7.2.2-apache

# Instalatu mysqli eta berbideralketa ahalbideratu
RUN docker-php-ext-install mysqli \
 && a2enmod rewrite \
 && sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
 && echo '<Directory /var/www/html/>\n\tAllowOverride All\n</Directory>' >> /etc/apache2/apache2.conf
