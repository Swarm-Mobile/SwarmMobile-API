#!/bin/bash

echo "Script Start";

echo "Getting root access";
sudo -s

echo "Updating the OS";
apt-get update;
debconf-set-selections <<< 'mysql-server-5.5 mysql-server/root_password password vagrant';
debconf-set-selections <<< 'mysql-server-5.5 mysql-server/root_password_again password vagrant';

echo "Installing needed packages"
apt-get -y install make mysql-server vim git redis-server php5-fpm mysql-client nginx htop php5-cli php5-mysql php5-curl php5-gd php-pear php5-mcrypt php5-dev

echo "Making /app"
mkdir /app
mkdir /app/logs
chmod -R 777 /app/logs
cd /app

echo "Create the virtual host" 
ln -s /vagrant /app/API;
echo "
worker_processes  1;
events {
    worker_connections  1024;
}
http {
    include             mime.types;
    default_type        application/octet-stream;
    sendfile            on;
    keepalive_timeout   65;
    include /etc/nginx/conf.d/*.conf;
    include /etc/nginx/sites-enabled/*;
}
" > /etc/nginx/nginx.conf

rm /etc/nginx/sites-enabled/default
rm /etc/nginx/sites-available/default

echo "
server {
        listen   80;        
        root   /app/API/app/webroot/;
        index  index.php index.html;
        access_log /app/logs/api.access.log;
        error_log /app/logs/api.error.log;
        location / {
            try_files \$uri \$uri/ /index.php?\$args;
        }
        location ~ \.php\$ {
            try_files \$uri =404;
            include /etc/nginx/fastcgi_params;
            fastcgi_pass    127.0.0.1:9000;
            fastcgi_read_timeout 300;
            fastcgi_index   index.php;
            fastcgi_param server_location local;
            fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        }
        location ~ /\.ht {
            deny  all;
        }
}
" > /etc/nginx/sites-available/api

echo "
[www]
user = www-data
group = www-data
listen =  127.0.0.1:9000;
pm = dynamic
pm.max_children = 5
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3
" > /etc/php5/fpm/pool.d/www.conf

ln -s /etc/nginx/sites-available/api /etc/nginx/sites-enabled/api

echo "Disable firewall"
ufw disable

echo "Restarting services"
service php5-fpm restart
service nginx restart

echo "Installing composer"
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

cd /app/API
composer install

echo "done!"
