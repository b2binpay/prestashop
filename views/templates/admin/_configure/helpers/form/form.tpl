{extends file="helpers/form/form.tpl"}

{block name="input"}
    {if $input.type === 'b2binpay-warning'}
        {if $warning}
            <div class="alert alert-warning">{$warning}</div>
        {/if}
    {elseif $input.type === 'b2binpay-wallets'}
        <input type="hidden" name="B2BINPAY_WALLETS" id="B2BINPAY_WALLETS" value="" class="" required="">
        <table id="b2binpayWallets" class=" table">
            <thead>
            <tr>
                <th class="col-lg-2">ID</th>
                <th class="col-lg-2">{l s='Currency' mod='b2binpay'}</th>
                <th class="col-lg-2">{l s='Currency alpha' mod='b2binpay'}</th>
                <th class="col-lg-2">{l s='Currency ISO' mod='b2binpay'}</th>
                <th class="col-lg-1"></th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$wallets key=i item=wallet}
                <tr>
                    <td>
                        <input type="text" name="b2binpay-wallet-id-{$i}" class="form-control" value="{$wallet->id}"/>
                    </td>
                    <td>
                        <input type="text" name="b2binpay-wallet-currency-{$i}" class="form-control"
                               value="{$wallet->currency}"/>
                    </td>
                    <td>
                        {$wallet->alpha}<input type="hidden" name="b2binpay-wallet-alpha-{$i}" class="form-control"
                                               value="{$wallet->alpha}"/>
                    </td>
                    <td>
                        {$wallet->iso}<input type="hidden" name="b2binpay-wallet-iso-{$i}" class="form-control"
                                             value="{$wallet->iso}"/>
                    </td>
                    <td><input type="button" class="b2binpayWalletDel btn btn-md btn-danger" value="Delete"></td>
                </tr>
            {/foreach}
            </tbody>
            <tfoot>
            <tr>
                <td colspan="5">
                    <input type="button" class="btn btn-lg btn-block btn-default" id="b2binpayWalletAdd"
                           value="Add Wallet"/>
                </td>
            </tr>
            </tfoot>
        </table>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
