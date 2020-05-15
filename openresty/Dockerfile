FROM openresty/openresty:alpine-fat

RUN apk add --no-cache --virtual .run-deps \
    bash \
    curl \
    diffutils \
    grep \
    sed \
    openssl \
    && mkdir -p /etc/resty-auto-ssl \
    && addgroup -S nginx \
    && adduser -D -S -h /var/cache/nginx -s /sbin/nologin -G nginx nginx \
    && chown nginx /etc/resty-auto-ssl

RUN apk add --no-cache --virtual .build-deps \
        gcc \
        libc-dev \
        make \
        openssl-dev \
        pcre-dev \
        zlib-dev \
        linux-headers \
        gnupg \
        libxslt-dev \
        gd-dev \
        geoip-dev \
        perl-dev \
        tar \
        unzip \
        zip \
        unzip \
        g++ \
        cmake \
        lua \
        lua-dev \
        make \
        autoconf \
        automake \
    && /usr/local/openresty/luajit/bin/luarocks install lua-resty-auto-ssl \
    && apk del .build-deps \
    && mkdir -p /var/cache/nginx
    # && rm -rf /usr/local/openresty/nginx/conf/* \
    
RUN openssl req -new -newkey rsa:2048 -days 3650 -nodes -x509 \
  -subj '/CN=app.oolora.com' \
  -keyout /etc/ssl/app.oolora.com.key \
  -out /etc/ssl/app.oolora.com.crt

# use self signed ssl certifacte to start nginx
COPY ./ssl /etc/resty-auto-ssl
COPY ./nginx/conf/nginx.conf /usr/local/openresty/nginx/conf/nginx.conf

# CMD ["openresty -s reload"]