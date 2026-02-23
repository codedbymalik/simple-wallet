FROM php:8.2-apache

# Enable Apache mod_rewrite for URL routing
RUN a2enmod rewrite

# Install PDO extension
RUN docker-php-ext-install pdo pdo_mysql

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set Apache configuration to serve from public directory
RUN echo '<VirtualHost *:80>\n    DocumentRoot /var/www/html/public\n    <Directory /var/www/html/public>\n        AllowOverride All\n        Require all granted\n        RewriteEngine On\n        RewriteCond %{REQUEST_FILENAME} !-f\n        RewriteCond %{REQUEST_FILENAME} !-d\n        RewriteRule ^(.*)$ index.php?uri=$1 [QSA,L]\n    </Directory>\n</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

EXPOSE 80

CMD ["apache2-foreground"]
