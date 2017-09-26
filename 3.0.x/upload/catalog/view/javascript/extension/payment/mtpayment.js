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
    isOfflinePayment: false,

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

            $this = $(this);
            var order = typeof $this.data('order') == 'undefined'?null:$this.data('order');

            $.ajax({
                type: 'GET',
                async: true,
                dataType: "json",
                url: MTPAYMENT_URL_DATA,
                headers: {"cache-control": "no-cache"},
                cache: false,
                data: {
                    order: order
                },
                success: function (data) {
                    if (data.success) {
                        MTPayment.order = order;
                        MTPayment.transaction = data.transaction;
                        MTPayment.customer = data.customer;
                        MTPayment.amount = data.amount;
                        MTPayment.currency = data.currency;
                        MTPayment.language = data.language;

                        if (data.websocket) {
                            mrTangoCollect.ws_id = data.websocket;
                        }

                        mrTangoCollect.set.payer(MTPayment.customer);
                        mrTangoCollect.set.amount(MTPayment.amount);
                        mrTangoCollect.set.currency(MTPayment.currency);
                        mrTangoCollect.set.description(MTPayment.transaction);
                        mrTangoCollect.set.lang(MTPayment.language);

                        if (MTPAYMENT_CALLBACK_URL) {
                            mrTangoCollect.custom = {'callback': MTPAYMENT_CALLBACK_URL};
                        }

                        mrTangoCollect.submit();

                        MTPayment.isOfflinePayment = false;
                    }
                }
            });
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
        MTPayment.isOfflinePayment = true;
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
            url: MTPAYMENT_URL_CONFIRM,
            headers: {"cache-control": "no-cache"},
            cache: false,
            data: {
                order: MTPayment.order ? MTPayment.order : null,
                transaction: MTPayment.transaction,
                websocket: mrTangoCollect.ws_id,
                amount: MTPayment.amount,
                offline: MTPayment.isOfflinePayment
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
        var url = MTPAYMENT_URL_CONTINUE;

        if (MTPayment.isOfflinePayment) {
            var operator = MTPAYMENT_URL_HISTORY.indexOf('?') === -1 ? '?' : '&';
            url = MTPAYMENT_URL_HISTORY + operator + 'order=' + MTPayment.order;
        }

        window.location.href = url;
    }
};

/**
 *
 */
$.getScript(MTPAYMENT_URL_SCRIPT, function (data, textStatus, jqxhr) {
    MTPayment.init();
});
