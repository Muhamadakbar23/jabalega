FROM php:8.2-cli

WORKDIR /app

# Install MySQL extension
RUN docker-php-ext-install mysqli

# Copy application
COPY . .

# Expose port
EXPOSE 8080

# Start PHP built-in server
CMD ["php", "-S", "0.0.0.0:8080"]
