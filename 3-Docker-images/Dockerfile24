FROM nginx 
RUN useradd ferney
COPY fruit /usr/share/nginx/html
WORKDIR /usr/share/nginx/html
ENV archivo docker
EXPOSE 80
LABEL version=1
USER ferney
RUN echo "Yo soy el usuario $(whoami) " > /tmp/yo.html
USER root
RUN cp /tmp/yo.html /usr/share/nginx/html/docker.html
VOLUME /var/logs/nginx
CMD nginx -g 'daemon off;'
