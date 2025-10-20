FROM php:8.1-apache

# Install curl extension for API calls and health checks
RUN apt-get update && apt-get install -y     libcurl4-openssl-dev     curl     && docker-php-ext-install curl     && apt-get clean     && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html
