FROM php:7.2.2-apache

# Copy PHP security configuration
COPY app/php-security.ini /usr/local/etc/php/conf.d/security.ini

# Instalatu mysqli eta berbideralketa ahalbideratu
RUN docker-php-ext-install mysqli \
 && a2enmod rewrite \
 && a2enmod headers \
 && sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
 && echo '<Directory /var/www/html/>\n\tAllowOverride All\n</Directory>' >> /etc/apache2/apache2.conf \
 && echo 'ServerTokens Prod' >> /etc/apache2/apache2.conf \
 && echo 'ServerSignature Off' >> /etc/apache2/apache2.conf
