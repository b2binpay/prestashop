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
