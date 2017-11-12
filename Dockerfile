FROM php:7.1-fpm

RUN docker-php-ext-install pdo pdo_mysql

COPY docker/php.ini /usr/local/etc/php/conf.d/customphp.ini