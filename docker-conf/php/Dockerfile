FROM php:8.2-fpm-alpine

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV PANTHER_WEB_SERVER_PORT 9800

# Chromium and ChromeDriver
ENV PANTHER_NO_SANDBOX 1
# Not mandatory, but recommended
ENV PANTHER_CHROME_ARGUMENTS='--disable-dev-shm-usage'


RUN apk update && apk add --no-cache \
     zlib-dev g++ git icu-dev libzip-dev libpng-dev libjpeg-turbo-dev zip unzip libpq-dev wget gnupg bash udev openssl \
    && docker-php-ext-install intl opcache pdo pdo_mysql zip gd \
    && docker-php-ext-configure gd --with-jpeg \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip

#RUN  chromium-chromedriver
RUN apk update && apk add --no-cache chromium chromium-chromedriver  \
    && rm -rf /var/cache/apk/*

WORKDIR /var/www/project
COPY . /var/www/project


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash -s -- --install-dir=/usr/local/bin
RUN mv /usr/local/bin/symfony /usr/local/bin/symfony
RUN composer require --dev dbrekelmans/bdi

EXPOSE 9800
