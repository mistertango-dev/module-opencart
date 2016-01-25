<?php if ($histories) { ?>
<table id="mtpayment-information-order-histories" class="list">
    <thead>
    <tr>
        <td class="text-left"><?php echo $column_date_added; ?></td>
        <td class="text-left"><?php echo $column_status; ?></td>
        <td class="text-left"><?php echo $column_comment; ?></td>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($histories as $history) { ?>
    <tr>
        <td class="text-left"><?php echo $history['date_added']; ?></td>
        <td class="text-left">
            <?php echo $history['status']; ?>
            <?php if ($history['status'] == $order_pending_status && $allow_different_payment): ?>
            <p class="jsAllowDifferentPayment">
                <?php echo $text_email_message; ?>
                <a href="#"
                   class="mtpayment-button-pay"
                   data-ws-id="<?php echo $websocket_id; ?>"
                   data-language="<?php echo $language_code; ?>"
                   data-customer="<?php echo $customer_email; ?>"
                   data-order="<?php echo $order_id; ?>"
                   data-amount="<?php echo $total; ?>"
                   data-currency="<?php echo $currency_code; ?>"
                   data-transaction="<?php echo $transaction_id; ?>">
                    <?php echo $text_click_here; ?>
                </a>
            </p>
            <?php endif; ?>
        </td>
        <td class="text-left"><?php echo $history['comment']; ?></td>
    </tr>
    <?php } ?>
    </tbody>
</table>
<?php } else { ?>
<p>Wrong order</p>
<?php } ?>
