FROM php:7.0-cli

RUN apt-get update && apt-get install -y unzip git

COPY php.ini /usr/local/etc/php/

# Install and enable xdebug and pdo mysql
RUN pecl install xdebug-2.5.1 \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-enable xdebug pdo_mysql

# Install composer
RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
    && curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
    && php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" \
    && php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --snapshot \
    && rm -f /tmp/composer-setup.*