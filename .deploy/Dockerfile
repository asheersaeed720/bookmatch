ARG PHP_VERSION=${PHP_VERSION:-8.5}

FROM rajabilal555/php-base:${PHP_VERSION} AS app-setup

ARG NON_ROOT_GROUP=${NON_ROOT_GROUP:-app}
ARG NON_ROOT_USER=${NON_ROOT_USER:-app}

# Set working directory
ENV LARAVEL_PATH=/srv/app
WORKDIR $LARAVEL_PATH

# Set cron job
COPY ./.deploy/config/crontab /etc/crontabs/$NON_ROOT_USER
RUN chmod 777 /usr/sbin/crond
RUN chown -R $NON_ROOT_USER:$NON_ROOT_GROUP /etc/crontabs/$NON_ROOT_USER && setcap cap_setgid=ep /usr/sbin/crond

# Switch to non-root 'app' user & install app dependencies
COPY composer.json composer.lock ./
RUN chown -R $NON_ROOT_USER:$NON_ROOT_GROUP $LARAVEL_PATH
COPY package.json package-lock.json ./
RUN chown -R $NON_ROOT_USER:$NON_ROOT_GROUP $LARAVEL_PATH
USER $NON_ROOT_USER
RUN composer install --prefer-dist --no-scripts --no-dev --no-autoloader --no-interaction
RUN rm -rf /home/$NON_ROOT_USER/.composer

RUN npm install

# Copy app
COPY --chown=$NON_ROOT_USER:$NON_ROOT_GROUP . $LARAVEL_PATH/
COPY ./.deploy/config/php/local.ini /usr/local/etc/php/conf.d/local.ini

RUN npm run build

# Set any ENVs
ARG CAPROVER_GIT_COMMIT_SHA=${CAPROVER_GIT_COMMIT_SHA}

# Start app
EXPOSE 80
COPY ./.deploy/entrypoint.sh /

ENTRYPOINT ["sh", "/entrypoint.sh"]
