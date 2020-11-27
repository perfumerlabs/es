FROM ubuntu:xenial

LABEL authors="Ilyas Makashev mehmatovec@gmail.com, Nurbek Torbayev torbayevnurbek1992@gmail.com"

RUN set -x \
    && apt-get update && apt-get install -y --no-install-recommends ca-certificates wget locales && rm -rf /var/lib/apt/lists/* \
    && useradd -s /bin/bash -m es \
    && echo "deb http://nginx.org/packages/ubuntu/ xenial nginx" > /etc/apt/sources.list.d/nginx.list \
    && echo "deb-src http://nginx.org/packages/ubuntu/ xenial nginx" >> /etc/apt/sources.list.d/nginx.list \
    && echo "deb http://ppa.launchpad.net/ondrej/php/ubuntu xenial main" > /etc/apt/sources.list.d/php.list \
    && echo "deb-src http://ppa.launchpad.net/ondrej/php/ubuntu xenial main" >> /etc/apt/sources.list.d/php.list \
    && apt-key adv --keyserver keyserver.ubuntu.com --recv-keys ABF5BD827BD9BF62 \
    && apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 4F4EA0AAE5267A6C \
    && apt update \
    && apt install -y \
        nginx \
        php7.4 \
        php7.4-cli \
        php7.4-common \
        php7.4-curl \
        php7.4-fpm \
        php7.4-json \
        php7.4-opcache \
        supervisor \
        iputils-ping \
        vim \
        curl \
        git \
        zip \
        sudo \
        gnupg2 \
        lsb-release \
        apt-transport-https \
    && apt update \
    && apt install -y

COPY project /opt/es
COPY nginx /usr/share/container_config/nginx
COPY supervisor /usr/share/container_config/supervisor
COPY init.sh /usr/local/bin/init.sh
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

RUN set -x\
    && chown -R es:es /opt/es \
    && cd /opt/es \
    && sudo -u es php composer.phar install --no-dev --prefer-dist \
    && chmod +x /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/init.sh

ENV ES_HOST "elasticsearch"
ENV ES_PORT 9200

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]