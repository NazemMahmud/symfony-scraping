FROM php:8.2-fpm

RUN apt-get update && apt-get install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip libpq-dev \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip


WORKDIR /var/www/project

COPY . /var/www/project

# Allow to select Symfony version
#ENV SYMFONY_VERSION=6.2

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
ENV PATH="/root/.symfony/bin:${PATH}"

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony
