FROM php:7.1-apache

RUN rm /etc/apache2/sites-enabled/*

COPY config/demo.conf /etc/apache2/sites-enabled/demo.conf

WORKDIR /var/www/app
