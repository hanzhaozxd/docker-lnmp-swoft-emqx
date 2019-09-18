FROM daocloud.io/library/php:7.3-fpm-alpine

COPY config/php.ini /usr/local/etc/php

# 更新安装源
RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.ustc.edu.cn/g' /etc/apk/repositories

#RUN apk add autoconf gcc \
#    && wget https://github.com/phpredis/phpredis/archive/3.1.3.tar.gz \
#    && tar -zxvf 3.1.3.tar.gz \
#    && cd phpredis-3.1.3 \
#    && /usr/local/bin/phpize \
#    && ./configure --with-php-config=/usr/local/bin/php-config \
#    && make \
#    && make install \

ENV PHPREDIS_VERSION 5.0.2
ENV SWOOLE_VERSION 4.4.2

# 安装redis和swoole扩展
RUN cd ~ \
    && adduser -s /bin/bash git -D -H \
    && wget https://onlyusb.oss-cn-beijing.aliyuncs.com/phpredis-5.0.2.tar.gz \
    && tar -zxvf phpredis-5.0.2.tar.gz \
    && mkdir -p /usr/src/php/ext \
    && mv phpredis-$PHPREDIS_VERSION /usr/src/php/ext/redis \
    && docker-php-ext-install redis \

    && apk add libstdc++ libpng-dev \
    && cd ~ \
    && wget https://onlyusb.oss-cn-beijing.aliyuncs.com/swoole-src-4.4.2.tar.gz \
    && tar -zxvf swoole-src-4.4.2.tar.gz \
    && mkdir -p /usr/src/php/ext \
    && mv swoole-src-$SWOOLE_VERSION /usr/src/php/ext/swoole \
    && docker-php-ext-install swoole \

    && docker-php-ext-install pdo_mysql mysqli pcntl gd\

    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer \

RUN php /usr/share/nginx/html/swoft.localhost/bin/swoft http:start -d