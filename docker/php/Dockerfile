FROM php:7.4.9-fpm

COPY php.ini /usr/local/etc/php/

RUN apt update \
  && apt install -y \
      default-mysql-client \
      zlib1g-dev \
      libzip-dev \
      unzip \
      libcurl4-openssl-dev \
      libonig-dev \
  && docker-php-ext-install \
      pdo_mysql \
      zip \
      curl \
      mbstring

RUN curl -sS https://getcomposer.org/installer | php \
  && mv composer.phar /usr/local/bin/composer \
  && composer self-update

WORKDIR /var/www
