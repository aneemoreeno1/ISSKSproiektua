FROM php:7.2.2-apache

# Instalatu mysqli eta berbideralketa ahalbideratu
RUN docker-php-ext-install mysqli \
 && a2enmod rewrite \
 && sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
 && echo '<Directory /var/www/html/>\n\tAllowOverride All\n</Directory>' >> /etc/apache2/apache2.conf
 
RUN echo "ServerTokens Prod" >> /etc/apache2/apache2.conf \
 && echo "ServerSignature Off" >> /etc/apache2/apache2.conf

RUN echo "ErrorDocument 404 /errorea.html" >> /etc/apache2/apache2.conf

COPY no-expose.ini /usr/local/etc/php/conf.d/99-no-expose.ini
