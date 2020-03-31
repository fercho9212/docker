FROM centos:7

RUN \
	yum -y install httpd php  php-cli php-common
#RUN echo "<?php phpinfo(); ?>" > /var/www/html/hola.php
COPY startbootstrap-sb-admin-2 /var/www/html
CMD apachectl -DFOREGROUND


