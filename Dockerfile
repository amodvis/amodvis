# # build to image
FROM pristtlt/lnp-base:7.2-fpm-stretch  AS build
ARG GITHUB_TOKEN
RUN echo -e "machine github.com\n  login hk01bot\n  password ${GITHUB_TOKEN}" >> ~/.netrc
WORKDIR /var/web/www
COPY . .

RUN composer install --no-dev --no-progress

FROM pristtlt/lnp-base:7.2-fpm-stretch 
WORKDIR /var/web/www
COPY --from=build /var/web/www/. .
