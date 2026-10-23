FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_sql

RUN a2enmod rewrite

COPY . /var/www/html/

WORKDIR /var/www/html/

ENV PORT=10000
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

EXPOSE 10000

CMD ["apache2-foreground"]