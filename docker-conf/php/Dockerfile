FROM php:8.2-fpm-alpine

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apk update && apk add --no-cache \
     zlib-dev g++ git icu-dev libzip-dev libpng-dev libjpeg-turbo-dev zip unzip libpq-dev wget gnupg bash udev openssl phpunit  \
    && docker-php-ext-install intl opcache pdo mysqli pdo_mysql zip gd \
    && docker-php-ext-configure gd --with-jpeg \
    && pecl install apcu redis \
    && docker-php-ext-enable apcu redis \
    && docker-php-ext-enable pdo_mysql \
    && docker-php-ext-configure zip



ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD true

RUN apk update && apk add --no-cache chromium chromium-chromedriver  \
    && rm -rf /var/cache/apk/*

# Install Node.js and npm
RUN apk add --no-cache nodejs npm

ENV PUPPETEER_EXECUTABLE_PATH='/usr/bin/chromium-browser'

# Install other project dependencies
COPY package*.json ./

WORKDIR /var/www/project
COPY . /var/www/project

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash -s -- --install-dir=/usr/local/bin
RUN mv /usr/local/bin/symfony /usr/local/bin/symfony

EXPOSE 9800

# set the default command for Docker container to run PHPUnit CMD ["phpunit"]