<?php

namespace Tests\Suites;

use PrestaFlow\Library\Expects\Expect;
use PrestaFlow\Library\Tests\TestsSuite;

class UpdateTitle extends TestsSuite
{
    public function init()
    {
        $this->importPage('BackOffice\Login');
        $this->importPage('Modules\Psflowdemo\Home', domain: 'Tests');

        extract($this->pages);

        $newTitle = 'Titre mis à jour par PrestaFlow';
        $defaultTitle = 'Bienvenue sur notre boutique';
        $boUrl    = rtrim($_ENV['PRESTAFLOW_BO_URL'], '/');

        $this
        ->describe('Modification du titre depuis le BO')
        ->it('se connecte au BO', function () use ($backOfficeLoginPage) {
            $backOfficeLoginPage->goToPage('login');
            $backOfficeLoginPage->login();
            Expect::that($backOfficeLoginPage->getPageTitle())->isNotEmpty();
        })
        ->it('met à jour le titre du bloc', function () use ($backOfficeLoginPage, $boUrl, $newTitle) {
            $backOfficeLoginPage->goToUrl(
                $boUrl . '/index.php?controller=AdminModules&configure=psflowdemo'
            );
            // Not in the article: without its token, the URL above lands on
            // PrestaShop's "Invalid security token" page. Confirm it.
            $backOfficeLoginPage->click('a.btn-continue');
            $backOfficeLoginPage->waitForPageReload();

            $backOfficeLoginPage->setValue('input[name="PSFLOWDEMO_TITLE"]', $newTitle);
            $backOfficeLoginPage->click('button[name="submitPsflowdemo"]');
            $backOfficeLoginPage->waitForPageReload();
        })
        ->it('affiche le nouveau titre sur la home', function () use ($modulesPsflowdemoHomePage, $newTitle) {
            $modulesPsflowdemoHomePage->goToPage('home');

            Expect::that($modulesPsflowdemoHomePage->getBlockTitle())
                ->contains($newTitle);
        })
        // Not in the article: puts the default title back so that DisplayHome
        // ("contains Bienvenue") still passes when the suites are re-run
        // against the same shop.
        ->it('remet le titre par défaut', function () use ($backOfficeLoginPage, $boUrl, $defaultTitle) {
            $backOfficeLoginPage->goToUrl(
                $boUrl . '/index.php?controller=AdminModules&configure=psflowdemo'
            );
            $backOfficeLoginPage->click('a.btn-continue');
            $backOfficeLoginPage->waitForPageReload();

            $backOfficeLoginPage->setValue('input[name="PSFLOWDEMO_TITLE"]', $defaultTitle);
            $backOfficeLoginPage->click('button[name="submitPsflowdemo"]');
            $backOfficeLoginPage->waitForPageReload();
        });
    }
}
