FROM alpine:latest

RUN apk update \
    && apk upgrade \
    && apk add --no-cache \
    curl \
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

RUN aws --version \
    && curl --version \
    && git --version \
    && jq -V \
    && echo "npm: $(npm --version)" \
    && echo "node: $(node --version)" \
    && echo python --version \
    && echo "zip $(zip -v)" \ 
    && zipcmp -V