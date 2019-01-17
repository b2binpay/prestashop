<form action="{$action}" id="b2binpay-payment-form" method="POST">
    <ul class="b2binpay-currency-list">
        {foreach from=$crypto_list item=crypto}
            <li class="b2binpay-currency-item">
                <input type="radio" id="wc-b2binpay-{$crypto->alpha}" class="" name="b2binpay-crypto"
                       value="{$crypto->id}">
                <label for="wc-b2binpay-{$crypto->alpha}">{$crypto->currency}</label>
            </li>
        {/foreach}
    </ul>
</form>
