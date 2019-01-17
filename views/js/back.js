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
