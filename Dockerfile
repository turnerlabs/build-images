FROM docker:18.06.1-ce-dind

RUN chmod o+w /tmp

RUN addgroup docker

VOLUME /var/lib/docker

EXPOSE 2375

ENTRYPOINT []
CMD ["/usr/local/bin/dockerd-entrypoint.sh"]