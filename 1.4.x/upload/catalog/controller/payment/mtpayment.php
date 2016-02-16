<?php

/**
 * Class ControllerPaymentMTPayment
 */
class ControllerPaymentMTPayment extends Controller
{

    /**
     *
     */
    public function index()
    {
        $this->load->language('payment/mtpayment');

        $this->data['text_instruction'] = $this->language->get('text_instruction');
        $this->data['text_description'] = $this->language->get('text_description');
        $this->data['text_payment'] = $this->language->get('text_payment');

        $this->data['button_confirm'] = $this->language->get('button_confirm');

        $this->data['mtpayment_username'] = $this->config->get('mtpayment_username');
        $this->data['mtpayment_url_confirm'] = $this->url->https('payment/mtpayment/confirm');
        $this->data['mtpayment_url_history'] = $this->url->https('payment/mtpayment/history');

        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $customer_email = $this->customer->getEmail();

        if (isset($this->session->data['guest'])) {
            $customer_email = $this->session->data['guest']['email'];
        }

        $this->data['language_code'] = $this->language->get('code');
        $this->data['customer_email'] = $customer_email;
        $this->data['total'] = $this->currency->format(bcdiv($order_info['total'], 1, 2), '', '', false);
        $this->data['currency_code'] = $this->currency->getCode();
        $this->data['transaction_id'] = $this->session->data['order_id'] . '_' . time();

        $this->data['continue'] = $this->url->https('checkout/success');

        $this->id = 'payment';

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/mtpayment_payment.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/mtpayment_payment.tpl';
        } else {
            $this->template = 'default/template/payment/mtpayment_payment.tpl';
        }

        $this->render();
    }

    /**
     *
     */
    public function confirm()
    {
        $this->response->addHeader('Content-Type', 'application/json');

        $customer_email = $this->customer->getEmail();

        if (isset($this->session->data['guest'])) {
            $customer_email = $this->session->data['guest']['email'];
        }

        if (empty($customer_email)) {
            $this->response->setOutput(json_encode(array(
                'success' => false,
                'error' => 'Unknown customer',
            )));

            return;
        }

        $order_id = null;

        if (isset($this->session->data['order_id'])) {
            $order_id = $this->session->data['order_id'];
        }

        if (isset($this->request->get['order']) && $this->request->get['order'] != 'null') {
            $order_id = $this->request->get['order'];
        }

        $transaction = isset($this->request->get['transaction']) ? $this->request->get['transaction'] : null;
        $websocket = isset($this->request->get['websocket']) ? $this->request->get['websocket'] : null;
        $amount = isset($this->request->get['amount']) ? $this->request->get['amount'] : null;

        if (empty($transaction) || empty($websocket) || empty($amount)) {
            $this->response->setOutput(json_encode(array(
                'success' => false,
                'error' => 'Invalid parameters',
            )));

            return;
        }

        $this->load->model('checkout/order');

        $order_info = array();

        if (isset($order_id)) {
            $order_info = $this->model_checkout_order->getOrder($order_id);
        }

        if ($order_info['payment_code'] == 'mtpayment') {
            $this->load->language('payment/mtpayment');

            $this->load->model('payment/mtpayment');

            $this->model_payment_mtpayment->validateOrder($transaction, $amount, $websocket);

            $this->response->setOutput(json_encode(array(
                'success' => true,
                'order' => $order_info['order_id']
            )));

            // Clear cart related stuff
            if (isset($this->session->data['order_id'])) {
                $this->cart->clear();

                unset($this->session->data['shipping_method']);
                unset($this->session->data['shipping_methods']);
                unset($this->session->data['payment_method']);
                unset($this->session->data['payment_methods']);
                unset($this->session->data['comment']);
                unset($this->session->data['order_id']);
                unset($this->session->data['coupon']);
                unset($this->session->data['reward']);
                unset($this->session->data['voucher']);
                unset($this->session->data['vouchers']);
            }

            return;
        }

        $this->response->setOutput(json_encode(array(
            'success' => false,
            'error' => 'Invalid transaction'
        )));
    }

    /**
     *
     */
    public function history()
    {
        $this->load->language('account/invoice');
        $this->load->language('payment/mtpayment');

        $this->load->model('checkout/order');
        $this->load->model('payment/mtpayment');

        $order_id = isset($this->request->get['order']) ? $this->request->get['order'] : null;

        $this->data = array(
            'order_id' => $order_id,
            'histories' => $this->histories(array(), false)
        );

        $this->data['text_history'] = $this->language->get('text_history');

        $this->data['mtpayment_username'] = $this->config->get('mtpayment_username');
        $this->data['mtpayment_url_confirm'] = $this->url->https('payment/mtpayment/confirm');
        $this->data['mtpayment_url_history'] = $this->url->https('payment/mtpayment/history');
        $this->data['mtpayment_url_histories'] = $this->url->https('payment/mtpayment/histories');

        $this->document->title = $this->language->get('text_order');

        $this->data['heading_title'] = $this->language->get('text_order');

        $this->data['text_error'] = $this->language->get('text_error');

        $this->data['button_continue'] = $this->language->get('button_continue');

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->https('common/home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_account'),
            'href' => $this->url->https('account/account'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->https('account/order'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_order'),
            'href' => $this->url->https('payment/mtpayment/history', 'order_id=' . $order_id),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['continue'] = $this->url->https('account/order');

        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/footer',
            'common/header'
        );

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/mtpayment_history.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/mtpayment_history.tpl';
        } else {
            $this->template = 'default/template/payment/mtpayment_history.tpl';
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    /**
     * @param array $params
     * @param bool|true $json
     * @return null|string
     */
    public function histories($params = array(), $json = true)
    {
        $this->load->language('account/invoice');
        $this->load->language('payment/mtpayment');

        $html = '';

        $this->data = array(
            'histories' => array()
        );

        $order_id = isset($this->request->get['order']) ? $this->request->get['order'] : null;

        if (isset($order_id)) {
            $this->load->model('checkout/order');
            $this->load->model('account/order');

            $order_info = $this->model_checkout_order->getOrder($order_id);

            $customer_email = $this->customer->getEmail();

            if (isset($this->session->data['guest'])) {
                $customer_email = $this->session->data['guest']['email'];
            }

            $websocket_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mttransactions WHERE `order` = '" . (int)$order_info['order_id'] . "'");

            if ($websocket_query->num_rows) {
                $websocket_id = $websocket_query->row['websocket'];
            } else {
                $websocket_id = null;
            }

            $this->data['order_id'] = $order_info['order_id'];
            $this->data['language_code'] = $this->language->get('code');
            $this->data['customer_email'] = $customer_email;
            $this->data['total'] = $this->currency->format(bcdiv($order_info['total'], 1, 2), '', '', false);
            $this->data['currency_code'] = $this->currency->getCode();
            $this->data['transaction_id'] = $order_info['order_id'] . '_' . time();
            $this->data['websocket_id'] = $websocket_id;

            $this->data['text_email_message'] = $this->language->get('text_email_message');
            $this->data['text_click_here'] = $this->language->get('text_click_here');

            $this->data['column_date_added'] = $this->language->get('column_date_added');
            $this->data['column_status'] = $this->language->get('column_status');
            $this->data['column_comment'] = $this->language->get('column_comment');

            $order_pending_status_id = $this->config->get('mtpayment_order_pending_status_id');

            $order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_pending_status_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

            if ($order_status_query->num_rows) {
                $order_pending_status = $order_status_query->row['name'];
            } else {
                $order_pending_status = '';
            }

            $this->data['order_pending_status'] = $order_pending_status;

            $results = $this->model_account_order->getOrderHistorys($order_id);

            $allow_different_payment = true;

            foreach ($results as $result) {
                $this->data['histories'][] = array(
                    'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                    'status' => $result['status'],
                    'comment' => $result['notify'] ? nl2br($result['comment']) : ''
                );

                if ($result['status'] != $order_pending_status) {
                    $allow_different_payment = false;
                }
            }

            $this->data['allow_different_payment'] = $allow_different_payment;

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/mtpayment_histories.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/payment/mtpayment_histories.tpl';
            } else {
                $this->template = 'default/template/payment/mtpayment_histories.tpl';
            }

            $html = $this->render(true);
        }

        if ($json) {
            $this->response->setOutput(json_encode(array(
                'success' => true,
                'html_table_order_histories' => $html
            )));

            return null;
        }

        return $html;
    }

    /**
     *
     */
    public function callback()
    {
        $this->load->model('payment/mtpayment');

        $hash = isset($this->request->post['hash']) ? $this->request->post['hash'] : false;

        if ($hash !== false) {
            $data = json_decode(
                $this->model_payment_mtpayment->decrypt($hash, $this->config->get('mtpayment_secret_key'))
            );
            $data->custom = isset($data->custom) ? json_decode($data->custom) : null;

            if (!isset($data->custom) && !isset($data->custom->description)) {
                die();
            }

            $transaction = explode('_', $data->custom->description);

            if (count($transaction) != 2) {
                die();
            }

            if ($this->model_payment_mtpayment->existsCallback($data->callback_uuid)) {
                die('OK');
            }

            try {
                $transaction_id = implode('_', $transaction);

                $success = $this->model_payment_mtpayment->closeOrder(
                    $transaction_id,
                    $data->custom->data->amount
                );
            } catch (Exception $e) {
                die();
            }

            if ($success) {
                $this->model_payment_mtpayment->insertCallback($data);
                die('OK');
            }
        }

        die();
    }
}