# Dockerfile for CampusPulse College Event Management System
# Production-ready PHP 8.2 Apache Image

FROM php:8.2-apache

# Install SQLite and MySQL PDO extensions
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite gd \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite module
RUN a2enmod rewrite headers

# Copy application source code
COPY . /var/www/html/

# Set directory permissions for SQLite database and custom image uploads
RUN mkdir -p /var/www/html/assets/images/uploads/events \
    && mkdir -p /var/www/html/database \
    && chown -R www-data:www-data /var/www/html/assets/images/uploads \
    && chown -R www-data:www-data /var/www/html/database \
    && chmod -R 775 /var/www/html/assets/images/uploads \
    && chmod -R 775 /var/www/html/database

# Expose standard HTTP port
EXPOSE 80

# Start Apache in foreground (adapting to $PORT if provided by Render)
CMD sed -i "s/80/${PORT:-80}/g" /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf && apache2-foreground

