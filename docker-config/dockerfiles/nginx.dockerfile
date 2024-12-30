FROM nginx:stable-alpine

ARG UID
ARG GID
ARG USER

ENV UID=${UID}
ENV GID=${GID}
ENV USER=${USER}

# MacOS staff group's gid is 20, so is the dialout group in alpine linux. We're not using it, let's just remove it.
RUN delgroup dialout

RUN addgroup -g ${GID} --system ${USER} || true
RUN adduser -G ${USER} --system -D -s /bin/sh -u ${UID} ${USER} || true
RUN sed -i "s/user  nginx/user ${USER}/g" /etc/nginx/nginx.conf

ADD nginx/default.conf /etc/nginx/conf.d/

RUN mkdir -p /var/www/html
