FROM composer:2.8 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer validate --strict \
    && composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    --ignore-platform-req=ext-intl


FROM dunglas/frankenphp:php8.5-alpine

ARG APP_REVISION=dev

ENV APP_REVISION=${APP_REVISION}

ARG APP_BUILD_DATE=unknown

ENV APP_BUILD_DATE=${APP_BUILD_DATE}

RUN apk add --no-cache curl \
    && install-php-extensions \
    pdo_mysql \
    opcache \
    mysqli \
    intl \
    mbstring \
    gd \
    zip

COPY Caddyfile /etc/frankenphp/Caddyfile

COPY . /app

COPY --from=vendor /app/vendor ./vendor

RUN chown -R www-data:www-data /app/writable /app/public
