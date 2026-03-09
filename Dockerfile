FROM php:8.2-cli

# instalar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www