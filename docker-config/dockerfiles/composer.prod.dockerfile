FROM php:8.2-cli

ARG UID
ARG GID
ARG USER

ENV UID=${UID}
ENV GID=${GID}
ENV USER=${USER}

# Remove unused dialout group, mirror your host UID/GID
RUN delgroup dialout \
 && addgroup -g ${GID} --system ${USER} || true \
 && adduser -G ${USER} --system -D -s /bin/sh -u ${UID} ${USER} || true

# Install the libraries needed for GD and zip
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      libpng-dev \
      libjpeg62-turbo-dev \
      zlib1g-dev \
      libzip-dev \
      pkg-config \
 && docker-php-ext-configure gd --with-jpeg=/usr/include \
 && docker-php-ext-install gd zip \
 && rm -rf /var/lib/apt/lists/*

# Copy Composer v2 in
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
ENTRYPOINT ["composer"]
