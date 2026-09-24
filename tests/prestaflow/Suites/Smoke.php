<?php

namespace Tests\Suites;

use PrestaFlow\Library\Expects\Expect;
use PrestaFlow\Library\Tests\TestsSuite;

/**
 * Browser-free checks on the module sources: runs without a shop, so CI can
 * gate every push on it before booting Flashlight.
 */
class Smoke extends TestsSuite
{
    public function __construct()
    {
        parent::__construct(loadGlobals: true, getBrowser: false);
    }

    public function init()
    {
        $root = dirname(__DIR__, 3);
        $module = (string) file_get_contents($root . '/psflowdemo.php');
        $template = (string) file_get_contents($root . '/views/templates/hook/displayHome.tpl');

        $this
            ->describe('psflowdemo — smoke (sans navigateur)')
            ->it('le module déclare la clé de configuration PSFLOWDEMO_TITLE', function () use ($module) {
                Expect::that($module)->contains("'PSFLOWDEMO_TITLE'", "psflowdemo.php should contain '{expected}'");
            })
            ->it('le formulaire BO soumet via submitPsflowdemo', function () use ($module) {
                Expect::that($module)->contains("'submitPsflowdemo'", "psflowdemo.php should contain '{expected}'");
            })
            ->it('le template rend le bloc #psflowdemo-block', function () use ($template) {
                Expect::that($template)->contains('id="psflowdemo-block"', "displayHome.tpl should contain '{expected}'");
            })
            ->it('le template échappe le titre (régression #142)', function () use ($template) {
                Expect::that($template)->contains("{\$psflowdemo_title|escape:'html':'UTF-8'}", "displayHome.tpl should contain '{expected}'");
            });
    }
}
