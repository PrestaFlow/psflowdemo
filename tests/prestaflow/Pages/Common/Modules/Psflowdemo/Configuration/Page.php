<?php

namespace Tests\Pages\Common\Modules\Psflowdemo\Configuration;

use PrestaFlow\Library\Pages\BackOfficePage;

/**
 * psflowdemo configuration page (AdminModules&configure=psflowdemo).
 *
 * Holds the behaviour and the selectors shared by every PrestaShop version;
 * the v7/, v8/ and v9/ subclasses only override what diverges.
 */
class Page extends BackOfficePage
{
    public function defineSelectors(): array
    {
        return [
            'titleInput' => 'input[name="PSFLOWDEMO_TITLE"]',
            'submitButton' => 'button[name="submitPsflowdemo"]',
            'successAlert' => '.alert.alert-success',
            // "Invalid security token" interstitial, same template on 1.7, 8 and 9.
            'invalidTokenContinue' => 'a.btn-continue',
        ];
    }

    public function openConfiguration(string $boUrl): void
    {
        $this->goToUrl(rtrim($boUrl, '/') . '/index.php?controller=AdminModules&configure=psflowdemo');

        // A legacy admin URL without its per-employee token lands on the
        // "Invalid security token" page: a test cannot compute that token, so
        // it confirms the interstitial, which reloads the same URL with it.
        if ($this->isVisible($this->getSelector('invalidTokenContinue'), 2000)) {
            $this->click($this->getSelector('invalidTokenContinue'));
            $this->waitForPageReload();
        }
    }

    public function fillTitle(string $title): void
    {
        $this->setValue($this->getSelector('titleInput'), $title);
    }

    public function save(): void
    {
        $this->click($this->getSelector('submitButton'));
        $this->waitForPageReload();
    }

    public function hasSuccessMessage(): bool
    {
        return $this->isVisible($this->getSelector('successAlert'), 5000);
    }
}
