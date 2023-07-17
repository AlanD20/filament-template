#!/bin/bash

echo "========================================================"
echo "    Run via sudo"
echo "========================================================"

read -p "Continue the installation? (y to continue, any key to cancel)"

if [ "${REPLY:0:1}" != "y" ]; then
  exit 0
fi

project_name="filament-template"
project_path="/var/www/$project_name"

# save current path
pwd=$(pwd)

echo "=========================================="
echo "Update & Add Repositories"
echo "=========================================="

apt update && apt -y upgrade && apt -y install apt-transport-https ca-certificates curl gnupg lsb-release
# Add repositories

# composer
add-apt-repository -y universe
# php
add-apt-repository -y ppa:ondrej/php

echo "=========================================="
echo "Update successfull"
echo "Repositories Added"
echo "=========================================="

#update the repositores
apt update && apt -y upgrade

#update kernal
echo "=========================================="
echo "Updating kernals..."
echo "=========================================="

apt -y install linux-headers-$(uname -r) build-essential dkms

echo "=========================================="
echo "Kernal updated successfully!"
echo "=========================================="

#installation
echo "=========================================="
echo "Installing....."
echo "=========================================="

apt-get -y install software-properties-common unattended-upgrades php8.2 php8.2-common php8.2-snmp php8.2-xml php8.2-zip php8.2-mbstring php8.2-curl php8.2-cgi php8.2-fpm php8.2-gd php8.2-imagick php8.2-intl php8.2-memcached php8.2-mysql php8.2-sqlite3 php8.2-opcache php8.2-pgsql php8.2-psr php8.2-redis nginx mysql-server certbot unzip dos2unix supervisor

echo "=========================================="
echo "Installation completed!"
echo "=========================================="

#install composer
echo "=========================================="
echo "Composer setup..."
echo "=========================================="

cd ~
curl -sS https://getcomposer.org/installer | php
mv ~/composer.phar /usr/local/bin/composer

echo "=========================================="
echo "Composer completed!"
echo "=========================================="

#enable auto updates
dpkg-reconfigure -f noninteractive --priority=low unattended-upgrades

#remove apache2
echo "=========================================="
echo "Uninstalling Apache2..."
echo "=========================================="

systemctl stop apache2
apt remove apache2 --purge

echo "=========================================="
echo "Apache2 uninstalled!"
echo "=========================================="

#Replace php.ini texts
echo "=========================================="
echo 'Fixing php.ini File...'
echo "=========================================="

sed -i 's/;cgi.fix_pathinfo=0/cgi.fix_pathinfo=1/gI' /etc/php/8.2/fpm/php.ini
sed -i 's/;extension=fileinfo/extension=fileinfo/gI' /etc/php/8.2/fpm/php.ini
sed -i 's/;extension=gd/extension=gd/gI' /etc/php/8.2/fpm/php.ini
sed -i 's/;extension=imap/extension=imap/gI' /etc/php/8.2/fpm/php.ini
sed -i 's/;extension=mbstring/extension=mbstring/gI' /etc/php/8.2/fpm/php.ini
sed -i 's/;extension=exif/extension=exif/gI' /etc/php/8.2/fpm/php.ini
sed -i 's/;extension=mysqli/extension=mysqli/gI' /etc/php/8.2/fpm/php.ini
sed -i 's/;extension=sqlite3/extension=sqlite3/gI' /etc/php/8.2/fpm/php.ini
sed -i 's/;extension=openssl/extension=openssl/gI' /etc/php/8.2/fpm/php.ini
sed -i 's/;extension=pdo_mysql/extension=pdo_mysql/gI' /etc/php/8.2/fpm/php.ini
sed -i 's/;extension=pdo_sqlite/extension=pdo_sqlite/gI' /etc/php/8.2/fpm/php.ini
sed -i 's/;extension=sockets/extension=sockets/gI' /etc/php/8.2/fpm/php.ini

echo "=========================================="
echo 'php.ini File fixed!'
echo "=========================================="

#Create Swap file
echo "=========================================="
echo 'Creating swapfile...'
echo "=========================================="

cd /
fallocate -l 2G /swapfile
chmod 600 swapfile
mkswap /swapfile
swapon /swapfile

echo "=========================================="
echo 'Swapfile created!'
echo "=========================================="

#Enable ufw
echo "=========================================="
echo 'Enabling ufw...'
echo "=========================================="

ufw allow 'Nginx FULL'
ufw allow 'OpenSSH'
ufw allow ssh
ufw --force enable

echo "=========================================="
echo 'ufw Enabled!'
echo "=========================================="

#Final steps
yes | apt autoremove
apt-get -y upgrade
. ~/.bashrc
systemctl restart php8.2-fpm
systemctl restart nginx

echo "=========================================="
echo 'Copying Config'
echo "=========================================="

cp $pwd/nginx.conf "/etc/nginx/sites-available/$project_name.conf"
cp $pwd/scheduler.conf "/etc/supervisor/conf.d/scheduler.conf"

#create symbolic link
ln -s "/etc/nginx/sites-available/$project_name.conf" /etc/nginx/sites-enabled/

supervisorctl reread
supervisorctl update

echo "=========================================="
echo 'Config Copied!'
echo "=========================================="

cat <<MANUAL_TASKS
==============================================
            Remaning Manual Tasks....
==============================================

- Setup mysql secure installation
mysql_secure_installation

- Change root password
passwd root

- Database Setup
1. Login to mysql:
mysql -u root -p

2. Change User Password with the following command:
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'Your Password';

    -   Flush Cache.
FLUSH PRIVILEGES;

    -   Create Database.
CREATE DATABASE mydb;

3. Restart services:
    $ systemctl restart php8.2-fpm
    $ systemctl restart nginx

4. Install & Migrate the project
  cd deploy && ./install.sh

MANUAL_TASKS

source ~/.bashrc
