FROM nginx:1.29-alpine3.22 AS base

# copy nginx configuration (shared for all stages)
COPY docker/nginx/nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]

# production stage - with static assets baked in
FROM base AS production

# copy static assets from Laravel public directory
COPY laravel/public /var/www/html

# set proper permissions
RUN chown -R nginx:nginx /var/www/html \
    && chmod -R 755 /var/www/html

# development stage - expects volume mount (default)
FROM base AS development
# no static copy - static files will be mounted via volume