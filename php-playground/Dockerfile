FROM php:8.1-apache

# Install required extensions and dependencies
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    libsqlite3-dev \
    curl \
    && docker-php-ext-install curl pdo pdo_sqlite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Create database directory and set permissions
RUN mkdir -p /var/lib/playground && \
    chown -R www-data:www-data /var/lib/playground && \
    chmod 755 /var/lib/playground

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html
