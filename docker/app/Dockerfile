FROM php:7.4.2-fpm-alpine3.10

ADD ./custom.ini /usr/local/etc/php/conf.d/custom.ini
ADD ./php-fpm-custom.conf /etc/php-fpm.d/php-fpm-custom.conf
RUN sed -i '/\[www\]/a include = \/etc\/php-fpm.d\/php-fpm-custom.conf' /usr/local/etc/php-fpm.d/www.conf

RUN apk update \
    && apk add libmcrypt-dev git unzip libpq postgresql-dev libzip-dev zip gnupg autoconf make libc-dev gcc oniguruma-dev bash \
    && pecl install xdebug-2.8.1 \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-configure zip \
    && docker-php-ext-install mbstring tokenizer pdo pdo_pgsql pgsql zip

ENV PATH /composer/vendor/bin:$PATH
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_VERSION 1.9.0
ENV COMPOSER_HOME /composer

# Setup the Composer installer
RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
  && curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
  && php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" \
  && php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --version=${COMPOSER_VERSION} && rm -rf /tmp/composer-setup.php

WORKDIR /var/www/app

# Add local web-user
ARG WEB_USER_ID=33
ARG WEB_USER_NAME=www-data
RUN echo "Building for web user: id=${WEB_USER_ID} name=${WEB_USER_NAME}" \
    && adduser -u ${WEB_USER_ID} ${WEB_USER_NAME} || echo "Users exists" \
    && sed -i -- "s/user = www-data/user = ${WEB_USER_NAME}/g" /usr/local/etc/php-fpm.d/www.conf

USER ${WEB_USER_ID}

CMD ["php-fpm"]
