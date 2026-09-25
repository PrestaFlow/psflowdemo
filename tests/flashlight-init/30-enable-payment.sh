#!/usr/bin/env bash
set -e

# A fresh Flashlight shop is installed with GB as its country, and PrestaShop
# restricts every payment module to that country only (ps_module_country).
# Any other delivery address - the French one of the library's GuestCheckout
# scenario, say - is then offered no payment method, and the checkout stops at
# the payment step. Activate France (see below) and open the payment modules
# to every active country, in every shop. Idempotent: INSERT IGNORE on the
# (module, shop, country) key.
cat > /tmp/enable-payment.php <<'PHP'
<?php
require_once '/var/www/html/config/config.inc.php';

$db = Db::getInstance();
$p = _DB_PREFIX_;

// The library's checkout scenarios ship a French address. France is active on
// the 8.x and 9.x images but not on 1.7.8.11: activate it, or the address form
// has no France to offer.
$db->execute("UPDATE {$p}country SET active = 1 WHERE iso_code = 'FR'");

$modules = $db->executeS(
    "SELECT DISTINCT hm.id_module FROM {$p}hook_module hm
     JOIN {$p}hook h ON h.id_hook = hm.id_hook
     WHERE h.name = 'paymentOptions'"
) ?: [];
$countries = $db->executeS("SELECT id_country FROM {$p}country WHERE active = 1") ?: [];
$shops = $db->executeS("SELECT id_shop FROM {$p}shop") ?: [];

$rows = 0;
foreach ($modules as $module) {
    foreach ($shops as $shop) {
        foreach ($countries as $country) {
            $db->execute(
                "INSERT IGNORE INTO {$p}module_country (id_module, id_shop, id_country) VALUES ("
                . (int) $module['id_module'] . ', ' . (int) $shop['id_shop'] . ', ' . (int) $country['id_country'] . ')'
            );
            $rows += (int) $db->Affected_Rows();
        }
    }
}

echo count($modules) . ' payment module(s) opened to ' . count($countries) . ' active countries (' . $rows . " new rows)\n";
PHP

php /tmp/enable-payment.php
rm -f /tmp/enable-payment.php
