#!/bin/bash
# rm -rf var/di var/cache/ var/page_cache/ generated/code/
# php bin/magento c:c
# php bin/magento setup:upgrade
# php -d memory_limit=-1 bin/magento setup:di:compile
# php bin/magento c:c
# php bin/magento indexer:reindex
# php -d memory_limit=-1 bin/magento s:s:d en_US fr_FR -f -j7
# php bin/magento c:c
# php bin/magento indexer:reindex




rm -rf var/*
rm -rf generated/*
rm -rf pub/static/*
php -d memory_limit=-1 bin/magento cache:clean
php -d memory_limit=-1 bin/magento setup:upgrade
php -d memory_limit=-1 bin/magento setup:di:compile
php -d memory_limit=-1 bin/magento cache:clean
php -d memory_limit=-1 bin/magento inde:rei
php -d memory_limit=-1 bin/magento s:s:d en_US fr_FR -f -j100