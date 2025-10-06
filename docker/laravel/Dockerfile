FROM php:8.4-fpm-alpine3.22 AS base

WORKDIR /var/www/html

# install system dependencies including sqlite
RUN apk add --no-cache git zip unzip sqlite sqlite-dev

# install php extensions
RUN docker-php-ext-install pdo pdo_sqlite

# create app user and necessary directories
RUN addgroup -g 1000 app && adduser -D -G app -u 1000 app \
    && chown -R app:app /var/www/html

USER app

# create empty .env
RUN touch .env

CMD ["php-fpm"]

# builder stage - install production dependencies
FROM base AS builder

COPY --from=composer:2.8.12 /usr/bin/composer /usr/bin/composer

# copy only composer files for caching
COPY --chown=app:app composer.json composer.lock ./

# install production dependencies (cached if composer files unchanged)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# copy application code (needed for autoload classmap)
COPY --chown=app:app . /var/www/html/

# generate optimized autoloader for production
RUN composer dump-autoload --no-dev --optimize --no-scripts --classmap-authoritative

# test stage - with dev dependencies
FROM base AS test

COPY --from=builder /usr/bin/composer /usr/bin/composer

# copy composer files
COPY --chown=app:app composer.json composer.lock ./

# install all dependencies including dev without scripts (cached if composer files unchanged)
RUN composer install --no-scripts --no-autoloader --no-interaction --prefer-dist

# copy application code
COPY --chown=app:app . /var/www/html/

# generate optimized autoloader
RUN composer dump-autoload --optimize --no-scripts

CMD ["php-fpm"]

# production stage - minimal, without composer
FROM base AS production

# copy application code
COPY --chown=app:app . /var/www/html/

# copy vendor from builder (after code to avoid overwriting)
COPY --from=builder --chown=app:app /var/www/html/vendor ./vendor

CMD ["php-fpm"]

# development stage - no code copied, expects volume mount (default)
FROM base AS development

COPY --from=builder /usr/bin/composer /usr/bin/composer

# no code copy - code comes from volume
