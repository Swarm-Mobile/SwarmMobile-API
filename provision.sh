#!/bin/bash

echo "Script Start";

echo "Getting root access";
sudo -s

echo "Updating the OS";
apt-get update;
debconf-set-selections <<< 'mysql-server-5.5 mysql-server/root_password password vagrant';
debconf-set-selections <<< 'mysql-server-5.5 mysql-server/root_password_again password vagrant';

apt-get -y install mysql-server mysql-client vim git redis-server;

echo "deb http://repos.zend.com/zend-server/7.0/deb_ssl1.0 server non-free" >> /etc/apt/sources.list
wget http://repos.zend.com/zend.key -O- | apt-key add -
aptitude update
aptitude -y install zend-server-php-5.5

echo "Creating Directory Links"
rm -rf /var/www;
ln -s /vagrant /var/www;

apt-get -y install mysql-server mysql-client vim git redis-server;

echo "Creating Virtual Host"
echo "<VirtualHost *:80>
         DocumentRoot /var/www/app/webroot
         SetEnv APPLICATION_ENV "development"
         php_value include_path ".:/var/www/:/usr/local/zend/share/pear"
         <Directory /var/www/app/webroot>
                 Options Indexes FollowSymLinks MultiViews
                 DirectoryIndex index.php
                 AllowOverride ALL
                 Order allow,deny
                 allow from all
         </Directory>

         ErrorLog ${APACHE_LOG_DIR}/error.log

         # Possible values include: debug, info, notice, warn, error, crit,
         # alert, emerg.
         LogLevel warn

         CustomLog ${APACHE_LOG_DIR}/access.log combined
      </VirtualHost>" > /etc/apache2/sites-enabled/000-default
service apache2 restart
ufw disable
ln -s /usr/local/zend/bin/php /usr/bin

echo "done!"