<?php

namespace Tests\Pages\v9\Modules\Psflowdemo\Home;

use Tests\Pages\v8\Modules\Psflowdemo\Home\Page as BasePage;

/**
 * The home block is rendered by the module's own template, identical on every
 * PrestaShop version: nothing to override. The class still has to exist —
 * importPage() builds the class name from PRESTAFLOW_PS_VERSION and never
 * falls back to another version.
 */
class Page extends BasePage
{
}
