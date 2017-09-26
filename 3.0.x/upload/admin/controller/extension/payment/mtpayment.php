<?php

/**
 * Class ControllerExtensionPaymentMTPayment
 */
class ControllerExtensionPaymentMTPayment extends Controller
{

    /**
     * @var array
     */
    private $error = array();

    /**
     *
     */
    public function install()
    {
        $this->load->model('extension/payment/mtpayment');
        $this->model_extension_payment_mtpayment->install();
    }

    /**
     *
     */
    public function uninstall()
    {
        $this->load->model('extension/payment/mtpayment');
        $this->model_extension_payment_mtpayment->uninstall();
    }

    /**
     *
     */
    public function index()
    {
        $this->load->language('extension/payment/mtpayment');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');
        $this->load->model('extension/payment/mtpayment');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('mtpayment', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');

        $data['entry_username'] = $this->language->get('entry_username');
        $data['entry_secret_key'] = $this->language->get('entry_secret_key');
        $data['entry_callback_url'] = $this->language->get('entry_callback_url');
        $data['entry_standard_redirect'] = $this->language->get('entry_standard_redirect');
        $data['entry_total'] = $this->language->get('entry_total');
        $data['entry_order_pending_status'] = $this->language->get('entry_order_pending_status');
        $data['entry_order_success_status'] = $this->language->get('entry_order_success_status');
        $data['entry_order_error_status'] = $this->language->get('entry_order_error_status');
        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');

        $data['help_username'] = $this->language->get('help_username');
        $data['help_secret_key'] = $this->language->get('help_secret_key');
        $data['help_callback_url'] = $this->language->get('help_callback_url');
        $data['help_total'] = $this->language->get('help_total');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $this->load->model('localisation/language');

        if (isset($this->error['username'])) {
            $data['error_username'] = $this->error['username'];
        } else {
            $data['error_username'] = '';
        }

        if (isset($this->error['secret_key'])) {
            $data['error_secret_key'] = $this->error['secret_key'];
        } else {
            $data['error_secret_key'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => '/index.php?route=extension/payment/mtpayment&token=' . $this->session->data['token']
        );

        $data['action'] = $this->url->link('extension/payment/mtpayment', 'token=' . $this->session->data['token'], 'SSL');

        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL');

        $this->load->model('localisation/language');

        if (isset($this->request->post['mt_username'])) {
            $data['mtpayment_username'] = $this->request->post['mtpayment_username'];
        } else {
            $data['mtpayment_username'] = $this->config->get('mtpayment_username');
        }

        if (isset($this->request->post['mtpayment_secret_key'])) {
            $data['mtpayment_secret_key'] = $this->request->post['mtpayment_secret_key'];
        } else {
            $data['mtpayment_secret_key'] = $this->config->get('mtpayment_secret_key');
        }

        if (isset($this->request->post['mtpayment_callback_url'])) {
            $data['mtpayment_callback_url'] = $this->request->post['mtpayment_callback_url'];
        } else {
            $data['mtpayment_callback_url'] = $this->config->get('mtpayment_callback_url');
        }

        if (isset($this->request->post['mtpayment_standard_redirect'])) {
            $data['mtpayment_standard_redirect'] = $this->request->post['mtpayment_standard_redirect'];
        } else {
            $data['mtpayment_standard_redirect'] = $this->config->get('mtpayment_standard_redirect');
        }

        if (isset($this->request->post['mtpayment_total'])) {
            $data['mtpayment_total'] = $this->request->post['mtpayment_total'];
        } else {
            $data['mtpayment_total'] = $this->config->get('mtpayment_total');
        }

        if (isset($this->request->post['mtpayment_order_pending_status_id'])) {
            $data['mtpayment_order_pending_status_id'] = $this->request->post['mtpayment_order_pending_status_id'];
        } else {
            $data['mtpayment_order_pending_status_id'] = $this->config->get('mtpayment_order_pending_status_id');
        }

        if (isset($this->request->post['mtpayment_order_success_status_id'])) {
            $data['mtpayment_order_success_status_id'] = $this->request->post['mtpayment_order_success_status_id'];
        } else {
            $data['mtpayment_order_success_status_id'] = $this->config->get('mtpayment_order_success_status_id');
        }

        if (isset($this->request->post['mtpayment_order_error_status_id'])) {
            $data['mtpayment_order_error_status_id'] = $this->request->post['mtpayment_order_error_status_id'];
        } else {
            $data['mtpayment_order_error_status_id'] = $this->config->get('mtpayment_order_error_status_id');
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['mtpayment_geo_zone_id'])) {
            $data['mtpayment_geo_zone_id'] = $this->request->post['mtpayment_geo_zone_id'];
        } else {
            $data['mtpayment_geo_zone_id'] = $this->config->get('mtpayment_geo_zone_id');
        }

        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['mtpayment_status'])) {
            $data['mtpayment_status'] = $this->request->post['mtpayment_status'];
        } else {
            $data['mtpayment_status'] = $this->config->get('mtpayment_status');
        }

        if (isset($this->request->post['mtpayment_sort_order'])) {
            $data['mtpayment_sort_order'] = $this->request->post['mtpayment_sort_order'];
        } else {
            $data['mtpayment_sort_order'] = $this->config->get('mtpayment_sort_order');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/mtpayment.tpl', $data));
    }

    /**
     * @return bool
     */
    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/payment/mtpayment')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (empty($this->request->post['mtpayment_username'])) {
            $this->error['username'] = $this->language->get('error_username');
        }

        if (empty($this->request->post['mtpayment_secret_key'])) {
            $this->error['secret_key'] = $this->language->get('error_secret_key');
        }

        return !$this->error;
    }
}
