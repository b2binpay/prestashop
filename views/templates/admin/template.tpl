{*
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
 *}

<div class="panel">
    <div class="row b2binpay-header">
        <img src="{$module_dir|escape:'html':'UTF-8'}views/img/b2binpay.svg" class="col-xs-6 col-md-2 text-center"
             id="payment-logo"/>
        <div class="col-xs-6 col-md-10 text-center text-muted">
            {l s='Accept Bitcoin, Bitcoin Cash, Litecoin, Ethereum, and other CryptoCurrencies on your PrestaShop store.' mod='b2binpay'}
        </div>
    </div>

    <div class="b2binpay-content hidden-xs">
        <hr/>
        <div class="row">
            <div class="col-md-5">
                <h5>{l s='Features:' mod='b2binpay'}</h5>
                <ul class="ul-spaced">
                    <li>
                        {l s='Fully automatic: set and forget.' mod='b2binpay'}
                    </li>
                    <li>
                        {l s='The lowest processing fee of 0.5%.' mod='b2binpay'}
                    </li>
                    <li>
                        {l s='CryptoCurrency amount is calculated using real-time exchange rates.' mod='b2binpay'}
                    </li>
                    <li>
                        {l s='Your customers can choose between CryptoCurrencies available in your B2BinPay account.' mod='b2binpay'}
                    </li>
                    <li>
                        {l s='Sandboxing available: you can request a testing account.' mod='b2binpay'}
                    </li>
                    <li>
                        {l s='You can set additional markup for each payment to compensate our fee.' mod='b2binpay'}
                    </li>
                    <li>
                        {l s='Secure checkout.' mod='b2binpay'}
                    </li>
                    <li>
                        {l s='Automatic payment confirmations.' mod='b2binpay'}
                    </li>
                    <li>
                        {l s='No chargebacks.' mod='b2binpay'}
                    </li>
                    <li>
                        {l s='No recurring fees or hidden charges.' mod='b2binpay'}
                    </li>
                </ul>
            </div>

            <div class="col-md-3">
                <h5>{l s='Available CryptoCurrencies' mod='b2binpay'}</h5>
                <div class="row">
                    <ul class="ul-spaced col-md-6">
                        <li>Bitcoin</li>
                        <li>Bitcoin Cash</li>
                        <li>Ethereum</li>
                        <li>DASH</li>
                        <li>Litecoin</li>
                        <li>Monero</li>
                        <li>NEO</li>
                        <li>NEM</li>
                        <li>Ripple</li>
                    </ul>
                    <ul class="ul-spaced col-md-6">
                        <li>B2BX</li>
                        <li>OMG</li>
                        <li>PAX</li>
                        <li>TUSD</li>
                        <li>GUSD</li>
                        <li>USDC</li>
                        <li>BNB</li>
                        <li>DOGE</li>
                        <li>Cardano</li>
                    </ul>
                </div>
            </div>

            <div class="col-md-4">
                <h5>{l s='How does it work?' mod='b2binpay'}</h5>
                <iframe width="335" height="188" src="https://www.youtube-nocookie.com/embed/-646IjktYno"
                        frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen></iframe>
            </div>
        </div>
    </div>
</div>

<div class="panel">
    <p class="text-muted">
        <i class="icon icon-info-circle"></i> {l s='This extension requires a B2BinPay account. To create one visit' mod='b2binpay'}
        <a href="https://b2binpay.com" target="_blank">b2binpay.com</a>.
        {l s='Creating an account is totally free.' mod='b2binpay'}
        {l s='For more details, consider reading our' mod='b2binpay'}
        <a href="https://b2binpay.com/docs/b2binpay-public_offer.pdf"
           target="_blank">{l s='Public Offer' mod='b2binpay'}</a>.
    </p>
</div>
