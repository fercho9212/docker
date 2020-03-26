FROM centos:latest

LABEL version=1.0
LABEL descripcion="This is a an apache image"
LABEL vendor=yo

RUN yum -y install httpd -y 

COPY startbootstrap-freelancer-master /var/www/html
RUN echo "$(whoami)" > /var/www/html/usuario.html
RUN useradd ferney
RUN chown ferney:ferney -R  /var/www/html
USER ferney
RUN echo "$(whoami)" > /tmp/usuario1.html
USER root
RUN cp /tmp/usuario1.html /var/www/html/usuario1.html
CMD apachectl -DFOREGROUND
