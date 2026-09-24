<?php

namespace Tests\Suites;

use PrestaFlow\Library\Scenarios\GuestCheckout;
use PrestaFlow\Library\Tests\TestsSuite;

class Checkout extends TestsSuite
{
    public function init()
    {
        $this
            ->describe('Parcours d\'achat invité')
            // Not in the article: the scenario defaults to a French shop. Follow
            // PRESTAFLOW_LOCALE so it also runs on an English one (Flashlight).
            ->scenario(GuestCheckout::class, [
                'locale' => $_ENV['PRESTAFLOW_LOCALE'] ?? 'fr',
            ]);
    }
}
