<?php

class ControllerKlarnaAccount extends Controller {

	public function index() {

		$json = array();

		if (!$this->customer->isLogged()) {

			$this->load->language('klarna/checkout');
			$this->load->model('account/customer');

			$email 		= isset($this->request->post['email']) 		? $this->request->post['email'] 		: NULL;
			$postcode 	= isset($this->request->post['postcode']) 	? $this->request->post['postcode'] 		: NULL;
			$password 	= isset($this->request->post['account']) 	? $this->request->post['account'] 		: NULL;

			if 	   ((utf8_strlen($email) > 96) || !filter_var($email, FILTER_VALIDATE_EMAIL)) 	{ $json['error'] = $this->language->get('error_account_email'); }
			elseif ($this->model_account_customer->getTotalCustomersByEmail($email)) 			{ $json['error'] = $this->language->get('error_account_exists'); }
			elseif ((utf8_strlen(trim($postcode )) < 4 || utf8_strlen(trim($postcode )) > 5)) 	{ $json['error'] = $this->language->get('error_account_postcode'); }
			elseif ((utf8_strlen($password) < 4) || (utf8_strlen($password) > 20)) 				{ $json['error'] = $this->language->get('error_account_password'); }

			if (!isset($json['error'])) {

				$customer = array(
					'customer_group_id'	=> $this->config->get('config_customer_group_id'),
					'firstname'			=> 'N/A',
					'lastname'			=> 'N/A',
					'email'				=> $email,
					'telephone'			=> 'N/A',
					'fax'				=> 'N/A',
					'password'			=> $password,
					'newsletter'		=> '1',
					'company'			=> '',
					'address_1'			=> 'N/A',
					'address_2'			=> 'N/A',
					'city'				=> 'N/A',
					'postcode'			=> $postcode,
					'country_id'		=> isset($this->session->data['kco_country_id']) ? $this->session->data['kco_country_id'] : '0',
					'zone_id'			=> '0',
				);

				$customer_id = $this->model_account_customer->addCustomer($customer);

				if ($customer_id) {

					$this->model_account_customer->deleteLoginAttempts($email);

					$this->session->data['account'] = 'register';

					$this->customer->login($email, $password);

					unset($this->session->data['guest']);
					unset($this->session->data['shipping_method']);
					unset($this->session->data['shipping_methods']);
					unset($this->session->data['payment_method']);
					unset($this->session->data['payment_methods']);

				}
				else {

					$json['error'] = $this->language->get('error_account_create');

				}

			}

		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

}

?>
