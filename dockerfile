# Gunakan image dasar PHP FPM resmi (misalnya PHP 8.2)
FROM php:8.2-fpm-alpine

# Update paket dan instal Nginx, bersama dengan ekstensi PHP yang dibutuhkan
RUN apk update && apk add --no-cache \
    nginx \
    php82-mysqli \
    php82-pdo_mysql \
   
    && rm -rf /var/cache/apk/*


RUN rm -rf /etc/nginx/conf.d/*


COPY nginx.conf /etc/nginx/conf.d/default.conf


WORKDIR /var/www/html
COPY . /var/www/html


RUN chown -R www-data:www-data /var/www/html

EXPOSE 8080


CMD sh -c "php-fpm -D && nginx -g 'daemon off;'"