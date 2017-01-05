FROM mijndomein/docker-demo:base

RUN apt-get -q -y dist-upgrade

RUN apt-get update

COPY config/php.ini ${PHP_INI_DIR}/conf.d/php.ini
COPY script/install-composer /usr/local/bin/

RUN install-composer
