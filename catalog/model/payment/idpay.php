<?php 

class ModelPaymentIDPay extends Model
{
	public function getMethod($address)
	{
		$this->load->language('payment/idpay');

		if ($this->config->get('idpay_status')) {

			$status = true;

		} else {

			$status = false;
		}

		$method_data = array ();

		if ($status) {

			$method_data = array (
        		'code'       => 'idpay',
        		'title'      => $this->language->get('text_title'),
				'terms'      => null,
				'sort_order' => $this->config->get('idpay_sort_order')
			);
		}

		return $method_data;
	}
}
