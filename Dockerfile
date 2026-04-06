FROM php:8.2-cli-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
        curl \
        git \
        unzip \
    && docker-php-ext-install \
        curl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy dependency files first for better layer caching
COPY composer.json composer.lock* ./

# Install PHP dependencies
RUN composer install \
        --no-dev \
        --optimize-autoloader \
        --no-interaction \
        --prefer-dist

# Copy application source
COPY . .

# Run as non-root user
RUN addgroup -S botuser && adduser -S botuser -G botuser
USER botuser

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=10s --retries=3 \
    CMD php -r "echo 'OK';" || exit 1

CMD ["php", "bot.php"]
