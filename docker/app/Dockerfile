FROM php:8.3-cli-alpine

# Install system dependencies
RUN apk update && apk add --update --no-cache curl \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    icu-dev \
    libpq-dev \
    supervisor \
    oniguruma-dev \
    zip \
    npm \
    # Below are libraries for redis
    autoconf \
    g++ \
    oniguruma-dev \
    make && \
    # Install and enable redis
    pecl install redis && \
    rm -rf /tmp/pear && \
    docker-php-ext-enable redis && \
    # Install PHP extensions
    docker-php-ext-configure intl && \
    docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip intl && \
    # These libraries are only needed for installing imagick
    apk del autoconf g++ make oniguruma-dev && \
    # Remove cache after installing packages
    rm /var/cache/apk/*

COPY supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY cron /var/spool/cron/crontabs/root
COPY start.sh /start.sh

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN chmod +x /start.sh

WORKDIR /var/www

ENTRYPOINT /start.sh
