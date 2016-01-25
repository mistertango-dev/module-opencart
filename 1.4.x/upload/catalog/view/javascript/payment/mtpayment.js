/**
 * @todo Update to latest changes from PrestaShop version
 *
 * @type {{isOpened: boolean, success: boolean, order: null, disallowDifferentPayment: boolean, transaction: null, customer: null, total: null, currency: null, language: null, init: Function, initButtonPay: Function, onOpen: Function, onOfflinePayment: Function, onSuccess: Function, onClose: Function, afterSuccess: Function}}
 */
MTPayment = {

    /**
     *
     */
    isOpened: false,

    /**
     *
     */
    success: false,

    /**
     *
     */
    order: null,

    /**
     *
     */
    disallowDifferentPayment: false,

    /**
     *
     */
    transaction: null,

    /**
     *
     */
    customer: null,

    /**
     *
     */
    total: null,

    /**
     *
     */
    currency: null,

    /**
     *
     */
    language: null,

    /**
     *
     */
    init: function () {
        mrTangoCollect.load();

        mrTangoCollect.set.recipient(MTPAYMENT_USERNAME);

        mrTangoCollect.onOpened = MTPayment.onOpen;
        mrTangoCollect.onClosed = MTPayment.onClose;

        mrTangoCollect.onSuccess = MTPayment.onSuccess;
        mrTangoCollect.onOffLinePayment = MTPayment.onOfflinePayment;

        MTPayment.initButtonPay();
    },

    /**
     *
     */
    initButtonPay: function () {
        $(document).on('click', '.mtpayment-button-pay', function (e) {
            e.preventDefault();

            if (typeof $(this).data('ws-id') != 'undefined') {
                mrTangoCollect.ws_id = $(this).data('ws-id');
            }

            MTPayment.order = null;

            if (typeof $(this).data('order') != 'undefined') {
                MTPayment.order = $(this).data('order');
            }

            MTPayment.transaction = $(this).data('transaction');
            MTPayment.customer = $(this).data('customer');
            MTPayment.amount = $(this).data('amount');
            MTPayment.currency = $(this).data('currency');
            MTPayment.language = $(this).data('language');

            mrTangoCollect.set.payer(MTPayment.customer);
            mrTangoCollect.set.amount(MTPayment.amount);
            mrTangoCollect.set.currency(MTPayment.currency);
            mrTangoCollect.set.description(MTPayment.transaction);
            mrTangoCollect.set.lang(MTPayment.language);

            mrTangoCollect.submit();
        });
    },

    /**
     *
     */
    onOpen: function () {
        MTPayment.isOpened = true;
    },

    /**
     *
     * @param response
     */
    onOfflinePayment: function (response) {
        mrTangoCollect.onSuccess = function () {};
        MTPayment.onSuccess(response);
    },

    /**
     *
     * @param response
     */
    onSuccess: function (response) {
        $.ajax({
            type: 'GET',
            async: true,
            dataType: "json",
            url: MTPayment.order ? MTPAYMENT_URL_CONFIRM : MTPAYMENT_URL_CONFIRM,
            headers: {"cache-control": "no-cache"},
            cache: false,
            data: {
                order: MTPayment.order ? MTPayment.order : null,
                transaction: MTPayment.transaction,
                websocket: mrTangoCollect.ws_id,
                amount: MTPayment.amount
            },
            success: function (data) {
                if (data.success) {
                    $('.jsAllowDifferentPayment').remove();
                    MTPayment.disallowDifferentPayment = true;
                    MTPayment.order = data.order;
                    MTPayment.success = true;

                    if (MTPayment.isOpened === false) {
                        MTPayment.afterSuccess();
                    }
                }
            }
        });
    },

    /**
     *
     */
    onClose: function () {
        MTPayment.isOpened = false;

        if (MTPayment.success) {
            MTPayment.afterSuccess();
        }
    },

    /**
     *
     */
    afterSuccess: function () {
        var operator = MTPAYMENT_URL_HISTORY.indexOf('?') === -1 ? '?' : '&';
        window.location.href = MTPAYMENT_URL_HISTORY + operator + 'order=' + MTPayment.order;
    }
};

/**
 * 
 */
$.getScript(MTPAYMENT_URL_SCRIPT, function (data, textStatus, jqxhr) {
    MTPayment.init();
});


