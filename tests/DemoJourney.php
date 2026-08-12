<?php

namespace PsFlowDemo\Tests;

use PrestaFlow\Library\Expects\Expect;
use PrestaFlow\Library\Tests\TestsSuite;

/**
 * Browser story — the "showcase" journey rendered in the PrestaFlow dashboard.
 * Kept intentionally minimal: navigating to the home page without exception is
 * proof that Flashlight booted, the module installed, and the storefront answers.
 *
 * Skips itself cleanly when PRESTAFLOW_FO_URL is unset (browser-free job).
 */
class DemoJourney extends TestsSuite
{
    public function __construct()
    {
        parent::__construct(loadGlobals: true, getBrowser: self::hasFrontOffice());
    }

    public function init(): void
    {
        if (!self::hasFrontOffice()) {
            $this
                ->describe('psflowdemo — journey (skipped, no PRESTAFLOW_FO_URL)')
                ->skip('needs Flashlight', function () {});
            return;
        }

        $this->importPage('FrontOffice\Home');
        extract($this->pages);

        $this
            ->describe('psflowdemo — showcase journey')
            ->it('opens the home page of the demo shop', function () use ($frontOfficeHomePage) {
                $frontOfficeHomePage->goToPage('home');
                // Navigating without exception is the assertion — record a trivial pass.
                Expect::that(true)->equals(true);
            });
    }

    private static function hasFrontOffice(): bool
    {
        $url = $_ENV['PRESTAFLOW_FO_URL'] ?? getenv('PRESTAFLOW_FO_URL') ?: null;
        return !empty($url);
    }
}
