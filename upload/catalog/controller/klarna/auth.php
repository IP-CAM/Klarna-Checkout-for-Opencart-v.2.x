<?php

class ControllerKlarnaAuth extends Controller {

	public function index() {

		$json = array();

		$this->load->model('account/customer');
		$this->load->language('account/login');

		// Check how many login attempts have been made.
		$attempts = $this->model_account_customer->getLoginAttempts($this->request->post['email']);

		if ($attempts && ($attempts['total'] >= $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($attempts['date_modified'])) {
			$json['error'] = $this->language->get('error_attempts');
		}

		// Check if customer has been approved.
		$customer = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

		if ($customer && !$customer['approved']) {
			$json['error'] = $this->language->get('error_approved');
		}

		if (!isset($json['error'])) {
			if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {

				$json['error'] = $this->language->get('error_login');

				$this->model_account_customer->addLoginAttempt($this->request->post['email']);

			} else {

				$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
			}

		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

}

?>
