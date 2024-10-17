#!/bin/bash
rm -rf var/*
rm -rf generated/*
rm -rf pub/static/frontend/*
php bin/magento cache:clean
php bin/magento cache:flush
php -d meory_limit=2G  bin/magento setup:static-content:deploy -f fr_FR en_GB ar_SA --area frontend
