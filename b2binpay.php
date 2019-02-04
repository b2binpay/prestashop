<?php
/**
 * NOTICE OF LICENSE
 *
 * Copyright (c) 2019 B2BinPay
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 *  @author    B2BINPAY <support@b2binpay.com>
 *  @copyright 2019 B2BinPay
 *  @license   https://github.com/b2binpay/prestashop/blob/master/LICENSE  The MIT License (MIT)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

if (defined('_PS_MODULE_DIR_')) {
    require_once _PS_MODULE_DIR_.'b2binpay/vendor/autoload.php';
}

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;
use B2Binpay\Currency as B2BinpayCurrency;
use B2Binpay\Provider;
use B2Binpay\Exception\ServerApiException;

class B2binpay extends PaymentModule
{
    public $provider;
    public $b2binpay_currency;

    public function __construct()
    {
        $this->name = 'b2binpay';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->is_eu_compatible = 1;
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->module_key = 'c1ae0a3ef16e14e71d3ded99c5faf216';
        $this->author = 'B2BinPay';
        $this->controllers = array('redirect', 'callback');

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';

        $this->bootstrap = true;

        parent::__construct();

        $this->provider = new Provider(
            Configuration::get('B2BINPAY_AUTH_KEY'),
            Configuration::get('B2BINPAY_AUTH_SECRET'),
            Configuration::get('B2BINPAY_TEST_MODE')
        );

        $this->b2binpay_currency = new B2BinpayCurrency();

        $this->displayName = $this->l('B2BinPay Crypto Payment Gateway');
        $this->description = $this->l(
            'Accept Bitcoin, Bitcoin Cash, Litecoin, Ethereum, and other CryptoCurrencies via B2BinPay.'
        );

        $this->warning = Configuration::get('B2BINPAY_WARNING');

        if (!count(Currency::checkPaymentCurrencies($this->id))) {
            $this->warning = $this->l('No currency has been set for this module.');
        }

        if (empty(Configuration::get('B2BINPAY_AUTH_KEY'))
            || empty(Configuration::get('B2BINPAY_AUTH_SECRET'))
        ) {
            $this->warning = $this->l(
                'B2BinPay Account details must be configured in order to use this module correctly.'
            );
        }
    }

    public function install()
    {
        if (!parent::install()
            || !$this->registerHook('paymentOptions')
            || !$this->registerHook('header')
            || !$this->registerHook('backOfficeHeader')
        ) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        return (
            Configuration::deleteByName('B2BINPAY_TITLE') &&
            Configuration::deleteByName('B2BINPAY_TEST_MODE') &&
            Configuration::deleteByName('B2BINPAY_AUTH_KEY') &&
            Configuration::deleteByName('B2BINPAY_AUTH_SECRET') &&
            Configuration::deleteByName('B2BINPAY_WALLETS') &&
            Configuration::deleteByName('B2BINPAY_MARKUP') &&
            Configuration::deleteByName('B2BINPAY_LIFETIME') &&
            Configuration::deleteByName('B2BINPAY_WARNING') &&
            parent::uninstall()
        );
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitB2binpayModule')) === true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);
        $this->context->smarty->assign(
            'wallets',
            json_decode(
                Configuration::get('B2BINPAY_WALLETS')
            )
        );
        $this->context->smarty->assign('warning', $this->warning);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/template.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitB2binpayModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'b2binpay-warning',
                        'name' => 'B2BINPAY_WARNING',
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'name' => 'B2BINPAY_TITLE',
                        'label' => $this->l('Title'),
                        'desc' => $this->l('The payment method title which a customer sees at the checkout'),
                        'required' => true,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Test Mode (Sandbox)'),
                        'name' => 'B2BINPAY_TEST_MODE',
                        'is_bool' => true,
                        'desc' => $this->l(
                            'Use this module in test mode. Warning: Sandbox and main gateway has their own credentials!'
                        ),
                        'required' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'name' => 'B2BINPAY_AUTH_KEY',
                        'label' => $this->l('Auth Key'),
                        'desc' => $this->l('B2BinPay API Auth Key'),
                        'required' => true,
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'name' => 'B2BINPAY_AUTH_SECRET',
                        'label' => $this->l('Auth Secret'),
                        'desc' => $this->l('B2BinPay API Auth Secret'),
                        'required' => true,
                    ),
                    array(
                        'type' => 'b2binpay-wallets',
                        'name' => 'B2BINPAY_WALLETS',
                        'label' => $this->l('Wallets'),
                        'required' => true,
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'name' => 'B2BINPAY_MARKUP',
                        'label' => $this->l('Markup (%)'),
                        'desc' => $this->l('Markup percentage for each payment'),
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'name' => 'B2BINPAY_LIFETIME',
                        'label' => $this->l('Order lifetime (seconds)'),
                        'desc' => $this->l('Lifetime for your orders in seconds'),
                        'required' => true,
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'B2BINPAY_TITLE' => Configuration::get('B2BINPAY_TITLE'),
            'B2BINPAY_TEST_MODE' => Configuration::get('B2BINPAY_TEST_MODE'),
            'B2BINPAY_AUTH_KEY' => Configuration::get('B2BINPAY_AUTH_KEY'),
            'B2BINPAY_AUTH_SECRET' => Configuration::get('B2BINPAY_AUTH_SECRET'),
            'B2BINPAY_WALLETS' => Configuration::get('B2BINPAY_WALLETS'),
            'B2BINPAY_MARKUP' => Configuration::get('B2BINPAY_MARKUP'),
            'B2BINPAY_LIFETIME' => Configuration::get('B2BINPAY_LIFETIME'),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }

        $this->warning = $this->checkConfig(
            Tools::getValue('B2BINPAY_AUTH_KEY'),
            Tools::getValue('B2BINPAY_AUTH_SECRET'),
            Tools::getValue('B2BINPAY_TEST_MODE'),
            Tools::getValue('B2BINPAY_WALLETS')
        );

        Configuration::updateValue('B2BINPAY_WARNING', $this->warning);
    }

    /**
     * Check B2BinPay authorization.
     * @param $key
     * @param $secret
     * @param $test
     * @param $wallets
     * @return string|null
     */
    protected function checkConfig($key, $secret, $test, $wallets)
    {
        if (empty($key) || empty($secret)) {
            return $this->l('You need to enter B2BinPay Auth Key/Secret.');
        }

        $this->provider = new Provider($key, $secret, $test);

        try {
            $this->provider->getAuthToken();
        } catch (ServerApiException $e) {
            return $this->l('Wrong B2BinPay Auth Key/Secret.');
        }

        if (empty(json_decode($wallets))) {
            return $this->l('You need to enter B2BinPay Wallet(s).');
        }

        $wallets_retrieved = $this->retrieveWallets($wallets);
        Configuration::updateValue('B2BINPAY_WALLETS', json_encode($wallets_retrieved));

        if (empty($wallets_retrieved)) {
            return $this->l('You entered wrong B2BinPay Wallet(s).');
        }

        return null;
    }

    /**
     * @param string $wallets_json
     * @return array
     */
    protected function retrieveWallets($wallets_json)
    {
        $wallets = json_decode($wallets_json);
        $wallets_updated = array();

        foreach ($wallets as $wallet) {
            if (empty($wallet->id)) {
                continue;
            }

            try {
                $b2binpay_wallet = $this->provider->getWallet((int)$wallet->id);
            } catch (ServerApiException $e) {
                continue;
            }

            $currency = (empty($wallet->currency))
                ? $this->b2binpay_currency->getName($b2binpay_wallet->currency->iso) : $wallet->currency;

            array_push(
                $wallets_updated,
                array(
                    'id' => $wallet->id,
                    'currency' => $currency,
                    'alpha' => $b2binpay_wallet->currency->alpha,
                    'iso' => $b2binpay_wallet->currency->iso,
                )
            );
        }

        return $wallets_updated;
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        $this->context->controller->addJS($this->_path.'views/js/back.js');
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookPaymentOptions($params)
    {
        if (!$this->active || !empty($this->warning)) {
            return;
        }

        $paymentOption = new PaymentOption();
        $paymentOption->setCallToActionText(Configuration::get('B2BINPAY_TITLE'))
            ->setForm($this->generateForm())
            ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/logo.png'));

        $payment_options = array($paymentOption);

        return $payment_options;
    }

    protected function generateForm()
    {
        $this->context->smarty->assign(
            array(
                'action' => $this->context->link->getModuleLink($this->name, 'redirect', array(), true),
                'crypto_list' => json_decode(Configuration::get('B2BINPAY_WALLETS')),
            )
        );

        return $this->context->smarty->fetch($this->local_path.'views/templates/front/payment_form.tpl');
    }
}
