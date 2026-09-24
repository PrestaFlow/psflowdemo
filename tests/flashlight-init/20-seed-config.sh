#!/usr/bin/env bash
set -e

cd /var/www/html

# Known, stable default the scenarios can rely on. The install already sets it;
# this pins it again in case the module ships a different default one day.
# prestashop:config only exists from PrestaShop 8 on: skip it on 1.7.
if php bin/console list prestashop 2>/dev/null | grep -q 'prestashop:config'; then
    php bin/console prestashop:config set PSFLOWDEMO_TITLE --value "Bienvenue sur notre boutique"
fi
