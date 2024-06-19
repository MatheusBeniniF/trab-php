FROM php:8.0-apache

# Instala extensões necessárias para o PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilita o módulo reescrita do Apache
RUN a2enmod rewrite

# Copia o arquivo de configuração do Apache
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Define o diretório de trabalho
WORKDIR /var/www/html
