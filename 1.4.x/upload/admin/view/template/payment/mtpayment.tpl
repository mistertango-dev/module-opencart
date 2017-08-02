<?php echo $header; ?>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div class="box">
    <div class="left"></div>
    <div class="right"></div>
    <div class="heading">
        <h1 style="background-image: url('view/image/payment.png');"><?php echo $heading_title; ?></h1>

        <div class="buttons"><a onclick="$('#form').submit();"
                                class="button"><span><?php echo $button_save; ?></span></a><a
                    onclick="location='<?php echo $cancel; ?>';"
                    class="button"><span><?php echo $button_cancel; ?></span></a></div>
    </div>
    <div class="content">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
            <table class="form">
                <tr>
                    <td><?php echo $entry_username; ?></td>
                    <td>
                        <input type="text" name="mtpayment_username" value="<?php echo $mtpayment_username; ?>"
                               placeholder="<?php echo $entry_username; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $entry_secret_key; ?></td>
                    <td>
                        <input type="text" name="mtpayment_secret_key" value="<?php echo $mtpayment_secret_key; ?>"
                               placeholder="<?php echo $entry_secret_key; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $entry_callback_url; ?></td>
                    <td>
                        <input type="text" name="mtpayment_callback_url" value="<?php echo $mtpayment_callback_url; ?>"
                               placeholder="<?php echo $entry_callback_url; ?>"/>
                    </td>
                </tr>
				<tr>
				    <td><?php echo $entry_standard_redirect; ?></td>
				    <td>
				      <input type="checkbox" name="mtpayment_standard_redirect"
				             value="1"<?php if ($mtpayment_standard_redirect): ?> checked="checked"<?php endif; ?> />
				    </td>
          		</tr>
                <tr>
                    <td><?php echo $entry_order_pending_status; ?></td>
                    <td>
                        <select name="mtpayment_order_pending_status_id">
                            <?php foreach ($order_statuses as $order_status) { ?>
                            <?php if ($order_status['order_status_id'] == $mtpayment_order_pending_status_id) { ?>
                            <option value="<?php echo $order_status['order_status_id']; ?>"
                                    selected="selected"><?php echo $order_status['name']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $entry_order_success_status; ?></td>
                    <td>
                        <select name="mtpayment_order_success_status_id">
                            <?php foreach ($order_statuses as $order_status) { ?>
                            <?php if ($order_status['order_status_id'] == $mtpayment_order_success_status_id) { ?>
                            <option value="<?php echo $order_status['order_status_id']; ?>"
                                    selected="selected"><?php echo $order_status['name']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $entry_order_error_status; ?></td>
                    <td>
                        <select name="mtpayment_order_error_status_id">
                            <?php foreach ($order_statuses as $order_status) { ?>
                            <?php if ($order_status['order_status_id'] == $mtpayment_order_error_status_id) { ?>
                            <option value="<?php echo $order_status['order_status_id']; ?>"
                                    selected="selected"><?php echo $order_status['name']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $entry_geo_zone; ?></td>
                    <td>
                        <select name="mtpayment_geo_zone_id">
                            <option value="0"><?php echo $text_all_zones; ?></option>
                            <?php foreach ($geo_zones as $geo_zone) { ?>
                            <?php if ($geo_zone['geo_zone_id'] == $mtpayment_geo_zone_id) { ?>
                            <option value="<?php echo $geo_zone['geo_zone_id']; ?>"
                                    selected="selected"><?php echo $geo_zone['name']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $entry_status; ?></td>
                    <td>
                        <select name="mtpayment_status">
                            <?php if ($mtpayment_status) { ?>
                            <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                            <option value="0"><?php echo $text_disabled; ?></option>
                            <?php } else { ?>
                            <option value="1"><?php echo $text_enabled; ?></option>
                            <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $entry_sort_order; ?></td>
                    <td>
                        <input type="text" name="mtpayment_sort_order" value="<?php echo $mtpayment_sort_order; ?>"
                               placeholder="<?php echo $entry_sort_order; ?>"/>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<?php echo $footer; ?>
