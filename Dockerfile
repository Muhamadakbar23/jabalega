FROM node:20.17.0-alpine AS frontend

WORKDIR /frontend
COPY package*.json ./
RUN npm ci
COPY index.html vite.config.js ./
COPY src ./src
RUN npm run build

FROM php:8.2-cli

WORKDIR /app

# Install MySQL extension
RUN docker-php-ext-install mysqli

# Copy application
COPY . .
COPY --from=frontend /frontend/public ./public

# Expose port
EXPOSE 8080

# Start PHP built-in server
CMD ["php", "-S", "0.0.0.0:8080"]
