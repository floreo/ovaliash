FROM php:8.3-apache
RUN a2enmod rewrite
WORKDIR /var/www/html
COPY --chown=www-data src/ /var/www/html/
EXPOSE 80
