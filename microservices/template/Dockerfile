FROM php:8.2.4-fpm
WORKDIR /template
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN apt update -y \
       && apt install git zip -y 
RUN docker-php-ext-install pdo pdo_mysql
