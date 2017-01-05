FROM mijndomein/docker-demo:base

COPY demo/ /var/www/app/demo
COPY vendor/ /var/www/app/vendor
