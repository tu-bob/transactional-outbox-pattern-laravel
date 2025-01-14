FROM php:8.4-apache

WORKDIR /var/www

COPY ./src /var/www

COPY ./infrastructure/docker/php/000-default.conf /etc/apache2/sites-available/000-default.conf

RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

RUN a2enmod rewrite

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

ARG NODE_MAJOR=18

# Install system dependencies
RUN apt-get update && apt-get install -y --fix-missing \
 git \
 curl \
 libpng-dev \
 libonig-dev \
 libxml2-dev \
 zlib1g-dev \
 libzip-dev \
 zip \
 unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install zip pdo_mysql mbstring exif pcntl bcmath gd

RUN apt-get update && apt-get install -y ca-certificates curl gnupg \
    && mkdir -p /etc/apt/keyrings \
    && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_MAJOR.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list \
    && apt-get update \
    && apt-get install -y nodejs \
    && npm install -g npm

RUN ln -sf /bin/bash /bin/sh

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

##Install composer dependencies
RUN composer install
#
##Install npm dependencies
#RUN npm install
#
## Build assets
#RUN npm run build
