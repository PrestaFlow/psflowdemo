<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Psflowdemo extends Module
{
    public function __construct()
    {
        $this->name = 'psflowdemo';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'PrestaFlow';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7.0.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('PrestaFlow Demo');
        $this->description = $this->l('Minimal module used as the reference project for PrestaFlow end-to-end demos and CI smoke runs.');
    }

    public function install(): bool
    {
        return parent::install()
            && $this->registerHook('displayHome');
    }

    public function uninstall(): bool
    {
        return parent::uninstall();
    }

    public function hookDisplayHome(array $params): string
    {
        return '<div data-psflowdemo="home-badge" style="text-align:center;padding:1rem;font:14px/1.4 system-ui;color:#6366f1;">PrestaFlow demo module loaded ✔</div>';
    }
}
