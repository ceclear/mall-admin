FROM composer:2.0.7 as composer

FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
         libfreetype6-dev \
         libjpeg62-turbo-dev \
         libpng-dev \
         libssl-dev \
         libzip-dev \
     && apt-get autoclean && apt-get clean \
     && docker-php-ext-configure gd --with-freetype --with-jpeg \
     && docker-php-ext-install -j$(nproc) gd \
     && pecl install redis-5.0.0 \
     && docker-php-ext-enable redis \
     && docker-php-ext-install pdo_mysql \
     && docker-php-ext-install mysqli \
     && docker-php-ext-install opcache \
     && docker-php-ext-install bcmath \
     && docker-php-ext-install zip

WORKDIR /www/web

COPY . .

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/ \
    && composer install

EXPOSE 9000
