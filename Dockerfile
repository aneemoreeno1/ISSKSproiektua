FROM php:7.2.2-apache
# Segurtasun goiburuak gehitu
RUN echo "Header always set X-Content-Type-Options \"nosniff\"" >> /etc/apache2/conf-enabled/security.conf
RUN echo "Header unset X-Powered-By" >> /etc/apache2/conf-enabled/security.conf
RUN echo "Header always set X-Frame-Options \"SAMEORIGIN\"" >> /etc/apache2/conf-enabled/security.conf
RUN echo "Header always set Strict-Transport-Security \"max-age=31536000; includeSubDomains; preload\"" >> /etc/apache2/conf-enabled/security.conf
RUN echo "Header always set Content-Security-Policy \"default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self';"" >> /etc/apache2/conf-enabled/security.conf


# Instalatu mysqli eta berbideralketa ahalbideratu
RUN docker-php-ext-install mysqli \
 && a2enmod rewrite \
 && sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
 && echo '<Directory /var/www/html/>\n\tAllowOverride All\n</Directory>' >> /etc/apache2/apache2.conf
 
RUN echo "ServerTokens Prod" >> /etc/apache2/apache2.conf \
 && echo "ServerSignature Off" >> /etc/apache2/apache2.conf

RUN echo "ErrorDocument 404 /errorea.html" >> /etc/apache2/apache2.conf

RUN a2enmod ssl

COPY ssl/localhost.crt /etc/ssl/certs/
COPY ssl/localhost.key /etc/ssl/private/

COPY ssl-config.conf /etc/apache2/sites-available/000-default.conf

COPY no-expose.ini /usr/local/etc/php/conf.d/99-no-expose.ini
