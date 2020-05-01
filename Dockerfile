FROM alpine:latest

RUN apk update \
    && apk upgrade \
    && apk add --no-cache \
    aws-cli \
    git \
    libzip-tools \
    && rm -rf /var/cache/apk/*