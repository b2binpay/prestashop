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

class B2binpayCallbackModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        if (Tools::isSubmit('tracking_id') == false
            || Tools::isSubmit('status') == false
            || Tools::isSubmit('amount') == false
            || Tools::isSubmit('actual_amount') == false
        ) {
            header('HTTP/1.1 400 Bad Request');
            exit();
        }

        $headers = getallheaders();
        $b2binpay_auth = $this->module->provider->getAuthorization();

        if (empty($headers['Authorization']) || ($headers['Authorization'] !== $b2binpay_auth)) {
            header('HTTP/1.1 401 Unauthorized');
            exit();
        }

        $tracking_id = Tools::getValue('tracking_id');
        $status = (string)Tools::getValue('status');

        $cart = new Cart((int)$tracking_id);
        $order = Order::getByCartId((int)$cart->id);

        $status_list = $this->getStatus();

        $amount = Tools::getValue('amount');
        $actual_amount = Tools::getValue('actual_amount');

        $history = new OrderHistory();
        $history->id_order = $order->id;

        if (($status === '2') && ($amount === $actual_amount)) {
            $payment_status = Configuration::get('PS_OS_PAYMENT');
        } else {
            $payment_status = $status_list[$status];
        }

        $history->changeIdOrderState(
            $payment_status,
            $order,
            true
        );

        $history->add();
        $order->save();

        exit("OK");
    }

    private function getStatus()
    {
        return array(
            '-2' => Configuration::get('PS_OS_ERROR'),
            '-1' => Configuration::get('PS_OS_ERROR'),
            '1' => Configuration::get('PS_OS_BANKWIRE'),
            '2' => Configuration::get('PS_OS_BANKWIRE'),
            '3' => Configuration::get('PS_OS_ERROR'),
            '4' => Configuration::get('PS_OS_PAYMENT'),
        );
    }
}
