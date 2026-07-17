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
# Ajout de libpq-dev + pdo_pgsql pour PostgreSQL (Render)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mysqli zip gd mbstring

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

# Render fournit la variable PORT (10000 par défaut) — Apache doit écouter dessus.
# Ce script remplace dynamiquement le port au démarrage du conteneur.
RUN echo '#!/bin/bash\n\
PORT="${PORT:-10000}"\n\
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf\n\
sed -i "s/:80>/:${PORT}>/" /etc/apache2/sites-available/000-default.conf\n\
exec apache2-foreground' > /usr/local/bin/start-apache.sh \
    && chmod +x /usr/local/bin/start-apache.sh

EXPOSE 10000

CMD ["/usr/local/bin/start-apache.sh"]