FROM php:7.0-cli

RUN pecl install xdebug-2.5.1 \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-enable xdebug pdo_mysql