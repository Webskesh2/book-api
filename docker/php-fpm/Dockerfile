FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    curl \
    libzip-dev \
    libpng-dev \
    jpegoptim \
    optipng \
    git \
    supervisor \
    && docker-php-ext-install pdo_mysql zip pcntl gd

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

ARG UID=1000
RUN adduser -u $UID -D -g '' appuser
RUN chown -R appuser:appuser /var/www/html

USER appuser

EXPOSE 9000

CMD ["php-fpm"]
