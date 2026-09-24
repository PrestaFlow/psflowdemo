<?php

namespace Tests\Suites\Regression;

use PrestaFlow\Library\Expects\Expect;
use PrestaFlow\Library\Tests\TestsSuite;

/**
 * Regression #142 — the block title used to be printed unescaped by
 * views/templates/hook/displayHome.tpl. Fixed by escaping it on output
 * ({$psflowdemo_title|escape:'html':'UTF-8'}). Never delete this suite.
 */
class NoXssInBlockTitle extends TestsSuite
{
    public function init()
    {
        $this->importPage('BackOffice\Login');
        $this->importPage('Modules\Psflowdemo\Configuration', domain: 'Tests');
        $this->importPage('Modules\Psflowdemo\Home', domain: 'Tests');

        extract($this->pages);

        $payload = '<script>window.__xssFired=true</script>Bienvenue';
        $defaultTitle = 'Bienvenue sur notre boutique';

        $this
        ->describe('Regression #142 — pas de XSS via le titre du bloc')
        ->it('se connecte au BO', function () use ($backOfficeLoginPage) {
            $backOfficeLoginPage->goToPage('login');
            $backOfficeLoginPage->login();
        })
        ->it('accepte un titre contenant du HTML/JS depuis la config', function () use ($modulesPsflowdemoConfigurationPage, $payload) {
            $modulesPsflowdemoConfigurationPage->openConfiguration($_ENV['PRESTAFLOW_BO_URL']);
            $modulesPsflowdemoConfigurationPage->fillTitle($payload);
            $modulesPsflowdemoConfigurationPage->save();
        })
        ->it('n\'exécute pas le script sur la home', function () use ($modulesPsflowdemoHomePage) {
            $modulesPsflowdemoHomePage->goToPage('home');

            // '<script', not '<script>': PrestaShop runs HTML config values
            // through HTMLPurifier, which stores the tag as
            // <script type="text/javascript"><!--//--><![CDATA[ ...
            Expect::that($modulesPsflowdemoHomePage->getBlockTitle())
                ->contains('<script');

            $xssFired = $modulesPsflowdemoHomePage->getPage()
                ->evaluate('window.__xssFired === true')
                ->getReturnValue();

            Expect::that($xssFired)->isTheSameAs(false);
        })
        // Not in the article: leaves the shop with its default title.
        ->it('remet le titre par défaut', function () use ($modulesPsflowdemoConfigurationPage, $defaultTitle) {
            $modulesPsflowdemoConfigurationPage->openConfiguration($_ENV['PRESTAFLOW_BO_URL']);
            $modulesPsflowdemoConfigurationPage->fillTitle($defaultTitle);
            $modulesPsflowdemoConfigurationPage->save();
        });
    }
}
