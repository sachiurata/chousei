FROM php:8.2-apache

# モジュールを有効化（任意）
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Apache の設定ファイルに「アクセス許可」追加
RUN echo "<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>" > /etc/apache2/conf-available/allow-access.conf \
    && a2enconf allow-access

# 権限を明示的に設定（念のため）
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html
