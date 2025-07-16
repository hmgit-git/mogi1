FROM php:7.4.9-fpm

COPY php.ini /usr/local/etc/php/

RUN sed -i 's|http://deb.debian.org|http://archive.debian.org|g' /etc/apt/sources.list && \
    sed -i 's|http://security.debian.org|http://archive.debian.org|g' /etc/apt/sources.list && \
    apt update && \
    apt install -y \
      default-mysql-client \
      zlib1g-dev \
      libzip-dev \
      unzip \
      libcurl4-openssl-dev \
      libonig-dev && \
    docker-php-ext-install \
      pdo_mysql \
      zip \
      curl \
      mbstring

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

# GDライブラリをインストール
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
    libnss3 \
    libgconf-2-4 \
    libatk1.0-0 \
    libatk-bridge2.0-0 \
    libcups2 \
    libxss1 \
    libasound2 \
    && rm -rf /var/lib/apt/lists/*

