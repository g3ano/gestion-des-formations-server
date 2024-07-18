FROM richarvey/nginx-php-fpm:3.1.6

# Install WebP development libraries
RUN apk --update add --no-cache libwebp-dev

COPY . .

# Image config
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

ENV APP_FALLBACK_LOCALE=en
ENV APP_FAKER_LOCALE=en_US

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

CMD ["/start.sh"]
