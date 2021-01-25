FROM php:7.4.0-cli-alpine
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
COPY . /usr/babelfisch
WORKDIR /usr/babelfisch
RUN composer update
CMD [ "php", "./vendor/bin/phpunit", "--colors=always", "--testsuite", "Default"]

