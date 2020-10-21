<?php
class ControllerExtensionModuleKCO extends Controller {

	private $error 		= array();
	private $name 		= 'kco';

	public function index() {

		// LOAD LANGUAGE
		$this->load->language('extension/module/' . $this->name);

		// SET META TITLE
		$this->document->setTitle($this->language->get('heading_title'));

		// LOAD SETTINGS
		$this->load->model('setting/setting');

		// IF POST
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			// SAVE SETTINGS
			$this->model_setting_setting->editSetting($this->name, $this->request->post);

			// SET SUCCESS MSG
			$this->session->data['success'] = $this->language->get('text_success');

			// REDIRECT TO MODULE LIST
			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true));

		}

		$result = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "order_kco'");

		if (!$result->num_rows) {

			$this->db->query("
				CREATE TABLE `" . DB_PREFIX . "order_kco` (
					`oid` 					int(11) unsigned NOT NULL,
					`kid` 					int(11) unsigned DEFAULT NULL,
					`eid` 					char(6) DEFAULT NULL,
					`secret` 				char(30) DEFAULT NULL,
					`reservation` 			varchar(30) DEFAULT NULL,
					`invoice_no` 			varchar(30) DEFAULT NULL,
					`locale` 				varchar(10) DEFAULT NULL,
					`country` 				varchar(8) DEFAULT NULL,
					`currency` 				varchar(4) DEFAULT NULL,
					`reference` 			varchar(50) DEFAULT NULL,
					`status` 				varchar(30) DEFAULT NULL,
					`type` 					varchar(20) DEFAULT NULL,
					`date_of_birth` 		varchar(20) DEFAULT NULL,
					`gender` 				varchar(10) DEFAULT NULL,
					`risk` 					varchar(50) DEFAULT NULL,
					`notes` 				text,
					`date_added` 			datetime DEFAULT NULL,
					`date_created` 			datetime DEFAULT NULL,
					`date_modified` 		datetime DEFAULT NULL,
					`date_activated` 		datetime DEFAULT NULL,
					`date_canceled` 		datetime DEFAULT NULL,
				  PRIMARY KEY (`oid`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;
			");

		}

		// SET TITLE
		$data['heading_title']					= $this->language->get('heading_title');

		// SET TEXTS
		$data['text_edit']						= $this->language->get('text_edit');
		$data['text_enabled']					= $this->language->get('text_enabled');
		$data['text_disabled']					= $this->language->get('text_disabled');

		$data['entry_test_mode']				= $this->language->get('entry_test_mode');
		$data['entry_log_mode']					= $this->language->get('entry_log_mode');
		$data['entry_order_status']				= $this->language->get('entry_order_status');
		$data['entry_status_account']			= $this->language->get('entry_status_account');
		$data['entry_status_coupon']			= $this->language->get('entry_status_coupon');
		$data['entry_status_voucher']			= $this->language->get('entry_status_voucher');
		$data['entry_status_comment']			= $this->language->get('entry_status_comment');
		$data['entry_status_analytics']			= $this->language->get('entry_status_analytics');
		$data['entry_status_checkout']			= $this->language->get('entry_status_checkout');
		$data['entry_status']					= $this->language->get('entry_status');

		$data['entry_eid']						= $this->language->get('entry_eid');
		$data['entry_secret']					= $this->language->get('entry_secret');
		$data['entry_country']					= $this->language->get('entry_country');

		$data['entry_logo']						= $this->language->get('entry_logo');
		$data['entry_product_option']			= $this->language->get('entry_product_option');
		$data['entry_auto_focus']				= $this->language->get('entry_auto_focus');
		$data['entry_override_shipping']		= $this->language->get('entry_override_shipping');
		$data['entry_override_subtotal']		= $this->language->get('entry_override_subtotal');
		$data['entry_override_tax']				= $this->language->get('entry_override_tax');
		$data['entry_override_total']			= $this->language->get('entry_override_total');

		$data['tab_general']					= $this->language->get('tab_general');
		$data['tab_settings']					= $this->language->get('tab_settings');
		$data['tab_au']							= $this->language->get('tab_au');
		$data['tab_de']							= $this->language->get('tab_de');
		$data['tab_dk']							= $this->language->get('tab_dk');
		$data['tab_fi']							= $this->language->get('tab_fi');
		$data['tab_no']							= $this->language->get('tab_no');
		$data['tab_se']							= $this->language->get('tab_se');

		$data['button_save']					= $this->language->get('button_save');
		$data['button_cancel']					= $this->language->get('button_cancel');

		if (isset($this->error['warning'])) 	{ $data['error_warning'] = $this->error['warning']; }
		else 									{ $data['error_warning'] = ''; }

		// BREABCRUMBS
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/' . $this->name, 'token=' . $this->session->data['token'], 'SSL')
		);

		// SET ACTION URL
		$data['action'] = $this->url->link('extension/module/' . $this->name, 'token=' . $this->session->data['token'], 'SSL');

		// SET CANCLE URL
		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$fields = array(
			'au_eid'					=> NULL,
			'au_secret'					=> NULL,
			'au_country_id'				=> '13',
			'au_status'					=> '0',

			'dk_eid'					=> NULL,
			'dk_secret'					=> NULL,
			'dk_country_id'				=> '57',
			'dk_status'					=> '0',

			'de_eid'					=> NULL,
			'de_secret'					=> NULL,
			'de_country_id'				=> '81',
			'de_status'					=> '0',

			'fi_eid'					=> NULL,
			'fi_secret'					=> NULL,
			'fi_country_id'				=> '72',
			'fi_status'					=> '0',

			'no_eid'					=> NULL,
			'no_secret'					=> NULL,
			'no_country_id'				=> '160',
			'no_status'					=> '0',

			'se_eid'					=> NULL,
			'se_secret'					=> NULL,
			'se_country_id'				=> '203',
			'se_status'					=> '1',

			'test_mode'					=> '1',
			'log_mode'					=> '0',
			'status'					=> '1',
			'order_status_id'			=> '0',

			'status_account'			=> '1',
			'status_coupon'				=> '1',
			'status_voucher'			=> '0',
			'status_comment'			=> '0',
			'status_checkout'			=> '0',
			'status_analytics'			=> '0',

			'logo'						=> '0',
			'auto_focus'				=> '0',
			'product_option'			=> '0',
			'override_shipping'			=> '1',
			'override_subtotal'			=> '1',
			'override_tax'				=> '1',
			'override_total'			=> '1',
		);

		foreach ($fields as $field => $default) {
			if     (isset($this->request->post[$this->name.'_'.$field])) 	{ $data[$this->name.'_'.$field] = $this->request->post[$this->name.'_'.$field]; }
			elseif ($this->config->has($this->name.'_'.$field)) 			{ $data[$this->name.'_'.$field] = $this->config->get($this->name.'_'.$field); }
			else 															{ $data[$this->name.'_'.$field] = $default; }
		}

		// LOAD MODEL
		$this->load->model('localisation/order_status');

		// ADD ORDER STATUSES TO DATA
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		// LOAD MODEL
		$this->load->model('localisation/country');

		// ADD ORDER STATUSES TO DATA
		$data['countries'] = $this->model_localisation_country->getCountries();

		// LOAD COMMON CONTROLLERS
		$data['header'] 			= $this->load->controller('common/header');
		$data['column_left'] 		= $this->load->controller('common/column_left');
		$data['footer'] 			= $this->load->controller('common/footer');

		// SET OUTPUT
		$this->response->setOutput($this->load->view('extension/module/kco', $data));
	}

	protected function validate() {

		// CHECK PERMISSION
		if (!$this->user->hasPermission('modify', 'extension/module/' . $this->name)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		// RETURN
		return !$this->error;

	}

}
