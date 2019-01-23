FROM php:7.2-fpm

RUN apt-get update && apt-get install --no-install-recommends -y \
    librabbitmq-dev \
    zlib1g-dev \
	libssl-dev \
	openssl \
	libmcrypt-dev \
	libpng-dev \
	libgd-dev \
	git


RUN pecl install apcu \
    && pecl install apcu_bc-1.0.3 \
    && docker-php-ext-enable apcu --ini-name 10-docker-php-ext-apcu.ini \
    && docker-php-ext-enable apc --ini-name 20-docker-php-ext-apc.ini

RUN pecl install redis && docker-php-ext-enable redis
RUN pecl install amqp && docker-php-ext-enable amqp

RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/

RUN docker-php-ext-install \
       pdo_mysql \
       zip \
       iconv \
       gd \
       opcache

RUN pecl install xdebug-2.6.0  && docker-php-ext-enable xdebug \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_connect_back=off" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_host=192.168.101.1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
