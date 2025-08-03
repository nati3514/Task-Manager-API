# Use official PHP image with Apache
FROM php:8.2-apache

# Install SQLite3 and PDO SQLite extension
RUN apt-get update && apt-get install -y \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Create database directory with proper permissions
RUN mkdir -p /var/www/html/database && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Configure Apache to use index.php as the main entry point
RUN echo '<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
    DirectoryIndex index.php\n\
</Directory>' > /etc/apache2/conf-available/task-manager.conf && \
    a2enconf task-manager

# Create .htaccess for clean URLs
RUN echo 'RewriteEngine On\n\
RewriteCond %{REQUEST_FILENAME} !-f\n\
RewriteCond %{REQUEST_FILENAME} !-d\n\
RewriteRule ^(.*)$ index.php [QSA,L]' > /var/www/html/.htaccess

# Expose port 80
EXPOSE 80

# Initialize database on container start
RUN php /var/www/html/database/init.php || true

# Start Apache
CMD ["apache2-foreground"]
