FROM alpine:latest

RUN apk update \
    && apk upgrade \
    && apk add --no-cache \
    curl \
    git \
    libzip-tools \
    && curl "https://s3.amazonaws.com/aws-cli/awscli-bundle.zip" -o "/tmp/awscli-bundle.zip" \
    && cd "/tmp" \
    && unzip "awscli-bundle.zip" \
    && ./awscli-bundle/install -i "/usr/local/aws" -b "/usr/local/bin/aws" \
    && rm -rf "/tmp/awscli-bundle.zip" \
    && rm -rf "/tmp/awscli-bundle" \
    && rm -rf "/var/cache/apk/*"