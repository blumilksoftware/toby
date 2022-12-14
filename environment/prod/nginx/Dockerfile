ARG NGINX_VERSION=1.23-alpine
ARG NODE_VERSION=19-alpine3.16

### FRONTEND ###
FROM node:${NODE_VERSION} as frontend

WORKDIR /app_frontend_dependencies

COPY package.json package-lock.json postcss.config.js tailwind.config.js vite.config.js webpack.config.js webpack.mix.js ./

RUN npm clean-install

COPY resources/js ./resources/js/
COPY resources/css ./resources/css/

RUN npm run build

### WEB SERVER ###
FROM nginx:${NGINX_VERSION} as webserver

WORKDIR /application

COPY --from=frontend /app_frontend_dependencies/public/build ./public/build/
COPY /public/favicon.ico /public/robots.txt /public/index.php ./public/
COPY /public/img ./public/img/
