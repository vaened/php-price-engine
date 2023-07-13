FROM php:8.1-cli

WORKDIR /app

RUN apt-get update && apt-get install -y \
    libbz2-dev \
    libpng-dev \
    libssl-dev

RUN docker-php-ext-install bcmath

COPY . /app

