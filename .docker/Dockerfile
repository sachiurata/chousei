FROM php:8.2-apache

# mysqli 拡張をインストール
RUN docker-php-ext-install mysqli

# PHPファイルをApache公開ディレクトリにコピー
COPY ./php/ /var/www/html/
