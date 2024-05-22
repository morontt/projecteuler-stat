FROM php:7.4-apache-buster

MAINTAINER Alexander Kharchenko <morontt@yandex.ru>

ARG HOST_UID=1000

ENV DEBIAN_FRONTEND noninteractive
ENV TZ "Europe/Moscow"
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
RUN apt-get update && apt-get install -y --no-install-recommends \
    libicu-dev libzip-dev python3-pygments \
    && docker-php-ext-install -j$(nproc) intl zip opcache pdo_mysql \
    && cp ${PHP_INI_DIR}/php.ini-production ${PHP_INI_DIR}/php.ini \
    && sed -i 's/;date.timezone =/date.timezone = Europe\/Moscow/' ${PHP_INI_DIR}/php.ini \
    && a2enmod rewrite remoteip && a2dismod deflate -f

COPY ./apache.conf /etc/apache2/sites-available/000-default.conf

RUN set -x && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

RUN usermod -u ${HOST_UID} www-data && groupmod -g ${HOST_UID} www-data

WORKDIR "/var/www/pe-stat"
