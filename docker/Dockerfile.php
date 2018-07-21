FROM php:7.1-fpm

ARG ANSIBLECMDB_BACKEND_VERSION
ARG APP_HOME=/opt/app_seraph
ARG DOCKER_DIR=docker
ARG APP_USER_UID=1000
ARG ANSIBLE_VER
ENV APP_HOME=${APP_HOME}

RUN apt-get update -q -q && \
 apt-get install --yes --force-yes runit && \
 apt-get install -y unzip git zlib1g-dev python python-pip openssh-client && \
 pip install virtualenv

COPY ${DOCKER_DIR}/runsvdir-start /usr/local/sbin/runsvdir-start

RUN apt-get update -q -q && \
    apt-get install cron --yes --force-yes && \
    docker-php-ext-install mbstring zip

RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

RUN mkdir -p $APP_HOME/${ANSIBLECMDB_BACKEND_VERSION}

RUN ln -s $APP_HOME/${ANSIBLECMDB_BACKEND_VERSION} $APP_HOME/current && \
    mkdir $APP_HOME/current/.ssh && \
    virtualenv -p python2.7 --no-site-packages $APP_HOME/venv && \
    . $APP_HOME/venv/bin/activate && \
    pip install ansible==$ANSIBLE_VER

RUN groupadd -g $APP_USER_UID app_user && \
    useradd -u $APP_USER_UID -g $APP_USER_UID \
    -s /bin/bash -md $APP_HOME/current app_user

COPY ${DOCKER_DIR}/inventory $APP_HOME/inventory

RUN chown -R app_user:app_user $APP_HOME

RUN apt-get purge -y git unzip curl zlib1g-dev && \
    apt-get clean		

COPY ${DOCKER_DIR}/cron.php /etc/cron.d/php

RUN >/etc/crontab && mkdir -p /etc/service/cron && \
    crontab -u app_user /etc/cron.d/php

COPY ${DOCKER_DIR}/cron.runit /etc/service/cron/run
 
RUN chown -R app_user:app_user /etc/service && \
    chmod u+x /etc/service/cron/run

USER app_user

CMD ["/usr/local/sbin/runsvdir-start"]
