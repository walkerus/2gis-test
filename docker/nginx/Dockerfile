FROM nginx

ADD ./nginx.conf /etc/nginx/nginx.conf
ADD ./settings.conf /etc/nginx/conf.d/settings.site
RUN ["chmod", "+x", "/etc/nginx/nginx.conf"]