FROM alpine:latest

RUN apk update \
    && apk upgrade \
    && apk add --no-cache \
    curl \
    git \
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
    && python --version