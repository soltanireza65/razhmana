FROM php:8.1-fpm

# update package list
RUN apt-get update

# install curl and git
RUN apt-get install -y curl git unzip

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# install pdo
RUN docker-php-ext-install pdo pdo_mysql

COPY ./docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

COPY ./ /app/
