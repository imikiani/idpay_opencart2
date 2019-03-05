<?php

class ControllerExtensionPaymentIDPay extends Controller
{
	public function index()
	{

        $this->load->language('payment/idpay');
        $this->load->model('checkout/order');

        /** @var \ModelCheckoutOrder $model */
        $model = $this->model_checkout_order;

        $order_info = $model->getOrder($this->session->data['order_id']);

        $encryption = new Encryption($this->config->get('config_encryption'));
        $sandbox = $this->config->get('idpay_sandbox') == 'yes' ? 'true' : 'false';

        $amount = $this->correctAmount($order_info);

        $data['text_wait'] = $this->language->get('text_wait');

        $data['button_confirm'] = $this->language->get('button_confirm');

        $data['error_warning'] = false;

		if (extension_loaded('curl')) {

			$api = $this->config->get('idpay_api_key');
			//$callback = $this->url->link('extension/payment/idpay/callback', 'order_id=' . $encryption->encrypt($order_info['order_id']), '', 'SSL');
			$callback = $this->url->link('extension/payment/idpay/callback', 'order_id=' . $order_info['order_id']);

			$order_id = $order_info['order_id'];
			$desc = 'پرداخت سفارش ' . $order_info['order_id'];

            // Customer information
            $name = $order_info['firstname'] . ' ' . $order_info['lastname'];
            $mail = $order_info['email'];
            $phone = $order_info['telephone'];

            $params = array(
                'order_id' => $order_id,
                'amount' => $amount,
                'name' => $name,
                'phone' => $phone,
                'mail' => $mail,
                'desc' => $desc,
                'callback' =>  $callback,
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.idpay.ir/v1.1/payment');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'X-API-KEY: ' . $api,
                'X-SANDBOX: ' . $sandbox,
            ));

            $result = curl_exec($ch);
            $result = json_decode($result);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);


            if ($http_status != 201 || empty($result) || empty($result->id) || empty($result->link)) {
                //$data['error_warning'] = sprintf($this->language->get('error_create_payment'), $http_status, $result->error_code, $result->error_message);
                $data['error_warning'] = $result->error_message;
            }

            else {
                // Add some histories to the order with order status 1 (Pending);
                $comment1 = sprintf($this->language->get('text_transaction_id'), $result->id);
                $model->addOrderHistory($order_id, 1, $comment1, false);

                $comment2 = $this->language->get('text_redirecting');
                $model->addOrderHistory($order_id, 1, $comment2, false);

                $data['action'] = $result->link;
            }

		} else {
			$data['error_warning'] = $this->language->get('error_curl');
		}

		return $this->load->view('/extension/payment/idpay.tpl', $data);

	}

	public function callback()
	{
		$this->load->language('payment/idpay');
		$this->load->model('checkout/order');

		/** @var \ModelCheckoutOrder $model */
        $model = $this->model_checkout_order;

		$this->document->setTitle($this->language->get('heading_title'));
        $sandbox = $this->config->get('idpay_sandbox') == 'yes' ? 'true' : 'false';

		$encryption = new Encryption($this->config->get('config_encryption'));

		$order_id = isset($this->session->data['order_id']) ? $this->session->data['order_id'] : false;
		$order_id = isset($order_id) ? $order_id : $encryption->decrypt($this->request->get['order_id']);

		$order_info = $model->getOrder($order_id);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['button_continue'] = $this->language->get('button_continue');
		$data['continue']        = $this->url->link('common/home', '', 'SSL');

		$data['error_warning'] = '';


        $status = empty($this->request->post['status']) ? NULL : $this->request->post['status'];
        $track_id = empty($this->request->post['track_id']) ? NULL : $this->request->post['track_id'];
        $id = empty($this->request->post['id']) ? NULL : $this->request->post['id'];
        $order_id = empty($this->request->post['order_id']) ? NULL : $this->request->post['order_id'];
        //$amount = empty($this->request->post['amount']) ? NULL : $this->request->post['amount'];
        $card_no = empty($this->request->post['card_no']) ? NULL : $this->request->post['card_no'];
        $date = empty($this->request->post['date']) ? NULL : $this->request->post['date'];


        if (!$order_info) {
            $comment = $this->idpay_get_failed_message($track_id, $order_id);
            // Set Order status id to 10 (Failed) and add a history.
            $model->addOrderHistory($order_id, 10, $comment, true);
            $data['error_warning'] = $comment;
            $data['button_continue'] = $this->language->get('button_view_cart');
            $data['continue'] = $this->url->link('checkout/cart');

        } else {

            if($status != 10) {
                $comment = $this->idpay_get_failed_message($track_id, $order_id);
                // Set Order status id to 10 (Failed) and add a history.
                $model->addOrderHistory($order_id, 10, $comment, true);
                $data['error_warning'] = $comment;
                $data['button_continue'] = $this->language->get('button_view_cart');
                $data['continue'] = $this->url->link('checkout/cart');
            }
            else {
                $amount = $this->correctAmount($order_info);
                $api_key = $this->config->get('idpay_api_key');
                $idpay_data = array(
                    'id' => $id,
                    'order_id' => $order_id,
                );
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://api.idpay.ir/v1.1/payment/verify');
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($idpay_data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'X-API-KEY:' . $api_key,
                    'X-SANDBOX: ' . $sandbox,
                ));
                $result = curl_exec($ch);
                $result = json_decode($result);

                $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if ($http_status != 200) {
                    $comment = sprintf($this->language->get('error_verify_payment'), $http_status, $result->error_code, $result->error_message);
                    // Set Order status id to 10 (Failed) and add a history.
                    $model->addOrderHistory($order_id, 10, $comment, true);
                    $data['error_warning'] = $comment;
                    $data['button_continue'] = $this->language->get('button_view_cart');
                    $data['continue'] = $this->url->link('checkout/cart');
                }
                else {
                    $verify_status = empty($result->status) ? NULL : $result->status;
                    $verify_track_id = empty($result->track_id) ? NULL : $result->track_id;
                    $verify_order_id = empty($result->order_id) ? NULL : $result->order_id;
                    $verify_amount = empty($result->amount) ? NULL : $result->amount;
                    if (empty($verify_status) || empty($verify_track_id) || empty($verify_amount) || $verify_amount != $amount || $verify_status < 100) {
                        $comment = $this->idpay_get_failed_message($verify_track_id, $verify_order_id);
                        // Set Order status id to 10 (Failed) and add a history.
                        $model->addOrderHistory($order_id, 10, $comment, true);
                        $data['error_warning'] = $comment;
                        $data['button_continue'] = $this->language->get('button_view_cart');
                        $data['continue'] = $this->url->link('checkout/cart');
                    } else { // Transaction is successful.
                        $comment = $this->idpay_get_success_message($verify_track_id, $verify_order_id);
                        $config_successful_payment_status = $this->config->get('idpay_order_status_id');
                        // Set Order status id to the configured status id and add a history.
                        $model->addOrderHistory($verify_order_id, $config_successful_payment_status , $comment, true);
                        // Add another history.
                        $comment2 = 'Status: ' . $result->status .' - Track id: ' . $result->track_id . ' - Card no: ' . $result->payment->card_no;
                        $model->addOrderHistory($verify_order_id, $config_successful_payment_status , $comment2, true);
                        $data['payment_result'] = $comment;
                        $data['button_continue'] = $this->language->get('button_complete');
                        $data['continue'] = $this->url->link('checkout/success');
                    }
                }
            }
        }

        // Breadcrumbs
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', '', true)
        );

        if ($data['error_warning']) {
            $data['breadcrumbs'][] = array(

                'text' => $this->language->get( 'text_basket' ),
                'href' => $this->url->link( 'checkout/cart', '', 'SSL' )
            );

            $data['breadcrumbs'][] = array(

                'text' => $this->language->get( 'text_checkout' ),
                'href' => $this->url->link( 'checkout/checkout', '', 'SSL' )
            );
        }

        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('/extension/payment/idpay_callback.tpl', $data));
	}

    private function idpay_get_success_message($track_id, $order_id)
    {
        return str_replace(["{track_id}", "{order_id}"], [$track_id, $order_id], $this->config->get('idpay_payment_successful_message'));
    }
    private function idpay_get_failed_message($track_id, $order_id)
    {
        return str_replace(["{track_id}", "{order_id}"], [$track_id, $order_id], $this->config->get('idpay_payment_failed_message'));

    }
    private function correctAmount($order_info)
    {
        $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $amount = round($amount);
        $amount = $this->currency->convert($amount, $order_info['currency_code'], "RLS");
        return (int)$amount;
    }
}

