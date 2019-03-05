<?php 

class ControllerExtensionPaymentIDPay extends Controller
{
	private $error = array ();

	public function index()
	{
		$this->load->language('extension/payment/idpay');
		$this->load->model('setting/setting');

		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {

			$this->model_setting_setting->editSetting('idpay', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_authorization'] = $this->language->get('text_authorization');
		$data['text_sale'] = $this->language->get('text_sale');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_success_message'] = $this->language->get('text_success_message');
        $data['text_failed_message'] = $this->language->get('text_failed_message');
        $data['text_sort_order'] = $this->language->get('text_sort_order');

        $data['text_sandbox'] = $this->language->get('text_sandbox');
        $data['text_sandbox_help'] = $this->language->get('text_sandbox_help');
        $data['text_api_key'] = $this->language->get('text_api_key');
        $data['text_status'] = $this->language->get('text_status');
        $data['text_order_status'] = $this->language->get('text_order_status');
        $data['text_order_status'] = $this->language->get('text_order_status');

        $data['entry_payment_successful_message_default'] = $this->language->get('entry_payment_successful_message_default');
        $data['entry_payment_failed_message_default'] = $this->language->get('entry_payment_failed_message_default');
        $data['entry_sandbox_yes'] = $this->language->get('entry_sandbox_yes');
        $data['entry_sandbox_no'] = $this->language->get('entry_sandbox_no');
        $data['text_successful_message_help'] = $this->language->get('text_successful_message_help');
        $data['text_failed_message_help'] = $this->language->get('text_failed_message_help');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

        $data['tab_general'] = $this->language->get('tab_general');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array (

			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array (

			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array (

			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/idpay', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('extension/payment/idpay', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->error['warning'])) {

			$data['error_warning'] = $this->error['warning'];

		} else {

			$data['error_warning'] = false;
		}

		if (isset($this->error['api_key'])) {

			$data['error_api_key'] = $this->error['api_key'];

		} else {

			$data['error_api'] = false;
		}

		if (isset($this->request->post['api_key'])) {

			$data['idpay_api_key'] = $this->request->post['idpay_api_key'];

		} else {

			$data['idpay_api_key'] = $this->config->get('idpay_api_key');
		}

        if (isset($this->request->post['idpay_sandbox'])) {

            $data['idpay_sandbox'] = $this->request->post['idpay_sandbox'];

        } else {

            $data['idpay_sandbox'] = $this->config->get('idpay_sandbox');
        }

		if (isset($this->request->post['idpay_order_status_id'])) {

			$data['idpay_order_status_id'] = $this->request->post['idpay_order_status_id'];

		} else {

			$data['idpay_order_status_id'] = $this->config->get('idpay_order_status_id');
		}

        if (isset($this->request->post['idpay_payment_successful_message'])) {

            $data['idpay_payment_successful_message'] = trim($this->request->post['idpay_payment_successful_message']);

        } else {

            $data['idpay_payment_successful_message'] = trim($this->config->get('idpay_payment_successful_message'));
        }

        if (isset($this->request->post['idpay_payment_failed_message'])) {

            $data['idpay_payment_failed_message'] = trim($this->request->post['idpay_payment_failed_message']);

        } else {

            $data['idpay_payment_failed_message'] = trim($this->config->get('idpay_payment_failed_message'));
        }

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['idpay_status'])) {

			$data['idpay_status'] = $this->request->post['idpay_status'];

		} else {

			$data['idpay_status'] = $this->config->get('idpay_status');
		}

		if (isset($this->request->post['idpay_sort_order'])) {

			$data['idpay_sort_order'] = $this->request->post['idpay_sort_order'];

		} else {

			$data['idpay_sort_order'] = $this->config->get('idpay_sort_order');
		}

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/idpay.tpl', $data));
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'extension/payment/idpay')) {

			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['idpay_api_key']) {

			$this->error['warning'] = $this->language->get('error_validate');
			$this->error['api_key'] = $this->language->get('error_api_key');
		}

		if (!$this->error) {

			return true;

		} else {

			return false;
		}
	}
}