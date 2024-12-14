FROM node:23-alpine as build

WORKDIR /app
COPY . .
RUN yarn && yarn build

FROM nginx:alpine
COPY --from=build /app/dist /usr/share/nginx/html
COPY docker/frontend/default.conf /etc/nginx/conf.d/default.conf
