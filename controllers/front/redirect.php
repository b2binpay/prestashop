<?php

use B2Binpay\Exception\B2BinpayException;

class B2binpayRedirectModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        $cart = $this->context->cart;

        if ($cart->id_customer == 0
            || $cart->id_address_delivery == 0
            || $cart->id_address_invoice == 0
            || !$this->module->active
            || empty($_REQUEST['b2binpay-crypto'])
        ) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $authorized = false;

        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'b2binpay') {
                $authorized = true;
                break;
            }
        }

        if (!$authorized) {
            die($this->module->l('This payment method is not available.', 'redirect'));
        }

        $customer = new Customer($cart->id_customer);

        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $wallet_id = $_REQUEST['b2binpay-crypto'];
        $wallet_list = json_decode(Configuration::get('B2BINPAY_WALLETS'), true);

        $wallet = array_reduce(
            $wallet_list,
            function ($carry, $item) use ($wallet_id) {
                if ($item['id'] === $wallet_id) {
                    $carry = $item;
                }

                return $carry;
            },
            array()
        );

        $currency = $this->context->currency;
        $total = (string)$cart->getOrderTotal(true, Cart::BOTH);

        $amount = $this->module->provider->convertCurrency(
            (string)$total,
            $currency->iso_code,
            $wallet['alpha']
        );

        if (!empty(Configuration::get('B2BINPAY_MARKUP'))) {
            $amount = $this->module->provider->addMarkup(
                $amount,
                $wallet['alpha'],
                Configuration::get('B2BINPAY_MARKUP')
            );
        }

        try {
            $bill = $this->module->provider->createBill(
                $wallet['id'],
                $amount,
                $wallet['alpha'],
                Configuration::get('B2BINPAY_LIFETIME'),
                $cart->id,
                $this->context->link->getModuleLink($this->module->name, 'callback', array(), true)
            );
        } catch (B2BinpayException $e) {
            die($this->module->l('Payment error: '.$e->getMessage(), 'redirect'));
        }

        if (empty($bill) || empty($bill->url)) {
            die($this->module->l('Payment gateway error.', 'redirect'));
        }

        $message = "B2BinPay created new invoice for $amount ETH. Bill ID: $bill->id";

        $this->module->validateOrder(
            $cart->id,
            Configuration::get('PS_OS_BANKWIRE'),
            $total,
            Configuration::get('B2BINPAY_TITLE'),
            $message,
            array(),
            (int)$currency->id,
            false,
            $customer->secure_key
        );

        Tools::redirect($bill->url);
    }
}
