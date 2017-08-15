FROM php:7.3.6-fpm

RUN mkdir -p /var/www/scripts
COPY scripts/ /var/www/scripts/php
RUN cd /var/www/scripts/php && bash ./provision.sh && rm -rf /var/www/scripts

COPY scripts/wait-for.sh /usr/local/bin/wait-for.sh
RUN chmod u+x /usr/local/bin/wait-for.sh

WORKDIR /var/www/html
