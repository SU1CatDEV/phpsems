FROM php:8.2-fpm

COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions memcached

RUN apt-get update && apt-get -y install zip unzip memcached && service memcached start
RUN docker-php-ext-install mysqli pdo pdo_mysql 

WORKDIR /data

VOLUME /data

CMD ["php-fpm"]
