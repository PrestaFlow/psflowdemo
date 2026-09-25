# psflowdemo

Minimal PrestaShop module used as the running example ("module fil rouge") of the PrestaFlow article series on the PrestaEdit blog, and as the PrestaFlow reference demo.

It does exactly three things — the smallest module that has a front, a back office and persisted configuration:

- renders a block on the home page (`displayHome` hook);
- shows a configurable title in that block;
- exposes a back-office configuration page to change the title.

Compatible PrestaShop 1.7.8 → 9 (module code: PHP 7.2+).

## The module contract

What the tests (and the articles) rely on:

| What | Value |
|---|---|
| Home block | `#psflowdemo-block` (also `data-psflowdemo="home-badge"`) |
| Block title | `#psflowdemo-block h3`, escaped on output |
| Template | `views/templates/hook/displayHome.tpl` |
| Configuration key | `PSFLOWDEMO_TITLE` (not multilingual) |
| Default title | `Bienvenue sur notre boutique` (set on install, deleted on uninstall) |
| BO configuration page | `index.php?controller=AdminModules&configure=psflowdemo` |
| Title field | `input[name="PSFLOWDEMO_TITLE"]` |
| Save button | `button[name="submitPsflowdemo"]` |
| Success message | `.alert.alert-success`, "Settings updated" (core translation, "Paramètres mis à jour" in French) |
| Empty title | refused, `.alert.alert-danger` |

The title is stored as typed (`Configuration::updateValue(..., $html = true)`, so PrestaShop's HTMLPurifier runs on it) and escaped by the template. The regression suite `Suites/Regression/NoXssInBlockTitle.php` keeps it that way.

## Layout

```
psflowdemo/
├── psflowdemo.php                 install/uninstall, getContent (HelperForm), hookDisplayHome
├── config.xml
├── views/templates/hook/displayHome.tpl
├── composer.json                  prestaflow/php-library as a dev dependency, in vendor-dev/
├── .env.example                   PRESTAFLOW_* variables, copy to .env
└── tests/
    ├── flashlight-init/           Flashlight init-scripts: install the module, seed PSFLOWDEMO_TITLE, enable payment
    └── prestaflow/                autoloaded as Tests\ (PSR-4)
        ├── Pages/
        │   ├── Common/Modules/Psflowdemo/Configuration/Page.php   shared BO page logic
        │   ├── v7/ v8/ v9/Modules/Psflowdemo/Configuration/Page.php
        │   └── v7/ v8/ v9/Modules/Psflowdemo/Home/Page.php
        └── Suites/                namespace Tests\Suites
            ├── Checkout.php       library scenario GuestCheckout
            ├── DisplayHome.php    block + default title on the home page
            ├── UpdateTitle.php    BO → front: change the title, check the home page
            ├── Smoke.php          browser-free checks on the sources
            └── Regression/NoXssInBlockTitle.php
```

`importPage('Modules\Psflowdemo\Home', domain: 'Tests')` resolves `Tests\Pages\v{major}\Modules\Psflowdemo\Home\Page` from `PRESTAFLOW_PS_VERSION`, with no fallback: every supported major version has its class.

## Install the module

Copy or symlink this directory as `modules/psflowdemo` in a PrestaShop shop, then:

```bash
php bin/console prestashop:module install psflowdemo
```

(or Back Office → Module Manager).

## Run the tests

```bash
composer install
cp .env.example .env    # then point it to your shop
composer prestaflow:all     # every suite in tests/prestaflow
composer prestaflow:smoke   # browser-free suite only, no shop needed
composer prestaflow -- run ./tests/prestaflow --group checkout   # any CLI call
```

`composer prestaflow` is the PrestaFlow CLI itself (`./vendor-dev/prestaflow/php-library/bin/prestaflow`): everything after `--` is passed to it. `process-timeout` is set to 0, otherwise Composer would kill a run after 300 seconds. Results land in `prestaflow/` (git-ignored). You need Chrome or Chromium on the machine.

Why `vendor-dev/` and not `vendor/`: when the module directory is mounted in a shop (Flashlight, local stack), PrestaShop includes `modules/<module>/vendor/autoload.php` for every installed module. The PrestaFlow library pulls Symfony 6 components that clash with the shop's own and crash it (checked on PrestaShop 8.1.7). Keeping dev dependencies in `vendor-dev/` avoids that.

### Against a throwaway Flashlight shop

```bash
docker network create psfd-net
docker run -d --name psfd-db --network psfd-net \
  -e MARIADB_ROOT_PASSWORD=prestashop -e MARIADB_DATABASE=prestashop \
  -e MARIADB_USER=prestashop -e MARIADB_PASSWORD=prestashop mariadb:11
docker run -d --name psfd-ps --network psfd-net -p 8899:80 \
  -e PS_DOMAIN=localhost:8899 -e MYSQL_HOST=psfd-db \
  -v "$PWD":/var/www/html/modules/psflowdemo \
  -v "$PWD/tests/flashlight-init":/tmp/init-scripts:ro \
  prestashop/prestashop-flashlight:8.1.7
```

Wait until `curl -s -o /dev/null -w '%{http_code}' http://localhost:8899/admin-dev/` answers `302`, then use this `.env`:

```env
PRESTAFLOW_PS_VERSION=8.1.7
PRESTAFLOW_LOCALE=en
PRESTAFLOW_FO_URL=http://localhost:8899/
PRESTAFLOW_BO_URL=http://localhost:8899/admin-dev/
PRESTAFLOW_BO_EMAIL=admin@prestashop.com
PRESTAFLOW_BO_PASSWD=prestashop
```

Clean up with `docker rm -f psfd-ps psfd-db && docker network rm psfd-net`.

Notes:

- The suites that change the title put the default back at the end, so a re-run against the same shop starts from a known state.
- Flashlight compiles Smarty templates once: after editing `displayHome.tpl`, clear `var/cache/*/smarty` in the container.
- A fresh Flashlight shop is installed with GB as its country, and its payment modules are restricted to GB only; France is not even active on the 1.7.8.11 image. The `Checkout` suite ships a French address, so it would find no payment method. `tests/flashlight-init/30-enable-payment.sh` activates France and opens the payment modules to every active country. With it, `Checkout` passes end to end on fresh 1.7.8.11, 8.1.7 and 9.0.0 shops (it fails at the address step without it).
- Don't run two PrestaFlow processes at the same time on one machine with the current library: they share one browser file and each run closes the other's Chrome at exit ("The page was closed and is not available anymore"). Fixed in PrestaFlow/php-library on branch `fix/checkout-page-closed`.

## CI

`.github/workflows/prestaflow.yml`:

- **smoke** — lints the PHP sources and runs `composer prestaflow:smoke`. No shop, no secret.
- **e2e** — matrix 1.7.8.11 / 8.1.7 / 9.0.0 through [PrestaFlow/github-action](https://github.com/PrestaFlow/github-action) with Flashlight. The Action mounts the repo as `modules/psflowdemo` and runs `composer prestaflow:json:file`; `tests/flashlight-init` installs the module at boot.

Repository secrets for the e2e job: `PRESTAFLOW_TOKEN` and `PRESTAFLOW_PROJECT_ID`.
