FROM php:7.2-alpine

RUN apk add --no-cache autoconf g++ make && \
    wget https://github.com/phpredis/phpredis/archive/develop.zip && unzip develop.zip && cd phpredis-develop && phpize && ./configure && make && make install && \
    docker-php-ext-enable redis && \
    apk del autoconf g++ make
