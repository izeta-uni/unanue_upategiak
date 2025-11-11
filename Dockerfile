FROM php:8.2-apache

# Sistemaren mendekotasunak eta PHP luzapenak instalatu
RUN apt-get update && apt-get install -y \
  libzip-dev \
  zip \
  && docker-php-ext-install mysqli zip

# Apache-ko rewrite modulua gaitu
RUN a2enmod rewrite

# Composer instalatu
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Laneko direktorioa ezarri
WORKDIR /var/www/html

# Aplikazioko fitxategiak kopiatu
COPY . .

# Composer-en mendekotasunak instalatu
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Fitxategien jabegoa www-data-era aldatu
RUN chown -R www-data:www-data /var/www/html
