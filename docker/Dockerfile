ARG PHP_BUILD_VERSION
ARG PHP_TEST_VERSION

# Composer on correct PHP version
FROM php:${PHP_BUILD_VERSION}-cli as build

ARG BUILD_ROOT_PATH
ARG NODE_VERSION

# PHP
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN apt-get update
RUN apt-get install -y zip unzip curl git
RUN docker-php-ext-install pdo pdo_mysql

## Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

## Reds Extension
RUN pecl install -o -f redis \
&&  rm -rf /tmp/pear \
&&  docker-php-ext-enable redis

# Node
RUN apt install -y curl
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
ENV NVM_DIR=/root/.nvm
RUN . "$NVM_DIR/nvm.sh" && nvm install ${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm use v${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm alias default v${NODE_VERSION}
ENV PATH="/root/.nvm/versions/node/v${NODE_VERSION}/bin/:${PATH}"
RUN node --version
RUN npm --version
RUN npm install -g yarn

WORKDIR ${BUILD_ROOT_PATH}
COPY . ./


FROM php:${PHP_TEST_VERSION}-fpm as test

ARG BUILD_ROOT_PATH
ARG NODE_VERSION

# PHP
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN pecl install xdebug-3.2.1
RUN docker-php-ext-install pcntl posix pdo pdo_mysql

## Reds Extension
RUN pecl install -o -f redis \
&&  rm -rf /tmp/pear \
&&  docker-php-ext-enable redis

# Node
RUN apt install -y curl
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
ENV NVM_DIR=/root/.nvm
RUN . "$NVM_DIR/nvm.sh" && nvm install ${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm use v${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm alias default v${NODE_VERSION}
ENV PATH="/root/.nvm/versions/node/v${NODE_VERSION}/bin/:${PATH}"
RUN node --version
RUN npm --version
RUN npm install -g yarn

WORKDIR ${BUILD_ROOT_PATH}
COPY --from=build ${BUILD_ROOT_PATH} ${BUILD_ROOT_PATH}
CMD ["php-fpm"]


# Install PHP dev dependencies
FROM build as vendor-dev

ARG BUILD_ROOT_PATH

WORKDIR ${BUILD_ROOT_PATH}
RUN COMPOSER_DISCARD_CHANGES=true composer install
