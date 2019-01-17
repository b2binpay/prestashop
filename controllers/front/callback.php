<?php

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
        return [
            '-2' => Configuration::get('PS_OS_ERROR'),
            '-1' => Configuration::get('PS_OS_ERROR'),
            '1' => Configuration::get('PS_OS_BANKWIRE'),
            '2' => Configuration::get('PS_OS_BANKWIRE'),
            '3' => Configuration::get('PS_OS_ERROR'),
            '4' => Configuration::get('PS_OS_PAYMENT'),
        ];
    }
}
