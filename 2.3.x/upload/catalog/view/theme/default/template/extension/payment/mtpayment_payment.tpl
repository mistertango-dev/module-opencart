<h2><?php echo $text_instruction; ?></h2>
<p><b><?php echo $text_description; ?></b></p>
<div class="well well-sm">
    <p><?php echo $text_payment; ?></p>
</div>
<div class="buttons">
    <div class="pull-right">
        <button type="button" id="button-confirm" class="btn btn-primary mtpayment-button-pay">
            <?php echo $button_confirm; ?>
        </button>
    </div>
</div>
<script type="text/javascript">
    var MTPAYMENT_USERNAME = "<?php echo $mtpayment_username; ?>";
    var MTPAYMENT_STANDARD_REDIRECT = "<?php echo $mtpayment_standard_redirect; ?>";
    var MTPAYMENT_URL_CONTINUE = "<?php echo $continue; ?>";
    var MTPAYMENT_URL_DATA = "<?php echo $mtpayment_url_data; ?>";
    var MTPAYMENT_URL_CONFIRM = "<?php echo $mtpayment_url_confirm; ?>";
    var MTPAYMENT_URL_HISTORY = "<?php echo $mtpayment_url_history; ?>";
    var MTPAYMENT_URL_SCRIPT = "https://payment.mistertango.com/resources/scripts/mt.collect.js?v=<?php echo time(); ?>";
</script>
<script type="text/javascript" src="/catalog/view/javascript/extension/payment/mtpayment.js?v=1.4.1"></script>
