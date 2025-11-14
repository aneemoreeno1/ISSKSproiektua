FROM php:7.2.2-apache

# Instalatu mysqli eta berbideralketa ahalbideratu
RUN docker-php-ext-install mysqli \
    && a2enmod rewrite headers ssl \
    && sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
    && echo '<Directory /var/www/html/>' >> /etc/apache2/apache2.conf \
    && echo '    AllowOverride All' >> /etc/apache2/apache2.conf \
    && echo '</Directory>' >> /etc/apache2/apache2.conf

# Segurtasun goiburuak gehitu (un solo RUN para evitar errores)
RUN { \
    echo "Header unset Server"; \
    echo "Header unset X-Powered-By"; \
    echo "Header always set X-Frame-Options \\\"SAMEORIGIN\\\""; \
    echo "Header always set Content-Security-Policy \\\"default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self';\\\""; \
    echo "Header always set Strict-Transport-Security \\\"max-age=31536000; includeSubDomains; preload\\\""; \
    echo "Header always set X-Content-Type-Options \\\"nosniff\\\""; \
} >> /etc/apache2/conf-enabled/security.conf

# Configuración del servidor (sin duplicados)
RUN { \
    echo "ServerTokens Prod"; \
    echo "ServerSignature Off"; \
    echo "ErrorDocument 404 /errorea.html"; \
} >> /etc/apache2/apache2.conf

# SSL
COPY ssl/localhost.crt /etc/ssl/certs/
COPY ssl/localhost.key /etc/ssl/private/
COPY ssl-config.conf /etc/apache2/sites-available/000-default.conf

COPY no-expose.ini /usr/local/etc/php/conf.d/99-no-expose.ini

