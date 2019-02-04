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

function b2binpayWalletsSave() {
    var wallets = $('table#b2binpayWallets tbody tr').get().map(function (row) {
        return {
            'id': $(row).find('input[name^="b2binpay-wallet-id-"]').val(),
            'currency': $(row).find('input[name^="b2binpay-wallet-currency-"]').val(),
            'alpha': $(row).find('input[name^="b2binpay-wallet-alpha-"]').val(),
            'iso': $(row).find('input[name^="b2binpay-wallet-iso-"]').val()
        };
    });

    return JSON.stringify(wallets);
}

$(document).ready(function () {
    var counter = 0;

    $("#b2binpayWalletAdd").on("click", function () {
        var newRow = $("<tr>");
        var cols = "";

        cols += '<td><input type="text" class="form-control" name="b2binpay-wallet-id-' + counter + '"/></td>';
        cols += '<td><input type="text" class="form-control" name="b2binpay-wallet-currency-' + counter + '"/></td>';
        cols += '<td></td><td></td>';

        cols += '<td><input type="button" class="b2binpayWalletDel btn btn-md btn-danger "  value="Delete"></td>';
        newRow.append(cols);
        $("table#b2binpayWallets").append(newRow);
        counter++;
    });

    $("table#b2binpayWallets").on("click", ".b2binpayWalletDel", function () {
        $(this).closest("tr").remove();
        counter -= 1
    });

    $("button[name='submitB2binpayModule']").on("click", function () {
        $("#B2BINPAY_WALLETS").val(b2binpayWalletsSave());
    });
});
