FROM php:7.1-apache

RUN apt-get update && apt-get -q -y install unzip wget

RUN rm /etc/apache2/sites-enabled/*

COPY config/demo.conf /etc/apache2/sites-enabled/demo.conf

COPY script/install-consul-template /usr/local/bin/

RUN install-consul-template

WORKDIR /var/www/app
