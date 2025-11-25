FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

ENV PORT=8080
RUN sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
RUN sed -i "s/:80/:${PORT}/" /etc/apache2/sites-enabled/000-default.conf

COPY . /var/www/html/

EXPOSE ${PORT}
