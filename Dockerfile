FROM php:7.2.2-apache

# Install mysqli extension and enable apache rewrite + allow .htaccess overrides
RUN docker-php-ext-install mysqli \
 && a2enmod rewrite \
 && sed -ri "s/AllowOverride None/AllowOverride All/g" /etc/apache2/apache2.conf

# berridaztea ahalbidetu .php ez ikusteko
RUN a2enmod rewrite \
 && sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf