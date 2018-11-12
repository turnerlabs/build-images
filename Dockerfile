FROM docker:18.06.1-ce-dind

RUN set -ex \
  && echo "Install APK packages..." \
  && apk add --no-cache \
    alpine-sdk \
    autoconf \
    automake \
    bash \
    ca-certificates \
    curl \
    g++ \
    gcc \
    git \
    lcms2-dev \
    libpng \
    libpng-dev \
    nodejs \
    npm \
    openssl \
    php7 \
    php7-cli \
    php7-apache2 \
    php7-bcmath \
    php7-bz2 \
    php7-calendar \
    php7-cgi \
    php7-common \
    php7-ctype \
    php7-curl \
    php7-dba \
    php7-dev \
    php7-doc \
    php7-dom \
    php7-embed \
    php7-enchant \
    php7-exif \
    php7-fileinfo \
    php7-fpm \
    php7-ftp \
    php7-gd \
    php7-gettext \
    php7-gmp \
    php7-iconv \
    php7-imap \
    php7-intl \
    php7-json \
    php7-ldap \
    php7-litespeed \
    php7-mbstring \
    php7-mysqli \
    php7-mysqlnd \
    php7-odbc \
    php7-opcache \
    php7-openssl \
    php7-pcntl \
    php7-pdo \
    php7-pdo_dblib \
    php7-pdo_mysql \
    php7-pdo_odbc \
    php7-pdo_pgsql \
    php7-pdo_sqlite \
    php7-pear \
    php7-pgsql \
    php7-phar \
    php7-phpdbg \
    php7-posix \
    php7-pspell \
    php7-recode \
    php7-session \
    php7-shmop \
    php7-simplexml \
    php7-snmp \
    php7-soap \
    php7-sockets \
    php7-sodium \
    php7-sqlite3 \
    php7-sysvmsg \
    php7-sysvsem \
    php7-sysvshm \
    php7-tidy \
    php7-tokenizer \
    php7-wddx \
    php7-xml \
    php7-xmlreader \
    php7-xmlrpc \
    php7-xmlwriter \
    php7-xsl \
    php7-zip \
    pngquant \
    python \
    tar \
    zlib-dev \
  && echo "Update certs..." \
  && mkdir -p /etc/ssl/certs/ \
  && update-ca-certificates --fresh \
  && echo "Install Composer..." \
  && curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/bin/composer \
  && composer self-update \
  && rm -rf /root/.composer/cache \
  && echo "Install gulp and pngquant-bin..." \
  && npm install --global gulp \
  && npm install -g pngquant-bin --allow-root  --unsafe-perm=true \
  && echo "Install AWS CLI..." \
  && cd /tmp \
  && curl "https://s3.amazonaws.com/aws-cli/awscli-bundle.zip" -o "awscli-bundle.zip" \
  && unzip awscli-bundle.zip \
  && ./awscli-bundle/install -i /usr/local/aws -b /usr/local/bin/aws \
  && rm awscli-bundle.zip \
  && rm -rf awscli-bundle \
  && rm -rf /tmp/src

RUN chmod o+w /tmp

RUN addgroup docker

VOLUME /var/lib/docker

EXPOSE 2375

ENTRYPOINT []
CMD ["/usr/local/bin/dockerd-entrypoint.sh"]