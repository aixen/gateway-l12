FROM php:8.2-fpm

# Install required system packages
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    mariadb-client \
    && docker-php-ext-install pdo pdo_mysql

# Set working directory
WORKDIR /var/www/gateway

CMD ["php-fpm"]
