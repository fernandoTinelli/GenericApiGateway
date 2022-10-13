FROM php:8.1-apache

ENV APP_HOME /var/www/projeto

RUN a2dissite 000-default.conf
RUN rm -r $APP_HOME

COPY . /var/www/projeto
COPY ./.docker/symfony.conf /etc/apache2/sites-available/symfony.conf
RUN a2ensite symfony.conf

WORKDIR /var/www/projeto