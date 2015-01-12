#!/bin/bash

sed -i -e 's/newrelic.appname.*/newrelic.appname = "'"$NEW_RELIC_APPNAME"'"/' /etc/php-5.5.d/newrelic.ini