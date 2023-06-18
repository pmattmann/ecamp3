FROM mcr.microsoft.com/devcontainers/php:0-8.2

RUN apt-get clean \
    && apt-get update \
    && apt-get install --yes libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl
