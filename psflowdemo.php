<?php
/**
 * psflowdemo — reference module for the PrestaFlow end-to-end demos.
 *
 * Front: a block on the home page (displayHome hook), #psflowdemo-block,
 * whose <h3> shows a configurable title.
 * Back office: a configuration page (AdminModules&configure=psflowdemo) with
 * a single text field, PSFLOWDEMO_TITLE, and a Save button (submitPsflowdemo).
 *
 * Compatible PrestaShop 1.7.8 → 9, PHP 7.2+.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Psflowdemo extends Module
{
    const CONFIG_TITLE = 'PSFLOWDEMO_TITLE';
    const DEFAULT_TITLE = 'Bienvenue sur notre boutique';
    const SUBMIT_ACTION = 'submitPsflowdemo';

    public function __construct()
    {
        $this->name = 'psflowdemo';
        $this->tab = 'front_office_features';
        $this->version = '1.1.0';
        $this->author = 'PrestaFlow';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7.8.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('PrestaFlow Demo');
        $this->description = $this->l('Displays a block with a configurable title on the home page. Reference module for the PrestaFlow end-to-end demos.');
        $this->confirmUninstall = $this->l('Uninstall the PrestaFlow demo module?');
    }

    public function install(): bool
    {
        return parent::install()
            && $this->registerHook('displayHome')
            && Configuration::updateValue(self::CONFIG_TITLE, self::DEFAULT_TITLE);
    }

    public function uninstall(): bool
    {
        return Configuration::deleteByName(self::CONFIG_TITLE)
            && parent::uninstall();
    }

    /**
     * Back-office configuration page: AdminModules&configure=psflowdemo.
     *
     * @return string
     */
    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit(self::SUBMIT_ACTION)) {
            $title = trim((string) Tools::getValue(self::CONFIG_TITLE));

            if ($title === '') {
                $output .= $this->displayError($this->l('The block title cannot be empty.'));
            } else {
                // Stored as typed ($html = true): escaping is the template's job,
                // not the storage's. Stripping tags here would hide an unescaped
                // template instead of fixing it.
                Configuration::updateValue(self::CONFIG_TITLE, $title, true);
                $output .= $this->displayConfirmation(
                    $this->trans('Settings updated', [], 'Admin.Notifications.Success')
                );
            }
        }

        return $output . $this->renderForm();
    }

    /**
     * @return string
     */
    protected function renderForm()
    {
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Settings', [], 'Admin.Global'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'name' => self::CONFIG_TITLE,
                        'label' => $this->l('Block title'),
                        'desc' => $this->l('Title displayed in the block on the home page.'),
                        'required' => true,
                    ],
                ],
                'submit' => [
                    'title' => $this->trans('Save', [], 'Admin.Actions'),
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = (int) $this->context->language->id;
        $helper->allow_employee_form_lang = (int) Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        $helper->identifier = $this->identifier;
        $helper->submit_action = self::SUBMIT_ACTION;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name
            . '&tab_module=' . $this->tab
            . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => [
                self::CONFIG_TITLE => Tools::getValue(self::CONFIG_TITLE, Configuration::get(self::CONFIG_TITLE)),
            ],
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => (int) $this->context->language->id,
        ];

        return $helper->generateForm([$form]);
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function hookDisplayHome($params)
    {
        $this->context->smarty->assign([
            'psflowdemo_title' => (string) Configuration::get(self::CONFIG_TITLE),
        ]);

        return $this->display(__FILE__, 'views/templates/hook/displayHome.tpl');
    }
}
