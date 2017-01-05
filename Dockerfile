FROM mijndomein/docker-demo:base

COPY demo/ /var/www/app/demo
COPY vendor/ /var/www/app/vendor
COPY var/consul/config /etc/consul-template.conf.d
COPY var/consul/template var/consul/template

CMD /usr/local/bin/consul-template -config /etc/consul-template.conf.d -exec="apache2-foreground"
