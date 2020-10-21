<?php

class ControllerKlarnaVoucher extends Controller {

	public function index() {

		// LOAD LANGUAGE
		$this->load->language('klarna/checkout');

		$data['text_voucher_code']	= $this->language->get('text_voucher_code');
		$data['voucher']			= NULL;

		if ( (isset($this->session->data['voucher'])) AND (!empty($this->session->data['voucher'])) ) {

			$this->load->model('extension/total/voucher');

			$data['voucher'] = $this->model_extension_total_voucher->getVoucher($this->session->data['voucher']);

		}

		// OUTPUT
		$this->response->setOutput($this->load->view('klarna/voucher', $data));

	}

	public function remove() {

		$json = array();

		$this->session->data['voucher'] = NULL;

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

	public function add() {

		$json = array();

		$this->load->language('klarna/checkout');
		$this->load->model('extension/total/voucher');

		$voucher = (isset($this->request->post['voucher'])) ? trim($this->request->post['voucher']) : NULL;

		$result = $this->model_extension_total_voucher->getVoucher($voucher);

		if (empty($voucher)) {

			$json['error'] = $this->language->get('error_no_voucher');
			unset($this->session->data['voucher']);

		} elseif ($result) {

			$json['success'] = $this->language->get('success_add_voucher');
			$this->session->data['voucher'] = $this->request->post['voucher'];

		} else {

			$json['error'] = $this->language->get('error_unknown_voucher');

		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

}

?>
