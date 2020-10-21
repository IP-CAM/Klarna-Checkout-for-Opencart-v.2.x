<?php

class ControllerExtensionPaymentKco extends Controller {

	private $error 		= array();
	private $name 		= NULL;

	public function index() {

		// SET NAME
		$this->name = basename(__FILE__, '.php');

		// LOAD LANGUAGE
		$this->load->language('extension/payment/' . $this->name);

		// SET META TITLE
		$this->document->setTitle($this->language->get('heading_title'));

		// LOAD SETTINGS
		$this->load->model('setting/setting');

		// SET TITLE
		$data['heading_title']		= $this->language->get('heading_title');

		// SET TEXTS
		$data['text_edit']			= $this->language->get('text_edit');
		$data['text_no_settings']	= $this->language->get('text_no_settings');

		// SET BUTTON TEXT
		$data['button_cancel']		= $this->language->get('button_cancel');

		// BREABCRUMBS
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('payment/' . $this->name, 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true);


		// LOAD COMMON CONTROLLERS
		$data['header'] 			= $this->load->controller('common/header');
		$data['column_left'] 		= $this->load->controller('common/column_left');
		$data['footer'] 			= $this->load->controller('common/footer');

		// SET OUTPUT
		$this->response->setOutput($this->load->view('extension/payment/' . $this->name . '.tpl', $data));

	}

}

?>
