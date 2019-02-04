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
