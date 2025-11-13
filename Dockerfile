FROM php:7.2.2-apache

# Instalar mysqli y habilitar mod_rewrite y mod_headers
RUN docker-php-ext-install mysqli \
 && a2enmod rewrite headers ssl \
 && sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
 && echo '<Directory /var/www/html/>\n\tAllowOverride All\n</Directory>' >> /etc/apache2/apache2.conf

# Seguridad Apache
RUN echo "ServerTokens Prod" >> /etc/apache2/apache2.conf \
 && echo "ServerSignature Off" >> /etc/apache2/apache2.conf \
 && echo "ErrorDocument 404 /errorea.html" >> /etc/apache2/apache2.conf

# Copiar SSL
COPY ssl/localhost.crt /etc/ssl/certs/
COPY ssl/localhost.key /etc/ssl/private/
COPY ssl-config.conf /etc/apache2/sites-available/000-default.conf

# Copiar app
COPY app/ /var/www/html/

# Copiar configuración PHP segura
#COPY php-conf/99-no-expose.ini /usr/local/etc/php/conf.d/

# Exponer puerto HTTPS
EXPOSE 443
