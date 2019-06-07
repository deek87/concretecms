#!/bin/bash
# enable php-fpm
apt-get update
apt-get install -y apache2 libapache2-mod-fastcgi
echo "PHP - $TRAVIS_PHP_VERSION"
cp ~/.phpenv/versions/$TRAVIS_PHP_VERSION/etc/php-fpm.conf.default ~/.phpenv/versions/$TRAVIS_PHP_VERSION/etc/php-fpm.conf
a2enmod rewrite actions fastcgi alias proxy_fcgi
echo "cgi.fix_pathinfo = 1" >> ~/.phpenv/versions/$TRAVIS_PHP_VERSION/etc/php.ini
sed -i -e "s,www-data,travis,g" /etc/apache2/envvars
chown -R travis:travis /var/lib/apache2/fastcgi
 ~/.phpenv/versions/$TRAVIS_PHP_VERSION/sbin/php-fpm
# configure apache virtual hosts
SCRIPT_DIR=$(dirname "${BASH_SOURCE[0]}")
cp -f $SCRIPT_DIR/concrete5.conf /etc/apache2/sites-available/000-default.conf
sed -e "s?%TRAVIS_BUILD_DIR%?$(pwd)?g" --in-place /etc/apache2/sites-available/000-default.conf
service apache2 restart
service php-fpm restart