FROM php:8.1-apache

COPY . /var/www/projeto

WORKDIR /var/www/projeto

# FROM php:8.1-apache

# # set main params
# ARG BUILD_ARGUMENT_ENV=dev
# ENV ENV=$BUILD_ARGUMENT_ENV
ENV APP_HOME /var/www/projeto
# ARG HOST_UID=1000
# ARG HOST_GID=1000
# ENV USERNAME=www-data
# ARG INSIDE_DOCKER_CONTAINER=1
# ENV INSIDE_DOCKER_CONTAINER=$INSIDE_DOCKER_CONTAINER

# # check environment
# RUN if [ "$BUILD_ARGUMENT_ENV" = "default" ]; then echo "Set BUILD_ARGUMENT_ENV in docker build-args like --build-arg BUILD_ARGUMENT_ENV=dev" && exit 2; \
#     elif [ "$BUILD_ARGUMENT_ENV" = "dev" ]; then echo "Building development environment."; \
#     elif [ "$BUILD_ARGUMENT_ENV" = "test" ]; then echo "Building test environment."; \
#     elif [ "$BUILD_ARGUMENT_ENV" = "staging" ]; then echo "Building staging environment."; \
#     elif [ "$BUILD_ARGUMENT_ENV" = "prod" ]; then echo "Building production environment."; \
#     else echo "Set correct BUILD_ARGUMENT_ENV in docker build-args like --build-arg BUILD_ARGUMENT_ENV=dev. Available choices are dev,test,staging,prod." && exit 2; \
#     fi

# # install all the dependencies and enable PHP modules
# RUN apt-get update && apt-get upgrade -y && apt-get install -y \
#       procps \
#       nano \
#       git \
#       unzip \
#       libicu-dev \
#       zlib1g-dev \
#       libxml2 \
#       libxml2-dev \
#       libreadline-dev \
#       cron \
#       sudo \
#       libzip-dev \
#       wget \
#       librabbitmq-dev \
#     && docker-php-ext-configure intl \
#     && docker-php-ext-install \
#       sockets \
#       intl \
#       opcache \
#       zip \
#     && rm -rf /tmp/* \
#     && rm -rf /var/list/apt/* \
#     && rm -rf /var/lib/apt/lists/* \
#     && apt-get clean

# # disable default site and delete all default files inside APP_HOME
RUN a2dissite 000-default.conf
RUN rm -r $APP_HOME
COPY ./.docker/symfony.conf /etc/apache2/sites-available/symfony.conf
RUN a2ensite symfony.conf


# # create document root, fix permissions for www-data user and change owner to www-data
# # RUN mkdir -p $APP_HOME/public && \
# #     mkdir -p /home/$USERNAME && chown $USERNAME:$USERNAME /home/$USERNAME \
# #     && usermod -o -u $HOST_UID $USERNAME -d /home/$USERNAME \
# #     && groupmod -o -g $HOST_GID $USERNAME \
# #     && chown -R ${USERNAME}:${USERNAME} $APP_HOME

# # put apache and php config for Symfony, enable sites
# COPY ./.docker/symfony.conf /etc/apache2/sites-available/symfony.conf
# RUN a2ensite symfony.conf
# COPY ./.docker/php.ini /usr/local/etc/php/php.ini

# # enable apache modules
# RUN a2enmod rewrite
# RUN a2enmod ssl

# # install composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# RUN chmod +x /usr/bin/composer
# ENV COMPOSER_ALLOW_SUPERUSER 1

# # set working directory
# WORKDIR $APP_HOME

# USER ${USERNAME}

# # copy source files
# COPY --chown=${USERNAME}:${USERNAME} . $APP_HOME/

# # install all PHP dependencies
# RUN if [ "$BUILD_ARGUMENT_ENV" = "dev" ] || [ "$BUILD_ARGUMENT_ENV" = "test" ]; then COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader --no-interaction --no-progress; \
#     else export APP_ENV=$BUILD_ARGUMENT_ENV && COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader --no-interaction --no-progress --no-dev; \
#     fi
    
# USER root