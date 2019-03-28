FROM docker:18.06.1-ce-dind

RUN set -ex \
    && echo "Install APK packages..." \
    && apk add --no-cache \
    gnupg

ENV NODE_VERSION="10.14.1"

#RUN for key in \
    #94AE36675C464D64BAFA68DD7434390BDBE9B9C5 \
    #B9AE9905FFD7803F25714661B63B535A4C206CA9 \
    #77984A986EBC2AA786BC0F66B01FBB92821C587A \
    #71DCFD284A79C3B38668286BC97EC7A07EDE3FC1 \
    #FD3A5288F042B6850C66B31F09FE44734EB7990E \
    #8FCCA13FEF1D0C2E91008E09770F7A9A5AE15600 \
    #C4F0DFFF4E8C1A8236409D08E73BC641CC11F4C8 \
    #DD8F2338BAE7501E3DD5AC78C273792F7D83545D \
    #; do \
        #gpg --keyserver hkp://p80.pool.sks-keyservers.net:80 --recv-keys "$key" || \
        #gpg --keyserver hkp://ipv4.pool.sks-keyservers.net --recv-keys "$key" || \
        #gpg --keyserver pool.sks-keyservers.net --recv-keys "$key"; \
        #gpg --keyserver hkp://pgp.mit.edu:80 --recv-keys "$key" ; \
    #done

RUN gpg --keyserver pool.sks-keyservers.net --recv-keys 4ED778F539E3634C779C87C6D7062848A1AB005C
RUN gpg --keyserver pool.sks-keyservers.net --recv-keys B9E2F5981AA6E0CD28160D9FF13993A75599653C
RUN gpg --keyserver pool.sks-keyservers.net --recv-keys 94AE36675C464D64BAFA68DD7434390BDBE9B9C5
RUN gpg --keyserver pool.sks-keyservers.net --recv-keys B9AE9905FFD7803F25714661B63B535A4C206CA9
RUN gpg --keyserver pool.sks-keyservers.net --recv-keys 77984A986EBC2AA786BC0F66B01FBB92821C587A
RUN gpg --keyserver pool.sks-keyservers.net --recv-keys 71DCFD284A79C3B38668286BC97EC7A07EDE3FC1
RUN gpg --keyserver pool.sks-keyservers.net --recv-keys FD3A5288F042B6850C66B31F09FE44734EB7990E
RUN gpg --keyserver pool.sks-keyservers.net --recv-keys 8FCCA13FEF1D0C2E91008E09770F7A9A5AE15600
RUN gpg --keyserver pool.sks-keyservers.net --recv-keys C4F0DFFF4E8C1A8236409D08E73BC641CC11F4C8
RUN gpg --keyserver pool.sks-keyservers.net --recv-keys DD8F2338BAE7501E3DD5AC78C273792F7D83545D
RUN gpg --keyserver pool.sks-keyservers.net --recv-keys A48C2BEE680E841632CD4E44F07496B3EB3C1762

RUN wget "https://nodejs.org/download/release/v$NODE_VERSION/node-v$NODE_VERSION-linux-x64.tar.gz" \
    && wget https://nodejs.org/dist/v$NODE_VERSION/SHASUMS256.txt \
    && grep node-v$NODE_VERSION-linux-x64.tar.gz SHASUMS256.txt | sha256sum -c - \
    && wget https://nodejs.org/dist/v$NODE_VERSION/SHASUMS256.txt.sig \
    && gpg --verify SHASUMS256.txt.sig SHASUMS256.txt \
    && tar -xzf "node-v$NODE_VERSION-linux-x64.tar.gz" -C /usr/local --strip-components=1 \
    && rm "node-v$NODE_VERSION-linux-x64.tar.gz" SHASUMS256.txt.sig SHASUMS256.txt \
    && ln -s /usr/local/bin/node /usr/local/bin/nodejs \
    && rm -fr /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN npm install -g serverless

RUN chmod o+w /tmp

RUN addgroup docker

VOLUME /var/lib/docker

EXPOSE 2375

ENTRYPOINT []
CMD ["/usr/local/bin/dockerd-entrypoint.sh"]