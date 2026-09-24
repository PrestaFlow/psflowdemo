<?php

namespace Tests\Suites;

use PrestaFlow\Library\Expects\Expect;
use PrestaFlow\Library\Tests\TestsSuite;

class DisplayHome extends TestsSuite
{
    public function init()
    {
        $this->importPage('Modules\Psflowdemo\Home', domain: 'Tests');

        extract($this->pages);

        $this
        ->describe('Bloc psflowdemo sur la home')
        ->it('affiche le bloc', function () use ($modulesPsflowdemoHomePage) {
            $modulesPsflowdemoHomePage->goToPage('home');

            Expect::that($modulesPsflowdemoHomePage->hasBlock())
                ->isTheSameAs(true);
        })
        ->it('affiche le titre par défaut', function () use ($modulesPsflowdemoHomePage) {
            Expect::that($modulesPsflowdemoHomePage->getBlockTitle())
                ->contains('Bienvenue');
        });
    }
}
