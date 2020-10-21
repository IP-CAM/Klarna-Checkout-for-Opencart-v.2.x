<?php

class ControllerKlarnaCoupon extends Controller {

	public function index() {

		// LOAD LANGUAGE
		$this->load->language('klarna/checkout');

		$data['text_coupon_code']	= $this->language->get('text_coupon_code');
		$data['coupon']				= NULL;

		if ( (isset($this->session->data['coupon'])) AND (!empty($this->session->data['coupon'])) ) {

			$this->load->model('extension/total/coupon');

			$data['coupon'] = $this->model_extension_total_coupon->getCoupon($this->session->data['coupon']);

		}

		// OUTPUT
		$this->response->setOutput($this->load->view('klarna/coupon', $data));

	}

	public function remove() {

		$json = array();

		$this->session->data['coupon'] = NULL;

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

	public function add() {

		$json = array();

		$this->load->language('klarna/checkout');
		$this->load->model('extension/total/coupon');

		$coupon = (isset($this->request->post['coupon'])) ? trim($this->request->post['coupon']) : NULL;

		$result = $this->model_extension_total_coupon->getCoupon($coupon);

		if (empty($coupon)) {

			$json['error'] = $this->language->get('error_no_coupon');
			unset($this->session->data['coupon']);

		} elseif ($result) {

			$json['success'] = $this->language->get('success_add_coupon');
			$this->session->data['coupon'] = $this->request->post['coupon'];

		} else {

			$json['error'] = $this->language->get('error_unknown_coupon');

		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

}

?>
