<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <?php echo $breadcrumb['separator']; ?><a
                href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>
    <h1><?php echo $heading_title; ?></h1>

    <h3><?php echo $text_history; ?></h3>
    <?php echo $histories; ?>
    <?php echo $content_bottom; ?></div>
<script type="text/javascript">
    var MTPAYMENT_ORDER_ID = "<?php echo $order_id; ?>";
    var MTPAYMENT_USERNAME = "<?php echo $mtpayment_username; ?>";
    var MTPAYMENT_URL_DATA = "<?php echo $mtpayment_url_data; ?>";
    var MTPAYMENT_URL_CONFIRM = "<?php echo $mtpayment_url_confirm; ?>";
    var MTPAYMENT_URL_HISTORY = "<?php echo $mtpayment_url_history; ?>";
    var MTPAYMENT_URL_HISTORIES = "<?php echo $mtpayment_url_histories; ?>";
    var MTPAYMENT_URL_SCRIPT = "https://mistertango.com/resources/scripts/mt.collect.js?v=<?php echo time(); ?>";
</script>
<script type="text/javascript" src="/catalog/view/javascript/payment/mtpayment.js"></script>
<script type="text/javascript" src="/catalog/view/javascript/payment/mtpayment_history.js"></script>
<?php echo $footer; ?>
