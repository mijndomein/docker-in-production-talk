FROM php:7.1-apache

RUN apt-get update && apt-get -q -y install unzip wget

RUN a2enmod rewrite
RUN docker-php-ext-install bcmath

RUN rm /etc/apache2/sites-enabled/*

COPY config/server.conf /etc/apache2/conf-enabled/server.conf
COPY config/app.conf /etc/apache2/sites-enabled/app.conf
COPY config/ports.conf /etc/apache2/ports.conf

COPY script/install-consul-template /usr/local/bin/

RUN install-consul-template

ENV APACHE_PID_FILE=/tmp/http.pid

WORKDIR /var/www/app

CMD exit
