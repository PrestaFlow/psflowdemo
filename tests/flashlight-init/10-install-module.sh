#!/usr/bin/env bash
set -e

cd /var/www/html

# The module lives at /var/www/html/modules/psflowdemo thanks to flashlight-mount: auto
php bin/console prestashop:module install psflowdemo
