# Use the official PHP Apache image
FROM php:8.2-apache

# Copy all project files into the web directory
COPY . /var/www/html/

# Give proper permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
