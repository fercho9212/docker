#!/bin/bash

/usr/sbin/php-fpm -c /etc/php/fpm

nginx -g 'daemon off;'

sudo systemctl restart nginx
