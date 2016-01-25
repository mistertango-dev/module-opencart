<?php echo $header; ?>
<div class="container">
    <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
    </ul>
    <div class="row"><?php echo $column_left; ?>
        <?php if ($column_left && $column_right) { ?>
        <?php $class = 'col-sm-6'; ?>
        <?php } elseif ($column_left || $column_right) { ?>
        <?php $class = 'col-sm-9'; ?>
        <?php } else { ?>
        <?php $class = 'col-sm-12'; ?>
        <?php } ?>
        <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
            <h1><?php echo $heading_title; ?></h1>

            <h3><?php echo $text_history; ?></h3>
            <?php echo $histories; ?>
            <div class="buttons">
                <div class="pull-right"><a href="<?php echo $continue; ?>"
                                           class="btn btn-primary"><?php echo $button_continue; ?></a></div>
            </div>
            <?php echo $content_bottom; ?></div>
        <?php echo $column_right; ?></div>
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
