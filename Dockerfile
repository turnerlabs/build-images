FROM docker:latest

RUN apk update \
    && apk upgrade \
    && apk add --no-cache \
    curl \
    docker-compose \
    git \
    jq \
    nodejs \
    nodejs-npm \
    zip \
    libzip-tools \
    python \
    python-dev \
    py-pip \
    build-base \
    && pip install awscli --upgrade --user \
    && ln -s /root/.local/bin/aws /bin/aws \
    && apk --purge -v del py-pip \
    && rm -rf /var/cache/apk/*

RUN echo "alpine version: $(cat /etc/alpine-release)" \
    && aws --version \
    && curl --version \
    && docker -v \
    && docker-compose --version \
    && git --version \
    && jq -V \
    && echo "npm: $(npm --version)" \
    && echo "node: $(node --version)" \
    && echo python --version \
    && zip -v \
    && zipcmp -V