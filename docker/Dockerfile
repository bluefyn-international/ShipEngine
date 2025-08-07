FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y curl zip unzip libonig-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install mbstring

# Optionally install XDebug
ARG WITH_XDEBUG=false

RUN if $WITH_XDEBUG ; then \
    pecl install xdebug; \
    docker-php-ext-enable xdebug; \
    echo "zend_extension=xdebug" > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "[xdebug]" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.mode=develop,debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
fi ;

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -d /home/ubuntu ubuntu
RUN mkdir -p /home/ubuntu/.composer && \
    chown -R ubuntu:ubuntu /home/ubuntu

RUN mkdir -p /var/www/app && \
    chown -R ubuntu:ubuntu /var/www/app

# Set working directory
WORKDIR /var/www/app
