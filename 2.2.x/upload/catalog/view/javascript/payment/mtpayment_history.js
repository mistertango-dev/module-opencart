/**
 *
 * @type {{init: Function, updateOrderHistoriesTable: Function}}
 */
MTPayment.Information = {

    /**
     *
     */
    init: function () {
        setInterval(MTPayment.Information.updateOrderHistoriesTable, 30000);
    },

    /**
     *
     */
    updateOrderHistoriesTable: function () {
        $.ajax({
            type: 'GET',
            async: true,
            dataType: "json",
            url: MTPAYMENT_URL_HISTORIES,
            headers: {"cache-control": "no-cache"},
            cache: false,
            data: {
                order: MTPAYMENT_ORDER_ID
            },
            success: function (data) {
                $('#mtpayment-information-order-histories').replaceWith(data.html_table_order_histories);
                if (MTPayment.disallowDifferentPayment) {
                    $('.jsAllowDifferentPayment').remove();
                }
            }
        });
    }
};

/**
 *
 */
$(MTPayment.Information.init);
