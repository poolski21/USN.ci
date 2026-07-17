# --- Étape 1 : build des assets front-end (Vite) ---
FROM node:20 AS node-build
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# --- Étape 2 : image PHP finale ---
FROM php:8.3-apache

# Dépendances système + extensions PHP nécessaires à Laravel
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli zip gd mbstring

# Installe Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configure Apache pour pointer vers public/
RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

WORKDIR /var/www/html

# Copie le code PHP
COPY . .

# Copie les assets déjà compilés depuis l'étape Node
COPY --from=node-build /app/public/build ./public/build

# Installe les dépendances PHP (sans les paquets de dev)
RUN composer install --optimize-autoloader --no-dev

# Permissions pour Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

CMD ["apache2-foreground"]