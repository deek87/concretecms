#!/bin/bash
echo "Download chrome driver and unzip it"
wget -c -nc --retry-connrefused --tries=0 http://chromedriver.storage.googleapis.com/74.0.3729.6/chromedriver_linux64.zip
unzip -o -q chromedriver_linux64.zip
echo "unzip complete"
export DISPLAY=:99.0
sh -e /etc/init.d/xvfb start
sleep 3 # give xvfb some time to start