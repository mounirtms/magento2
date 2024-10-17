#!/bin/bash
rm -rf var/*
rm -rf generated/*
rm -rf pub/static/*
php bin/magento cache:clean
php -d memory_limit=-1 bin/magento setup:di:compile
php bin/magento cache:clean
php bin/magento indexer:reindex
php -dmemory_limit=-1 bin/magento s:s:d en_US fr_FR -f -j100 --theme Sm/Market --theme Magento/backend

