FROM php:7.1-fpm-alpine

MAINTAINER Alexander Harchenko <morontt@gmail.com>

RUN apk --no-cache add tzdata shadow icu-dev py-pygments
RUN cp /usr/share/zoneinfo/Europe/Kiev /etc/localtime \
    && echo "Europe/Kiev" >  /etc/timezone \
    && cp $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

RUN docker-php-ext-install -j$(nproc) intl opcache pdo_mysql

RUN set -x && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

COPY . /var/www/pe-stat

RUN cd /var/www/pe-stat \
    && composer install \
    && chown -R www-data:www-data /var/www/pe-stat

WORKDIR "/var/www/pe-stat"
