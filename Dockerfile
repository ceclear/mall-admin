FROM composer:2.0.7 as build

WORKDIR /www/web

COPY . .

RUN composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/ \
    && composer install

FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

WORKDIR /www/web

COPY --from=build /www/web /www/web

EXPOSE 9000
