FROM php:8.4-fpm-alpine3.22

WORKDIR /var/www/html

# install system dependencies including sqlite
RUN apk add --no-cache git zip unzip sqlite sqlite-dev

# install php extensions
RUN docker-php-ext-install pdo pdo_sqlite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# create app user and necessary directories
RUN addgroup -g 1000 app && adduser -D -G app -u 1000 app \
    && chown -R app:app /var/www/html

USER app

CMD ["php-fpm"]
