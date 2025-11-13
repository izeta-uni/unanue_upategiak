
# Composer mendekotasunak
FROM composer:2 as vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Produkzioaren azken irudia
FROM php:8.2-apache

# PHP-rako beharrezko luzapenak instalatu
RUN apt-get update && apt-get install -y libzip-dev zip \
  && docker-php-ext-install mysqli zip

# Apache mod_rewrite gaitatu
RUN a2enmod rewrite

# Aplikazioko kodea kopiatu
WORKDIR /var/www/html
COPY . .

# Lehen urratseko mendekotasunak kopiatu
COPY --from=vendor /app/vendor/ /var/www/html/vendor/

# Baimen egokiak ezarri
RUN chown -R www-data:www-data /var/www/html
