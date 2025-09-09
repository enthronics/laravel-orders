# =======================================================
# PHP 8.2 + Apache Dockerfile Laravel 10 -projektille
# =======================================================

# Käytetään PHP 8.2 + Apache imagea
FROM php:8.2-apache

# Työhakemisto kontissa
WORKDIR /var/www/html

# Päivitetään pakettivarastot ja asennetaan PHP-laajennukset + työkalut
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    curl \
    libonig-dev \
    libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql zip mbstring \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Ota Apache rewrite-moduuli käyttöön
RUN a2enmod rewrite

# Kopioidaan koko projekti ensin, jotta artisan on olemassa
COPY . .

# Asennetaan Composer ja projektin riippuvuudet
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader

# Asetetaan oikeudet
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Asetetaan Apache DocumentRoot Laravelin public-hakemistoon
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Altistetaan portti 80
EXPOSE 80

# Käynnistetään Apache foreground-tilassa
CMD ["apache2-foreground"]
