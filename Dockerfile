FROM composer:2.0.7 as composer

FROM registry.cn-chengdu.aliyuncs.com/happyceclear/php_base_image:latest

WORKDIR /www/web/mall-admin

COPY  . /www/web/mall-admin
#COPY .
COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/ \
    && composer install  --ignore-platform-reqs \
    && chmod -R 777 storage \
    && cp .env.example .env \
    && php artisan key:generate

EXPOSE 9000
