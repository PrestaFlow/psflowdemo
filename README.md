# psflowdemo

Minimal PrestaShop module used as the PrestaFlow reference demo. It installs cleanly, renders a small badge on the home page, and ships with two PrestaFlow test suites (a browser-free smoke and a browser buyer-journey).

Every PrestaFlow launch artefact — the marketing site screenshots, the GitHub Action documentation, the dashboard tours — runs against this module so that the promise "your module tested end-to-end" is grounded in a real, publicly reproducible target.

## What's inside

```
psflowdemo/
├── psflowdemo.php               PS module: install, uninstall, displayHome hook
├── config.xml                   Module metadata
├── composer.json                Autoload + prestaflow/php-library dev-dep
├── tests/
│   ├── Smoke.php                Browser-free — 2 trivial assertions
│   └── DemoJourney.php          Browser — 4-step buyer journey (home → cart)
└── .github/workflows/
    └── prestaflow.yml           Two CI jobs: smoke + Flashlight journey
```

## Install locally

```bash
composer install
```

Then copy or symlink the module into a running PrestaShop's `modules/` directory and install from Back Office → Module Manager.

## Run the tests

```bash
composer prestaflow:json:file
```

The browser suite (`DemoJourney`) skips itself unless `PRESTAFLOW_FO_URL` is set — that variable is exported automatically by [PrestaFlow/github-action](https://github.com/PrestaFlow/github-action) when Flashlight is enabled.

## CI

Two GitHub Actions jobs, both wired to `PrestaFlow/github-action@main`:

- **Smoke** — `flashlight: false`. Fast, exercises the composer script + results upload. Runs on every push and PR.
- **Journey** — `flashlight: true`, `ps-version: '9.0.0'`. Boots PrestaShop 9.0.0 via Flashlight, installs this module, runs the buyer journey, uploads screenshots + report.

Two repo secrets are required:

- `PRESTAFLOW_TOKEN` — a valid PrestaFlow API token.
- `PRESTAFLOW_PROJECT_ID` — the project ID from the PrestaFlow dashboard.

## Why this module exists

We refuse to demo PrestaFlow against a made-up screenshot. Every claim on the landing page — visual scenarios, cloud runs, GitHub Action — is proven by running the same public code against the same public shop, and linking the resulting run. If a demo breaks, we know before shipping.
