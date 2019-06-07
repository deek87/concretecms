#!/bin/bash
# enable php-fpm
cp ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf.default ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf
a2enmod rewrite actions fastcgi alias
echo "cgi.fix_pathinfo = 1" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
sed -i -e "s,www-data,travis,g" /etc/apache2/envvars
chown -R travis:travis /var/lib/apache2/fastcgi
 ~/.phpenv/versions/$(phpenv version-name)/sbin/php-fpm
# configure apache virtual hosts
cp -f build/travis-ci-apache /etc/apache2/sites-available/000-default.conf
sed -e "s?%TRAVIS_BUILD_DIR%?$(pwd)?g" --in-place /etc/apache2/sites-available/000-default.conf
service start apache2