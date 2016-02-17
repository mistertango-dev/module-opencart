<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content">
    <div class="top">
        <div class="left"></div>
        <div class="right"></div>
        <div class="center">
            <h1><?php echo $heading_title; ?></h1>
        </div>
    </div>
    <div class="middle">
        <h3><?php echo $text_history; ?></h3>
        <?php echo $histories; ?>
    </div>
    <div class="bottom">
        <div class="left"></div>
        <div class="right"></div>
        <div class="center"></div>
    </div>
</div>
<script type="text/javascript">
    var MTPAYMENT_ORDER_ID = "<?php echo $order_id; ?>";
    var MTPAYMENT_USERNAME = "<?php echo $mtpayment_username; ?>";
    var MTPAYMENT_URL_CONFIRM = "<?php echo $mtpayment_url_confirm; ?>";
    var MTPAYMENT_URL_HISTORY = "<?php echo $mtpayment_url_history; ?>";
    var MTPAYMENT_URL_HISTORIES = "<?php echo $mtpayment_url_histories; ?>";
    var MTPAYMENT_URL_SCRIPT = "https://mistertango.com/resources/scripts/mt.collect.js?v=<?php echo time(); ?>";
</script>
<script type="text/javascript" src="/catalog/view/javascript/payment/mtpayment.js"></script>
<script type="text/javascript" src="/catalog/view/javascript/payment/mtpayment_history.js"></script>
<?php echo $footer; ?>
