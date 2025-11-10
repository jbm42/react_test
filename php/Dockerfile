FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends curl \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_mysql \
    && a2enmod headers

COPY php/apache-cache.conf /etc/apache2/conf-enabled/cache-headers.conf

WORKDIR /var/www/html
COPY ./php/public/ ./
