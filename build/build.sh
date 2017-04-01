#!/usr/bin/env bash

echo 'Taking care of apt ...'
add-apt-repository ppa:ondrej/php
apt-get update -y

echo 'Taking care of php ...'
apt-get install -y php7.1 php7.1-dev php7.1-mbstring php7.1-xml php7.1-zip php-pear

echo 'Taking care of composer ...'
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
mv /home/ubuntu/composer.phar /usr/local/bin/composer

echo 'Taking care of mongodb ...'
apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 0C49F3730359A14518585931BC711F9BA15703C6
echo 'deb http://repo.mongodb.org/apt/ubuntu xenial/mongodb-org/3.4 multiverse' | sudo tee /etc/apt/sources.list.d/mongodb-org-3.4.list
apt-get update
apt-get install -y mongodb-org
apt-get install pkg-config
pecl install mongodb
cp /home/vagrant/project/build/mongodb.ini /etc/php/7.1/mods-available/mongodb.ini
ln -sf /etc/php/7.1/mods-available/mongodb.ini /etc/php/7.1/cli/conf.d/20-mongodb.ini
service mongod restart
