FROM php:7.4-apache
RUN apt-get update -y
RUN apt-get install git -y
RUN apt-get install zip -y
RUN apt-get install unzip -y
RUN apt-get install gnupg2 -y

# Gmp (To access Keyvault)
RUN apt-get install libgmp-dev -y
RUN docker-php-ext-install gmp

# MSSql (Debian 11)
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add -
RUN curl https://packages.microsoft.com/config/debian/11/prod.list > /etc/apt/sources.list.d/mssql-release.list

RUN apt-get update -y
RUN ACCEPT_EULA=Y apt-get install msodbcsql18 -y
RUN ACCEPT_EULA=Y apt-get install mssql-tools18 -y
RUN apt-get install unixodbc-dev -y
RUN echo 'export PATH="$PATH:/opt/mssql-tools18/bin"' >> ~/.bashrc && . ~/.bashrc
RUN pecl install sqlsrv
RUN pecl install pdo_sqlsrv
RUN docker-php-ext-enable sqlsrv pdo_sqlsrv

# PHP.ini file
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini
RUN sed -i -e "s/^ *upload_max_filesize.*/upload_max_filesize = 10M/g" /usr/local/etc/php/php.ini && \
    sed -i -e "s/^ *post_max_size.*/post_max_size = 100M/g" /usr/local/etc/php/php.ini && \
    sed -i -e "s/^ *max_file_uploads.*/max_file_uploads = 50/g" /usr/local/etc/php/php.ini

# Apache2
COPY source /var/www/html
WORKDIR /var/www/html

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer.phar
COPY composer.json /var/www/html
RUN composer.phar self-update
RUN composer.phar install

EXPOSE 443/tcp