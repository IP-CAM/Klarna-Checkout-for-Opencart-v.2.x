<?php

class ControllerKlarnaComment extends Controller {

	public function index() {

		$json = array();

		if (isset($this->request->post['comment'])) {
			$this->session->data['comment'] = strip_tags($this->request->post['comment']);
		}

		if (isset($this->session->data['order_id']) AND ($this->session->data['order_id'])) {

			$this->load->model('klarna/checkout');

			$this->model_klarna_checkout->addComment($this->session->data['order_id'], $this->session->data['comment']);

		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

}

?>
