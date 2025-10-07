FROM php:8.2-fpm-alpine3.17

RUN apk add --no-cache \
    git \
    unzip \
    icu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    bash \
 && docker-php-ext-configure gd \
      --with-freetype \
      --with-jpeg \
 && docker-php-ext-install \
      pdo_mysql \
      mbstring \
      intl \
      zip \
      gd \
      opcache \
 && rm -rf /var/cache/apk/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
