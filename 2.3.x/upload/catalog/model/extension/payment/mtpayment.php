<?php

/**
 * Class ModelExtensionPaymentMTPayment
 */
class ModelExtensionPaymentMTPayment extends Model
{

    /**
     * @param $address
     * @param $total
     * @return array
     */
    public function getMethod($address, $total)
    {
        $this->load->language('extension/payment/mtpayment');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('mtpayment_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if ($this->config->get('mtpayment_total') > 0 && $this->config->get('mtpayment_total') > $total) {
            $status = false;
        } elseif (!$this->config->get('mtpayment_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code' => 'mtpayment',
                'title' => $this->language->get('text_title'),
                'terms' => '',
                'sort_order' => $this->config->get('mtpayment_sort_order')
            );
        }

        return $method_data;
    }

    /**
     * @param $callback
     * @return bool
     */
    public function existsCallback($callback)
    {
        $exists = $this->db->query(
            'SELECT 1 FROM `' . DB_PREFIX . 'mtcallbacks`
            WHERE `callback` = \'' . $this->db->escape($callback) . '\''
        );

        $exists = is_array($exists->row) ? reset($exists->row) : null;

        return $exists ? true : false;
    }

    /**
     * @param $data
     */
    public function insertCallback($data)
    {
        $this->db->query(
            "INSERT INTO `" . DB_PREFIX . "mtcallbacks`
            (
                `callback`,
                `transaction`,
                `amount`
            )
            VALUES
            (
                '" . $this->db->escape($data->callback_uuid) . "',
                '" . $this->db->escape($data->custom->description) . "',
                '" . $this->db->escape($data->custom->data->amount) . "'
            )"
        );
    }

    /**
     * @param $transaction_id
     * @param $amount
     * @return bool
     */
    public function closeOrder($transaction_id, $amount)
    {
        $order_id = $this->db->query(
            'SELECT `order`
			FROM `' . DB_PREFIX . 'mttransactions`
			WHERE `transaction` = \'' . $this->db->escape($transaction_id) . '\''
        );

        $order_id = is_array($order_id->row) ? reset($order_id->row) : null;

        if (empty($order_id)) {
            $this->validateOrder($transaction_id, $amount);

            $order_id = $this->db->query(
                'SELECT `order`
                FROM `' . DB_PREFIX . 'mttransactions`
                WHERE `transaction` = \'' . $this->db->escape($transaction_id) . '\''
            );

            $order_id = is_array($order_id->row) ? reset($order_id->row) : null;
        }

        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($order_id);

        if (empty($order_info)) {
            //@todo: exception should be logged.

            return false;
        }

        $order_status_id = $this->config->get('mtpayment_order_success_status_id');

        $order_total = preg_replace('/[^0-9.]/', '', trim(strip_tags($order_info['total'])));

        if (bcdiv($order_total, 1, 2) != bcdiv($amount, 1, 2)) {
            $order_status_id = $this->config->get('mtpayment_order_error_status_id');
        }

        $this->load->language('extension/payment/mtpayment');

        $comment = $this->language->get('text_amount_received') . ': ' . $amount;

        $this->model_checkout_order->addOrderHistory(
            $order_info['order_id'],
            $order_status_id,
            $comment,
            true
        );

        return true;
    }

    /**
     * @param $transaction_id
     * @param $amount
     * @param null $websocket_id
     * @return bool
     */
    public function validateOrder($transaction_id, $amount, $websocket_id = null)
    {
        $transaction = explode('_', $transaction_id);

        if (count($transaction) == 2) {
            $this->load->language('extension/payment/mtpayment');
            $this->load->model('account/order');
            $this->load->model('checkout/order');

            $order_id = $transaction[0];
            $order_histories = $this->model_account_order->getOrderHistories($order_id);

            if (empty($order_histories)) {
                $comment = $this->language->get('text_instruction') . "\n\n";
                $comment .= $this->language->get('text_payment');

                $this->model_checkout_order->addOrderHistory(
                    $order_id,
                    $this->config->get('mtpayment_order_pending_status_id'),
                    $comment,
                    true
                );
            }

            $this->insertTransaction($transaction_id, $websocket_id, $order_id, $amount);

            return true;
        }

        return false;
    }

    /**
     * @param $transaction
     * @param $websocket
     * @param $order
     * @param $amount
     */
    public function insertTransaction($transaction, $websocket, $order, $amount)
    {
        $this->db->query(
            "INSERT INTO " . DB_PREFIX . "mttransactions
            (
                `transaction`,
                `websocket`,
                `order`,
                `amount`
            )
            VALUES
            (
                '" . $this->db->escape($transaction) . "',
                '" . $this->db->escape($websocket) . "',
                '" . $this->db->escape($order) . "',
                '" . $this->db->escape($amount) . "'
            )"
        );
    }

    /**
     * @param $encoded_text
     * @param $key
     * @return string
     */
    public function decrypt($encoded_text, $key)
    {
        if (strlen($key) == 30)
            $key .= "\0\0";

        $encoded_text = trim($encoded_text);
        $ciphertext_dec = base64_decode($encoded_text);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $iv_dec = substr($ciphertext_dec, 0, $iv_size);

        $ciphertext_dec = substr($ciphertext_dec, $iv_size);
        $sResult = @mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);

        return trim($sResult);
    }
}
