FROM mysql:5.7

MAINTAINER Alexander Harchenko <morontt@gmail.com>

VOLUME ["/var/lib/mysql"]

ARG HOST_UID=1000

RUN usermod -u ${HOST_UID} mysql \
    && groupmod -g ${HOST_UID} mysql

EXPOSE 3306
