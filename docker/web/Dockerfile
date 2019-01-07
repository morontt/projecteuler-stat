FROM php:7.1-fpm-alpine

MAINTAINER Alexander Harchenko <morontt@gmail.com>

ARG HOST_UID=1000

RUN apk --no-cache add tzdata shadow icu-dev
RUN cp /usr/share/zoneinfo/Europe/Kiev /etc/localtime \
    && echo "Europe/Kiev" >  /etc/timezone

COPY php.ini-development /usr/local/etc/php/php.ini

RUN docker-php-ext-install -j$(nproc) intl opcache pdo_mysql

RUN set -x && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

RUN usermod -u ${HOST_UID} www-data && groupmod -g ${HOST_UID} www-data

WORKDIR "/var/www/pe-stat"
