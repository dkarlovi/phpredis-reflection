FROM php:7.2-alpine

RUN apk add --no-cache autoconf g++ make && \
    pecl install redis && \
    docker-php-ext-enable redis && \
    apk del autoconf g++ make
