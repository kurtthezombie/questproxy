FROM php:8.2-cli

#install dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libonig-dev \
    libfreetype6-dev libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring zip gd

# install composer from official composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

#set working directory
WORKDIR /var/www

#copy project files into the container
COPY . .

#install laravel dependencies 
RUN composer install --no-interaction --prefer-dist --optimize-autoloader