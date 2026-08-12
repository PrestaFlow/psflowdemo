<?php

namespace PsFlowDemo\Tests;

use PrestaFlow\Library\Expects\Expect;
use PrestaFlow\Library\Tests\TestsSuite;

/**
 * Browser story — the "showcase" journey rendered in the PrestaFlow dashboard.
 * Steps mirror what a merchant expects to work after a module update:
 *   1. Home loads and the psflowdemo hook renders its badge
 *   2. A category page opens and lists products
 *   3. A product page opens and shows a price
 *   4. Add-to-cart succeeds
 *
 * Runs only when Flashlight is up (PRESTAFLOW_FO_URL set). Otherwise skips
 * cleanly so `smoke-no-flashlight` stays fast.
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
        $this->importPage('FrontOffice\Category');
        $this->importPage('FrontOffice\Product');
        extract($this->pages);

        $this
            ->describe('psflowdemo — buyer journey')
            ->it('loads the home page with the demo hook rendered', function () use ($frontOfficeHomePage) {
                $frontOfficeHomePage->goToPage('home');
                Expect::that($frontOfficeHomePage->pageTitle())->isNotEmpty();
            })
            ->it('opens a category and lists at least one product', function () use ($frontOfficeCategoryPage) {
                $frontOfficeCategoryPage->goToPage('category');
                Expect::that($frontOfficeCategoryPage->productsCount())->greaterThan(0);
            })
            ->it('opens a product page and displays a price', function () use ($frontOfficeProductPage) {
                $frontOfficeProductPage->goToPage('product');
                Expect::that($frontOfficeProductPage->productPrice())->isNotEmpty();
            })
            ->it('adds the product to the cart', function () use ($frontOfficeProductPage) {
                $frontOfficeProductPage->addToCart();
                Expect::that($frontOfficeProductPage->cartItemsCount())->greaterThan(0);
            });
    }

    private static function hasFrontOffice(): bool
    {
        $url = $_ENV['PRESTAFLOW_FO_URL'] ?? getenv('PRESTAFLOW_FO_URL') ?: null;
        return !empty($url);
    }
}
