<?php

/**
 * Class ControllerPaymentMTPayment
 */
class ControllerPaymentMTPayment extends Controller
{

    /**
     * @return mixed
     */
    public function index()
    {
        $this->load->language('payment/mtpayment');

        $data['text_instruction'] = $this->language->get('text_instruction');
        $data['text_description'] = $this->language->get('text_description');
        $data['text_payment'] = $this->language->get('text_payment');

        $data['button_confirm'] = $this->language->get('button_confirm');

        $data['mtpayment_username'] = $this->config->get('mtpayment_username');
        $data['mtpayment_standard_redirect'] = $this->config->get('mtpayment_standard_redirect');
	    $data['mtpayment_url_data'] = '/index.php?route=payment/mtpayment/data';
	    $data['mtpayment_url_confirm'] = '/index.php?route=payment/mtpayment/confirm';
	    $data['mtpayment_url_history'] = '/index.php?route=payment/mtpayment/history';

        $data['continue'] = $this->url->link('checkout/success');

        return $this->load->view('payment/mtpayment_payment', $data);
    }

    /**
     *
     */
    public function data()
    {
        $this->response->addHeader('Content-Type: application/json');

        $customer_email = $this->customer->getEmail();
        if (
            empty($customer_email)
            && isset($this->session->data['guest'])
            && isset($this->session->data['guest']['email'])
            && !empty($this->session->data['guest']['email'])
        ) {
            $customer_email = $this->session->data['guest']['email'];
        }

        $order_id = null;
        if (!empty($this->request->get['order']) && $this->request->get['order'] != 'null') {
            $order_id = $this->request->get['order'];
        } elseif (!empty($this->session->data['order_id'])) {
            $order_id = $this->session->data['order_id'];
        }

        if (empty($order_id)) {
            $this->response->setOutput(json_encode(array(
                'success' => false,
                'error' => 'Order is not present',
            )));

            return;
        }

        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($order_id);

        if (empty($customer_email) && isset($order_info['email'])) {
            $customer_email = $order_info['email'];
        }

        if (empty($customer_email)) {
            $this->response->setOutput(json_encode(array(
                'success' => false,
                'error' => 'Unknown customer',
            )));

            return;
        }

        $websocket_query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "mttransactions WHERE `order` = '" . (int)$order_info['order_id'] . "'"
        );

        if ($websocket_query->num_rows) {
            $websocket_id = $websocket_query->row['websocket'];
        } else {
            $websocket_id = null;
        }

        $currency = $this->session->data['currency'];
        $this->response->setOutput(json_encode(array(
            'success' => true,
            'websocket' => $websocket_id,
            'transaction' => $this->session->data['order_id'] . '_' . time(),
            'customer' => $customer_email,
            'amount' => bcdiv(trim(strip_tags($order_info['total'])), 1, 2),
            'currency' => $currency,
            'language' => $this->language->get('code'),
        )));
    }

    /**
     *
     */
    public function confirm()
    {
        $this->response->addHeader('Content-Type: application/json');

        $order_id = null;

        if (!empty($this->session->data['order_id'])) {
            $order_id = $this->session->data['order_id'];
        }

        if (!empty($this->request->get['order'])) {
            $order_id = $this->request->get['order'];
        }

        $transaction = isset($this->request->get['transaction']) ? $this->request->get['transaction'] : null;
        $websocket = isset($this->request->get['websocket']) ? $this->request->get['websocket'] : null;
        $amount = isset($this->request->get['amount']) ? $this->request->get['amount'] : null;
        $offline = isset($this->request->get['offline']) ? $this->request->get['offline'] : false;

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

            // Clear cart related stuff if needed
            if (
                !$this->config->get('mtpayment_standard_redirect')
                && !$offline
                && isset($this->session->data['order_id'])
            ) {
                $this->cart->clear();

                // Add to activity log
                $this->load->model('account/activity');

                if ($this->customer->isLogged()) {
                    $activity_data = array(
                        'customer_id' => $this->customer->getId(),
                        'name' => $this->customer->getFirstName() . ' ' . $this->customer->getLastName(),
                        'order_id' => $this->session->data['order_id']
                    );

                    $this->model_account_activity->addActivity('order_account', $activity_data);
                } else {
                    $activity_data = array(
                        'name' => $this->session->data['guest']['firstname'] . ' ' . $this->session->data['guest']['lastname'],
                        'order_id' => $this->session->data['order_id']
                    );

                    $this->model_account_activity->addActivity('order_guest', $activity_data);
                }

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
                unset($this->session->data['totals']);
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
        $this->load->language('account/order');
        $this->load->language('payment/mtpayment');

        $this->load->model('checkout/order');
        $this->load->model('payment/mtpayment');

        $order_id = isset($this->request->get['order']) ? $this->request->get['order'] : null;

        $data = array(
            'order_id' => $order_id,
            'histories' => $this->histories(array(), false)
        );

        $data['text_history'] = $this->language->get('text_history');

        $data['mtpayment_username'] = $this->config->get('mtpayment_username');
        $data['mtpayment_standard_redirect'] = $this->config->get('mtpayment_standard_redirect');
        $data['mtpayment_url_data'] = '/index.php?route=payment/mtpayment/data';
	    $data['mtpayment_url_confirm'] = '/index.php?route=payment/mtpayment/confirm';
	    $data['mtpayment_url_history'] = '/index.php?route=payment/mtpayment/history';
        $data['mtpayment_url_histories'] = '/index.php?route=payment/mtpayment/histories';

        $this->document->setTitle($this->language->get('text_order'));

        $data['heading_title'] = $this->language->get('text_order');

        $data['text_error'] = $this->language->get('text_error');

        $data['button_continue'] = $this->language->get('button_continue');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_account'),
            'href' => $this->url->link('account/account', '', 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('account/order', '', 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_order'),
            'href' => '/index.php?route=payment/mtpayment/history&order_id=' . $order_id
        );

        $data['continue'] = '/index.php?route=payment/mtpayment/history';
        if ($this->config->get('mtpayment_standard_redirect') && isset($this->session->data['order_id'])) {
            $data['continue'] = $this->url->link('checkout/success');
        }

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('payment/mtpayment_history', $data));
    }

    /**
     * @param array $params
     * @param bool|true $json
     * @return null|string
     */
    public function histories($params = array(), $json = true)
    {
        $this->load->language('account/order');
        $this->load->language('payment/mtpayment');

        $html = '';

        $data = array(
            'histories' => array()
        );

        $order_id = isset($this->request->get['order']) ? $this->request->get['order'] : null;

        if (isset($order_id)) {
            $this->load->model('account/order');

            $data['text_email_message'] = $this->language->get('text_email_message');
            $data['text_click_here'] = $this->language->get('text_click_here');

            $data['column_date_added'] = $this->language->get('column_date_added');
            $data['column_status'] = $this->language->get('column_status');
            $data['column_comment'] = $this->language->get('column_comment');

            $order_pending_status_id = $this->config->get('mtpayment_order_pending_status_id');

            $order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_pending_status_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

            if ($order_status_query->num_rows) {
                $order_pending_status = $order_status_query->row['name'];
            } else {
                $order_pending_status = '';
            }

            $data['order_id'] = $order_id;
            $data['order_pending_status'] = $order_pending_status;

            $results = $this->model_account_order->getOrderHistories($order_id);

            $allow_different_payment = true;

            foreach ($results as $result) {
                $data['histories'][] = array(
                    'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                    'status' => $result['status'],
                    'comment' => $result['notify'] ? nl2br($result['comment']) : ''
                );

                if ($result['status'] != $order_pending_status) {
                    $allow_different_payment = false;
                }
            }

            $data['allow_different_payment'] = $allow_different_payment;

            $html = $this->load->view('payment/mtpayment_histories', $data);
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
