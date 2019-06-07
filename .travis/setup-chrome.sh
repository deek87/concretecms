#!/bin/bash
echo "Download chrome driver and unzip it"
wget -c -nc --retry-connrefused --tries=0 http://chromedriver.storage.googleapis.com/74.0.3729.6/chromedriver_linux64.zip
unzip -o -q chromedriver_linux64.zip
chromedriver --url-base=wd/hub > /dev/null 2>&1&