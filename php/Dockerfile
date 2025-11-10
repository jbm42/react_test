FROM node:20 AS assets
WORKDIR /app
ENV VITE_OUT_DIR=./dist
COPY ./node/package*.json ./node/
RUN cd node && npm ci
COPY ./node ./node
RUN cd node && npm run build

FROM php:8.2-apache
RUN docker-php-ext-install pdo pdo_mysql
WORKDIR /var/www/html
COPY ./php/public/ ./
COPY --from=assets /app/node/dist/inject.js ./build/inject.js
