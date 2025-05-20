FROM php:8.1-apache

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

COPY static /var/www/html/static
COPY views /var/www/html/views
COPY index.php /var/www/html/index.php

WORKDIR /var/www/html

