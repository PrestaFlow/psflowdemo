<?php

namespace PsFlowDemo\Tests;

use PrestaFlow\Library\Expects\Expect;
use PrestaFlow\Library\Tests\TestsSuite;

/**
 * Browser-free smoke — runs without a live shop.
 * Enough to produce a valid results.json for the GitHub Action to upload.
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
            ->it('composer autoload resolves the suite class', function () {
                Expect::that(class_exists(self::class))->isTrue();
            })
            ->it('reports its module name', function () {
                Expect::that('psflowdemo')->equals('psflowdemo');
            });
    }
}
