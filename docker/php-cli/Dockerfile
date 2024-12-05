FROM php:8.2-cli

# Downloading the PHP Extensions installation script
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Install all the required APT packages and configure PHP Extensions
RUN chmod +x /usr/local/bin/install-php-extensions && sync \
    && install-php-extensions \
        mbstring \
        json \
        ctype \
        xdebug \
        @composer-2.0.2

WORKDIR /www
