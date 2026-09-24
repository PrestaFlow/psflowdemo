<?php

namespace PsFlowDemo\Tests;

use PrestaFlow\Library\Expects\Expect;
use PrestaFlow\Library\Tests\TestsSuite;

/**
 * Browser-free smoke — runs without a live shop.
 * Two trivial assertions, enough to produce a valid results.json that
 * uploads cleanly through the GitHub Action.
 */
class Smoke extends TestsSuite
{
    public function __construct()
    {
        parent::__construct(loadGlobals: true, getBrowser: false);
    }

    public function init(): void
    {
        $this
            ->describe('psflowdemo — smoke')
            ->it('arithmetic sanity', function () {
                Expect::that(1 + 1)->equals(2);
            })
            ->it('module slug is psflowdemo', function () {
                Expect::that('psflowdemo')->equals('psflowdemo');
            });
    }
}
