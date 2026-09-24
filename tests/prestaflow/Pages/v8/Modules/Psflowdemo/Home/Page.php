<?php

namespace Tests\Pages\v8\Modules\Psflowdemo\Home;

use PrestaFlow\Library\Pages\v8\FrontOffice\Page as BasePage;

class Page extends BasePage
{
    public function defineSelectors(): array
    {
        return [
            'block' => '#psflowdemo-block',
            'title' => '#psflowdemo-block h3',
        ];
    }

    public function hasBlock(): bool
    {
        return $this->isVisible($this->getSelector('block'), 5000);
    }

    public function getBlockTitle(): string
    {
        // getTextContent() returns false when the selector never shows up, and
        // hands back HTML entities as-is (&lt;, &amp;...): decode them so the
        // suites compare against the text a visitor actually reads.
        return html_entity_decode(
            (string) $this->getTextContent($this->getSelector('title')),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );
    }
}
