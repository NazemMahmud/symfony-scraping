FROM php:8.2-fpm-alpine


ENV COMPOSER_ALLOW_SUPERUSER 1
ENV PANTHER_WEB_SERVER_PORT 9800

# Chromium and ChromeDriver
ENV PANTHER_NO_SANDBOX 1
# Not mandatory, but recommended
ENV PANTHER_CHROME_ARGUMENTS='--disable-dev-shm-usage'
# Firefox and GeckoDriver (optional)
ARG GECKODRIVER_VERSION=0.28.0


# these are for apt-get gnupg2 libnss3-tools
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

#RUN apt-get add --no-cache firefox libzip-dev; \
#    docker-php-ext-install zip
#RUN apt-get update && apt-get install -y firefox libzip-dev; \
#    docker-php-ext-install zip
#
#RUN wget -q https://github.com/mozilla/geckodriver/releases/download/v$GECKODRIVER_VERSION/geckodriver-v$GECKODRIVER_VERSION-linux64.tar.gz; \
#    tar -zxf geckodriver-v$GECKODRIVER_VERSION-linux64.tar.gz -C /usr/bin; \
#    rm geckodriver-v$GECKODRIVER_VERSION-linux64.tar.gz

# Install the ChromeDriver binary (adjust the version number if needed)
ARG CHROMEDRIVER_VERSION=96.0.4664.45
RUN wget -q -O /tmp/chromedriver.zip https://chromedriver.storage.googleapis.com/$CHROMEDRIVER_VERSION/chromedriver_linux64.zip \
    && unzip /tmp/chromedriver.zip -d /usr/local/bin/ \
    && chmod +x /usr/local/bin/chromedriver \
    && rm /tmp/chromedriver.zip

# Set the ChromeDriver path for Symfony Panther
ENV PANTHER_CHROME_DRIVER_BINARY /usr/local/bin/chromedriver

WORKDIR /var/www/project
COPY . /var/www/project


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash -s -- --install-dir=/usr/local/bin
RUN mv /usr/local/bin/symfony /usr/local/bin/symfony
RUN composer require --dev dbrekelmans/bdi

EXPOSE 9800
