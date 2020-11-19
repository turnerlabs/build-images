# from https://github.com/turnerlabs/lnpf-base/blob/alpine/Dockerfile
FROM turnerlabs/lnpf-base:0.2.0-php7.2.16-fpm-alpine3.9

# Build identification data
ARG BUILD_ID=0
ARG GIT_REVISION=0
ARG APP_VERSION=0

WORKDIR /

# Copy Drupal Dependencies
COPY composer /

# Copy NodeJS dependencies
COPY package.json /package.json
COPY package-lock.json /package-lock.json

# Add utilities
RUN apk add --no-cache \
  git \
	jpegoptim \
  jq \
  libwebp \
  nodejs \
  nodejs-npm \
	optipng \
  patch \
  redis \
  tzdata \
  vim \
  wget

  # Setup logging for php fpm
  RUN  mkdir /var/log/php7 \
  && chown -R www-data:www-data /var/log/php7 \

  && echo "extension=redis.so" >> /usr/local/etc/php/conf.d/redis.ini \
# Install PHP libraries
  && set -ex \
    && apk add --no-cache --virtual .php-build-deps \
      autoconf \
      g++ \
      make \
      musl \
      musl-dev \
      musl-utils \
    && pecl install apcu \
    && pecl uploadprogress \
    && apk del .php-build-deps

# Install New Relic
RUN export NEWRELIC_VERSION=$(curl -sS https://download.newrelic.com/php_agent/release/ | sed -n 's/.*>\(.*linux-musl\).tar.gz<.*/\1/p') \
  && curl -L -o newrelic.tar.gz https://download.newrelic.com/php_agent/release/$NEWRELIC_VERSION.tar.gz \
  && gzip -dc newrelic.tar.gz | tar xf - \
  && cd $NEWRELIC_VERSION \
  && NR_INSTALL_SILENT=true NR_INSTALL_USE_CP_NOT_LN=true ./newrelic-install install \
  && rm -rf $NEWRELIC_VERSION 

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=1.8.0 \
 # && composer self-update \
  && composer global require hirak/prestissimo \ 
# had to remove - https://github.com/drupal-composer/drupal-scaffold/issues/101
  && php -d memory_limit=-1  /usr/local/bin/composer -vvv update -o \
#  && (cd /build/html/modules/contrib && patch -N -p1) \
#     < /patches/imageoptimize_update.patch \
#  && (cd /build/html && patch -N -p1) \
#     < /patches/2974911_3.patch \
#  && rm -rf /patches \
  && ln -s /vendor/bin/drupal /usr/local/bin/drupal \
  && ln -s /vendor/bin/drush /usr/local/bin/drush \
  && mkdir -p /cache/fastcgi_cache \
  # Install NodeJS global dependencies
  && npm install -g \
    grunt-cli@1.2.0 \
    gulp-cli@1.2.2 \
    coffeescript@1.10.0 \
    marked@0.3.6 \
    node-gyp@3.6.0 \
  && npm install \
  && npm cache clean --force \
  && rm -rf /var/cache/apk/* \
  && rm -rf /tmp/* \
  && rm -rf /root/.cache \
  && rm -rf /root/.composer/cache \
# Change ownership of nginx work directories from default nginx user to www-data
  && chown -R www-data:www-data /var/tmp/nginx /var/lib/nginx \
# Write build info
  && mkdir /DEPENDENCIES_BUILD \
  && echo -n "$BUILD_ID" > /DEPENDENCIES_BUILD/BUILD_ID \
  && echo -n "$GIT_REVISION" >  /DEPENDENCIES_BUILD/GIT_REVISION \
  && echo -n "$APP_VERSION" >  /DEPENDENCIES_BUILD/APP_VERSION

EXPOSE 8080
