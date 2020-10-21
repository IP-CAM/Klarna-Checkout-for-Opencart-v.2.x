<?php

class ControllerKlarnaSuccess extends Controller {

	public function index() {

		unset($this->session->data['shipping_method']);
		unset($this->session->data['shipping_methods']);
		unset($this->session->data['payment_method']);
		unset($this->session->data['payment_methods']);
		unset($this->session->data['guest']);
		unset($this->session->data['comment']);
		unset($this->session->data['order_id']);
		unset($this->session->data['coupon']);
		unset($this->session->data['reward']);
		unset($this->session->data['voucher']);
		unset($this->session->data['vouchers']);
		unset($this->session->data['totals']);

		unset($this->session->data['kco_eid']);
		unset($this->session->data['kco_secret']);
		unset($this->session->data['kco_locale']);
		unset($this->session->data['kco_currency']);
		unset($this->session->data['kco_country']);
		unset($this->session->data['kco_country_id']);
		unset($this->session->data['kco_order_id']);
		unset($this->session->data['kco_email']);
		unset($this->session->data['kco_postcode']);

		// CLEAR CART
		$this->cart->clear();

		$this->load->language('klarna/checkout');
		$this->load->model('klarna/checkout');
		$this->load->model('extension/extension');

		$data['icon'] = NULL;
		$data['logo'] = NULL;

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$data['icon'] = $this->config->get('config_url') . 'image/' . $this->config->get('config_icon');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $this->config->get('config_url') . 'image/' . $this->config->get('config_logo');
		}

		if (!$this->config->get('kco_logo')) {
			$data['logo'] = NULL;
		}

		$data['title']					= $this->config->get('config_meta_title');

		$data['base']					= ($this->request->server['HTTPS']) ? $this->config->get('config_ssl') : $this->config->get('config_url');
		$data['description']			= $this->config->get('config_meta_description');
		$data['keywords']				= $this->config->get('config_meta_keyword');
		$data['lang']					= $this->language->get('code');
		$data['direction']				= $this->language->get('direction');
		$data['name']					= $this->config->get('config_name');

		$data['home']					= $this->url->link('common/home');

		$data['heading_thank_you']		= $this->language->get('heading_thank_you');
		$data['heading_receipt']		= $this->language->get('heading_receipt');

		$data['text_thank_you']			= sprintf($this->language->get('text_thank_you'), $this->config->get('config_email'), $this->config->get('config_email'));
		$data['text_pay_now']			= $this->language->get('text_pay_now');
		$data['text_continue']			= $this->language->get('text_continue');

		// GET ORDER IDS
		$kid = isset($this->request->get['kid']) ? trim($this->request->get['kid']) : NULL;
		$oid = isset($this->request->get['oid']) ? trim($this->request->get['oid']) : NULL;

		// IF NO ORDER, DIE
		if (is_null($kid)) 		{ die($this->language->get('error_push_no_kco_id')); }
		elseif (is_null($oid)) 	{ die($this->language->get('error_push_no_oc_id')); }

		$oc_order = $this->model_klarna_checkout->getKlarnaOrder($oid);

		if (!$oc_order) { die($this->language->get('error_push_no_oc_order')); }

		// GET EID AND SECRET OR USE KLARNAS TEST ACCOUNT
		$eid		= isset($oc_order['eid']) 		? $oc_order['eid'] 		: '200';
		$secret		= isset($oc_order['secret']) 	? $oc_order['secret'] 	: 'test';

		// LOAD KLARNA CHECKOUT
		include_once(DIR_SYSTEM . 'vendor/Klarna/Checkout.php');

		// GET KLARNA DOMAIN
		$server = ($this->config->get('kco_test_mode')) ? Klarna_Checkout_Connector::BASE_TEST_URL : Klarna_Checkout_Connector::BASE_URL;

		// START KLARNA CHECKOUT
		$oConnector = Klarna_Checkout_Connector::create($secret, $server);

		// GET ORDER
		$order = new Klarna_Checkout_Order($oConnector, basename($kid));

		// TRY TO GET ORDER
		try {

			$order->fetch();

		}
		catch (Exception $e) {

			die($this->language->get('error_push_no_kco_order'));

		}

		$data['snippet'] = (isset($order['gui']['snippet'])) ? $order['gui']['snippet'] : NULL;

		// GOOGLE ANALYTICS

		$ecom = NULL;

		if ($this->config->get('kco_status_analytics')) {

			$ga_order		= $this->model_klarna_checkout->getGAOrder($oid);
			$ga_products	= $this->model_klarna_checkout->getGAProducts($oid);

			$id				= isset($oid) 						? $oid 						: '0';
			$affiliation	= isset($ga_order['store_name']) 	? $ga_order['store_name'] 	: $this->config->get('config_name');

			$revenue		= isset($ga_order['revenue']) 		? $ga_order['revenue'] 		: '0';
			$shipping		= isset($ga_order['shipping']) 		? $ga_order['shipping'] 	: '0';
			$tax			= isset($ga_order['tax']) 			? $ga_order['tax'] 			: '0';

			$ecom  = "<script>\n";
			$ecom .= "ga('require', 'ecommerce', 'ecommerce.js');\n";
			$ecom .= sprintf("ga('ecommerce:addTransaction', {'id':'%s', 'affiliation':'%s', 'revenue':'%s', 'shipping':'%s', 'tax':'%s'});\n", $id, $affiliation, $revenue, $shipping, $tax);

			foreach ($ga_products as $product) {

				$name		= isset($product['name']) 			? $product['name'] 			: 'NA';
				$sku		= isset($product['sku']) 			? $product['sku'] 			: 'NA';

				$price		= isset($product['price']) 			? $product['price'] 		: '0';
				$quantity	= isset($product['quantity']) 		? $product['quantity'] 		: '0';

				$ecom .= sprintf("ga('ecommerce:addItem', {'id':'%s', 'name':'%s', 'sku':'%s', 'price':'%s', 'quantity':'%s'});\n", $id, $name, $sku, $price, $quantity);

			}

			$ecom .= "ga('ecommerce:send');\n";
			$ecom .= "</script>\n";

		}

		// EMPTY ARRAY
		$data['analytics'] = array();

		// GET EXTENSIONS
		$analytics = $this->model_extension_extension->getExtensions('analytics');

		// LOOP THEM AND ADD ENABLED EXTENSIONS TO OUR ARRAY
		foreach ($analytics as $analytic) {
			if ($this->config->get($analytic['code'] . '_status') && $analytic['code'] == 'google_analytics') {
				$extension = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get($analytic['code'] . '_status'));
				$data['analytics'][] = $extension;
			}
		}

		$data['analytics'][] = $ecom;

		// OUTPUT
		$this->response->setOutput($this->load->view('klarna/success', $data));

	}

}
