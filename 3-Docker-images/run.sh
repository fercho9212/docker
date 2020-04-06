#!/bin/bash
echo "Iniciando Container...."
echo "INICIANDO ! " > /var/www/html/ini.html
apachectl -DFOREGROUND
