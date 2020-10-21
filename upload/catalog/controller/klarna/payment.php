<?php

class ControllerKlarnaPayment extends Controller {

	public function index() {

		$email 		= (isset($this->request->post['email'])) 		? $this->request->post['email'] 		: NULL;
		$postcode 	= (isset($this->request->post['postcode'])) 	? $this->request->post['postcode'] 		: NULL;
		$order_id 	= (isset($this->session->data['order_id'])) 	? (int)$this->session->data['order_id'] : NULL;

		$logfile = DIR_LOGS . 'kco/create/' . date('Ymd_His') . '_';

		if (!file_exists(DIR_LOGS . 'kco')) 		{ mkdir(DIR_LOGS . 'kco', 0777, true); }
		if (!file_exists(DIR_LOGS . 'kco/create')) 	{ mkdir(DIR_LOGS . 'kco/create', 0777, true); }

		$this->load->language('klarna/checkout');
		$this->load->model('extension/extension');

		$data['heading_error']	= $this->language->get('heading_error');
		$data['error_unknown']	= $this->language->get('error_unknown');

		$order_id = $this->addOrder($order_id, $email, $postcode);

		$products = $this->cart->getProducts();
		$cart = array();

		foreach ($products as $product) {

			$product['tax']		= $this->getTax($product['price'], $product['tax_class_id']);
			$product['price']	= $this->getPrice($product['price'], $product['tax_class_id']);

			// GET NAME
			$product['name'] = trim($product['name']);

			// CHECK IF ANY OPTIONS IS SET AND NO MORE THEN 3
			if ( ($this->config->get('kco_product_option')) AND (count($product['option'])) AND (count($product['option'])<4) ) {
				foreach ($product['option'] as $option) {
					$product['name'] .= sprintf(' (%s: %s)', $option['name'], $option['value']);
				}
			}

			$cart[] = array(
				'reference'		=> $product['model'],
				'name'			=> $product['name'],
				'quantity'		=> (int)$product['quantity'],
				'unit_price'	=> (int)$product['price'],
				'tax_rate'		=> (int)$product['tax'],
			);

		}

		// VOUCHERS
		if (!empty($this->session->data['vouchers'])) {
			foreach ($this->session->data['vouchers'] as $key => $voucher) {
				$cart[] = array(
					'reference'		=> 'GIFTCERT',
					'name'			=> $this->language->get('text_voucher_description'),
					'quantity'		=> 1,
					'unit_price'	=> (int)$this->getPrice($voucher['amount']),
					'tax_rate'		=> 0
				);
			}
		}

		// SHIPPING
		if (!empty($this->session->data['shipping_method'])) {

			$name			= isset($this->session->data['shipping_method']['title']) 			? $this->session->data['shipping_method']['title'] 			: '';
			$name 			= ($this->config->get('kco_override_shipping')) 					? $this->language->get('text_override_shipping') 			: $name;

			$cost			= isset($this->session->data['shipping_method']['cost']) 			? $this->session->data['shipping_method']['cost'] 			: 0;
			$tax_class_id	= isset($this->session->data['shipping_method']['tax_class_id']) 	? $this->session->data['shipping_method']['tax_class_id'] 	: 0;

			$tax			= $this->getTax($cost, $tax_class_id);
			$cost			= $this->getPrice($cost, $tax_class_id);

			$cart[] = array(
				'type'			=> 'shipping_fee',
				'reference'		=> 'SHIPPING',
				'name'			=> $name,
				'quantity'		=> 1,
				'unit_price'	=> (int)$cost,
				'tax_rate'		=> (int)$tax,
			);

		}

		// SHIPPING
		if ($this->config->get('handling_status') && ($this->cart->getSubTotal() > $this->config->get('handling_total')) && ($this->cart->getSubTotal() > 0)) {

			$name			= $this->language->get('text_handling');
			$cost			= $this->config->get('handling_fee');
			$tax_class_id	= $this->config->get('handling_tax_class_id');

			$tax			= $this->getTax($cost, $tax_class_id);
			$cost			= $this->getPrice($cost, $tax_class_id);

			$cart[] = array(
				'reference'		=> 'HANDLING',
				'name'			=> $name,
				'quantity'		=> 1,
				'unit_price'	=> (int)$cost,
				'tax_rate'		=> (int)$tax,
			);

		}

		// SHIPPING
		if ($this->config->get('low_order_status') && ($this->cart->getSubTotal() > $this->config->get('low_order_total')) && ($this->cart->getSubTotal() > 0)) {

			$name			= $this->language->get('text_low_order_fee');
			$cost			= $this->config->get('low_order_fee_fee');
			$tax_class_id	= $this->config->get('low_order_fee_tax_class_id');

			$tax			= $this->getTax($cost, $tax_class_id);
			$cost			= $this->getPrice($cost, $tax_class_id);

			$cart[] = array(
				'reference'		=> 'LOWORDER',
				'name'			=> $name,
				'quantity'		=> 1,
				'unit_price'	=> (int)$cost,
				'tax_rate'		=> (int)$tax,
			);

		}

		// TOTALS
		$totals		= array();
		$taxes		= $this->cart->getTaxes();
		$total		= 0;

		$total_data = array(
			'totals' => &$totals,
			'taxes'  => &$taxes,
			'total'  => &$total
		);

		$results = $this->model_extension_extension->getExtensions('total');

		foreach ($results as $result) {
			if ($this->config->get($result['code'] . '_status')) {
				$this->load->model('extension/total/' . $result['code']);
				$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
			}
		}

		foreach ($totals as $total) {

			if ($total['code']=='voucher') {

				$total['value'] = $this->getPrice($total['value']);

				$cart[] = array(
					'type'			=> 'discount',
					'reference'		=> 'VOUCHER',
					'name'			=> $total['title'],
					'unit_price'	=> (int)$total['value'],
					'tax_rate'		=> 0
				);
			}

		}

		if (isset($this->session->data['coupon'])) {

			// LOAD MODEL FOR CPOUPON
			$this->load->model('extension/total/coupon');

			// GET COUNPON DATA
			$coupon = $this->model_extension_total_coupon->getCoupon($this->session->data['coupon']);

			// CHECK IF DATA IS OK
			if ($coupon) {

				// EMPTY VARS
				$discount = 0;
				$coupon_s = 0;
				$coupon_p = 0;
				$coupon_f = 0;
				$coupon_t = $coupon['discount'];

				// CHECK IF FREE SHIPPING IS ENABLED
				if ($coupon['shipping'] && isset($this->session->data['shipping_method'])) {

					// ADD SHIPPING DISCOUNT
					$coupon_s += $this->getPrice($this->session->data['shipping_method']['cost'], $this->session->data['shipping_method']['tax_class_id']);

				}

				// LOOP PRODUCTS IN CART
				foreach ($products as $product) {

					// CHECK IF ANY PRODUCTS IS SPECIFIED IN COUPON AND EXISTS IN CART - OR - IF NO SPECIFIC PRODUCTS IS SET
					if ( (count($coupon['product']) AND in_array($product['product_id'], $coupon['product'])) OR (!count($coupon['product'])) ) {

						// CALCULATE PRODUCT TOTAL FOR (%)
						$coupon_p += $this->getPrice($product['total'], $product['tax_class_id']);

						// CHECK IF VALUE IS HIGHER THEN 0
						if ($coupon_t) {

							// GET AMOUNT TO SUBSRACT
							$coupon_h = ($product['total']>=$coupon_t) ? $coupon_t : $product['total'];

							// CALUCATE DISCOUNT FOR ($)
							$coupon_f = $this->getPrice($coupon_h, $product['tax_class_id']);

							// IF AMOUNT IS LESS THEN DISCOUNT, CALCUATE THE REMAINING AMOUNT
							$coupon_t = ($coupon_t) ? ($coupon_t - $coupon_h) : $coupon_t;

						}

					}

				}

				// CHECK IF ANY DISCOUNT IS SET
				if ($coupon_p OR $coupon_f) {

					// GET DISCOUNT
					if ($coupon['type']=='P') 	{ $discount = $coupon_p * ((float)($coupon['discount'] / 100)); }
					else 						{ $discount = $coupon_f; }

					// ADD SHIPPING DISCOUNT
					if ($coupon_s) 				{ $discount += $coupon_s; }

					// ADD TO CART
					$cart[] = array(
						'type'			=> 'discount',
						'reference'		=> 'COUPON',
						'name'			=> $this->language->get('text_discount'),
						'unit_price'	=> -(int)$discount,
						'tax_rate'		=> 0
					);

				}

			}

		}

		//die('<pre>' . print_r($cart, true));

		$eid		= isset($this->session->data['kco_eid']) 			? trim($this->session->data['kco_eid']) 			: '200';
		$secret		= isset($this->session->data['kco_secret']) 		? trim($this->session->data['kco_secret']) 			: 'test';

		$locale 	= isset($this->session->data['kco_locale']) 		? strtolower($this->session->data['kco_locale']) 	: 'sv-se';
		$currency 	= isset($this->session->data['kco_currency']) 		? strtoupper($this->session->data['kco_currency']) 	: 'SEK';
		$country 	= isset($this->session->data['kco_country'])  		? strtoupper($this->session->data['kco_country'])  	: 'SE';

		$kid 		= isset($this->session->data['kco_order_id']) 		? $this->session->data['kco_order_id'] 				: NULL;

		$params = array();

		$params['purchase_country']					= $country;
		$params['purchase_currency']				= $currency;
		$params['locale']							= $locale;

		$params['shipping_address']['postal_code']	= $postcode;
		$params['shipping_address']['email']		= $email;

		$params['merchant_reference'] 				= array('orderid1' => (string)$order_id);

		foreach ($cart as $item) {
			$params['cart']['items'][] = $item;
		}

		$params['merchant'] = array(
			'id'				=> $eid,
			'terms_uri'			=> $this->url->link('information/information', array('information_id' => $this->config->get('config_checkout_id'))),
			'checkout_uri'		=> $this->url->link('klarna/checkout'),
			'confirmation_uri'	=> $this->url->link('klarna/success') . '&kid={checkout.order.id}&oid=' . $order_id,
			'push_uri'			=> $this->url->link('klarna/push') . '&kid={checkout.order.id}&oid=' . $order_id,
		);

		// LOAD KLARNA CHECKOUT
		include_once(DIR_SYSTEM . 'vendor/Klarna/Checkout.php');

		// GET KLARNA DOMAIN
		$server = ($this->config->get('kco_test_mode')) ? Klarna_Checkout_Connector::BASE_TEST_URL : Klarna_Checkout_Connector::BASE_URL;

		// START KLARNA CHECKOUT
		$oConnector = Klarna_Checkout_Connector::create($secret, $server);

		if ($kid) {

			$order = new Klarna_Checkout_Order($oConnector, $kid);

			try {

				$order->update($params);
				$order->fetch();

			}

			catch(Exception $e) {

				// LOG ERROR IF LOGGING IS ENABLED
				if ($this->config->get('kco_log_mode')) { file_put_contents($logfile . 'order_update_fail.txt', print_r($e, true)); }

				// GET PAYLOAD
				$e = (method_exists($e, 'getPayload')) ? $e->getPayload() : NULL;

				// FORMAT ERROR MESSAGE
				$message = isset($e['internal_message']) ? sprintf($this->language->get('error_internal_message'), $e['internal_message']) : $this->language->get('error_unknown');

				// EXIT AND PRINT MESSAGE
				die($message);

			}

		}
		else {

			$order = new Klarna_Checkout_Order($oConnector);

			try {

				$order->create($params);
				$order->fetch();

				$this->session->data['kco_order_id'] = $order['id'];

			}
			catch(Exception $e) {

				// LOG ERROR IF LOGGING IS ENABLED
				if ($this->config->get('kco_log_mode')) { file_put_contents($logfile . 'order_create_fail.txt', print_r($e, true)); }

				// GET PAYLOAD
				$e = (method_exists($e, 'getPayload')) ? $e->getPayload() : NULL;

				// FORMAT ERROR MESSAGE
				$message = isset($e['internal_message']) ? sprintf($this->language->get('error_internal_message'), $e['internal_message']) : $this->language->get('error_unknown');

				// EXIT AND PRINT MESSAGE
				die($message);

			}

		}

		// Display checkout
		$data['snippet'] = (isset($order['gui']['snippet'])) ? $order['gui']['snippet'] : NULL;

		$this->response->setOutput($this->load->view('klarna/payment', $data));

	}

	private function addOrder($order_id, $email, $postcode) {

		// LOAD LANGUAGE
		$this->load->language('klarna/checkout');

		// SET VALUES
		$customer_id 		= 0;
		$customer_group_id 	= $this->config->get('config_customer_group_id');
		$firstname 			= NULL;
		$lastname 			= NULL;
		$email 				= $email;
		$telephone 			= $postcode;
		$fax 				= NULL;
		$custom_field 		= NULL;

		// CHECK IF USER IS LOGGED
		if ($this->customer->isLogged()) {

			// LOAD MODEL
			$this->load->model('account/customer');

			// GET CUSTOMER DATA
			$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

			// SET VALUES
			$customer_id 		= $this->customer->getId();
			$customer_group_id 	= $customer_info['customer_group_id'];
			$firstname 			= $customer_info['firstname'];
			$lastname 			= $customer_info['lastname'];
			$email 				= $customer_info['email'];
			$telephone 			= $customer_info['telephone'];
			$fax 				= $customer_info['fax'];
			$custom_field 		= unserialize($customer_info['custom_field']);

		}

		$sort_order	= array();
		$totals		= array();
		$taxes		= $this->cart->getTaxes();
		$total		= 0;

		$total_data = array(
			'totals'	=> &$totals,
			'taxes'		=> &$taxes,
			'total'		=> &$total
		);

		$this->load->model('extension/extension');

		$results = $this->model_extension_extension->getExtensions('total');

		foreach ($results as $key => $value) {
			$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
		}

		array_multisort($sort_order, SORT_ASC, $results);

		foreach ($results as $result) {
			if ($this->config->get($result['code'] . '_status')) {
				$this->load->model('extension/total/' . $result['code']);

				$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
			}
		}

		$sort_order = array();

		foreach ($totals as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}

		array_multisort($sort_order, SORT_ASC, $totals);

		// MAKE EMPTY ARRAY
		$products = array();

		// LOOP CART
		foreach ($this->cart->getProducts() as $product) {

			// MAKE EMPTY ARRAY
			$option_data = array();

			// LOOP OPTIONS
			foreach ($product['option'] as $option) {

				// ADD OPTION TO ARRAY
				$option_data[] = array(
					'product_option_id'       => $option['product_option_id'],
					'product_option_value_id' => $option['product_option_value_id'],
					'option_id'               => $option['option_id'],
					'option_value_id'         => $option['option_value_id'],
					'name'                    => $option['name'],
					'value'                   => $option['value'],
					'type'                    => $option['type']
				);

			}

			// ADD PRODUCT TO ARRAY
			$products[] = array(
				'product_id' => $product['product_id'],
				'name'       => $product['name'],
				'model'      => $product['model'],
				'option'     => $option_data,
				'download'   => $product['download'],
				'quantity'   => $product['quantity'],
				'subtract'   => $product['subtract'],
				'price'      => $product['price'],
				'total'      => $product['total'],
				'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
				'reward'     => $product['reward']
			);

		}

		// MAKE EMPTY ARRAY
		$vouchers = array();

		// CHECK IF ANY VOUCHERS IS SET
		if (!empty($this->session->data['vouchers'])) {

			// LOOP VOUCHERS FROM SESSION
			foreach ($this->session->data['vouchers'] as $voucher) {

				// ADD VOUCHER TO ARRAY
				$vouchers[] = array(
					'description'      => $voucher['description'],
					'code'             => substr(md5(mt_rand()), 0, 10),
					'to_name'          => $voucher['to_name'],
					'to_email'         => $voucher['to_email'],
					'from_name'        => $voucher['from_name'],
					'from_email'       => $voucher['from_email'],
					'voucher_theme_id' => $voucher['voucher_theme_id'],
					'message'          => $voucher['message'],
					'amount'           => $voucher['amount']
				);

			}

		}

		// CHECK IF TRACKING IS SET
		if (isset($this->request->cookie['tracking'])) {

			// GET TRACKING CODE FROM SESSION
			$tracking = $this->request->cookie['tracking'];

			// GET SUBTOTAL FOR CART
			$subtotal = $this->cart->getSubTotal();

			// LOAD MODEL
			$this->load->model('affiliate/affiliate');

			// GET DATA FOR AFFILIATE IF ANY
			$affiliate_info = $this->model_affiliate_affiliate->getAffiliateByCode($tracking);

			// CHECK IF DATA IS FOUND
			if ($affiliate_info) {

				// ADD ID AND COMMISSION TO ORDER
				$affiliate_id 	= $affiliate_info['affiliate_id'];
				$commission 	= ($subtotal / 100) * $affiliate_info['commission'];

			}

			// LOAD MODEL
			$this->load->model('checkout/marketing');

			// GET DATA FOR MARTETING IF ANY
			$marketing_info = $this->model_checkout_marketing->getMarketingByCode($tracking);

			// CHECK IF DATA IS FOUND
			if ($marketing_info) {

				// ADD ID TO ORDER
				$marketing_id = $marketing_info['marketing_id'];

			}

		}

		$order = array(
			'order_id'					=> $order_id,
			'invoice_prefix' 			=> $this->config->get('config_invoice_prefix'),
			'store_id' 					=> $this->config->get('config_store_id'),
			'store_name' 				=> $this->config->get('config_name'),
			'store_url' 				=> isset($order_data['store_id']) ? $this->config->get('config_url') : HTTP_SERVER,
			'customer_id' 				=> $customer_id,
			'customer_group_id'			=> $customer_group_id,
			'firstname' 				=> $firstname,
			'lastname' 					=> $lastname,
			'email'						=> $email,
			'telephone' 				=> $telephone,
			'fax' 						=> $fax,
			'custom_field'				=> NULL,
			'payment_firstname' 		=> NULL,
			'payment_lastname' 			=> NULL,
			'payment_company' 			=> NULL,
			'payment_address_1' 		=> NULL,
			'payment_address_2' 		=> NULL,
			'payment_city' 				=> NULL,
			'payment_postcode' 			=> NULL,
			'payment_country' 			=> NULL,
			'payment_country_id' 		=> NULL,
			'payment_zone' 				=> NULL,
			'payment_zone_id' 			=> NULL,
			'payment_address_format' 	=> NULL,
			'payment_custom_field' 		=> NULL,
			'payment_method' 			=> $this->language->get('text_payment_method'),
			'payment_code'				=> 'kco',
			'shipping_firstname' 		=> NULL,
			'shipping_lastname' 		=> NULL,
			'shipping_company' 			=> NULL,
			'shipping_address_1' 		=> NULL,
			'shipping_address_2' 		=> NULL,
			'shipping_city' 			=> NULL,
			'shipping_postcode' 		=> NULL,
			'shipping_country' 			=> NULL,
			'shipping_country_id' 		=> NULL,
			'shipping_zone' 			=> NULL,
			'shipping_zone_id' 			=> NULL,
			'shipping_address_format' 	=> NULL,
			'shipping_custom_field' 	=> NULL,
			'shipping_method' 			=> isset($this->session->data['shipping_method']['title']) 	? $this->session->data['shipping_method']['title'] 					: NULL,
			'shipping_code' 			=> isset($this->session->data['shipping_method']['code'])  	? $this->session->data['shipping_method']['code']  					: NULL,
			'comment' 					=> isset($this->session->data['comment']) 					? $this->session->data['comment'] 									: NULL,
			'affiliate_id' 				=> isset($affiliate_id) 									? $affiliate_id 													: NULL,
			'commission' 				=> isset($commission) 										? $commission 														: NULL,
			'marketing_id' 				=> isset($marketing_id) 									? $marketing_id 													: NULL,
			'tracking' 					=> isset($tracking) 										? $tracking 														: NULL,
			'currency_id' 				=> isset($this->session->data['kco_currency']) 				? $this->currency->getId($this->session->data['kco_currency']) 		: '0',
			'currency_code' 			=> isset($this->session->data['kco_currency']) 				? $this->session->data['kco_currency'] 								: 'SEK',
			'currency_value' 			=> isset($this->session->data['kco_currency']) 				? $this->currency->getValue($this->session->data['kco_currency'])	: '0',
			'ip' 						=> isset($this->request->server['REMOTE_ADDR']) 			? $this->request->server['REMOTE_ADDR'] 							: NULL,
			'forwarded_ip' 				=> isset($this->request->server['HTTP_X_FORWARDED_FOR']) 	? $this->request->server['HTTP_X_FORWARDED_FOR'] 					: NULL,
			'user_agent' 				=> isset($this->request->server['HTTP_USER_AGENT']) 		? $this->request->server['HTTP_USER_AGENT'] 						: NULL,
			'accept_language' 			=> isset($this->request->server['HTTP_ACCEPT_LANGUAGE']) 	? $this->request->server['HTTP_ACCEPT_LANGUAGE'] 					: NULL,
			'kco_eid'					=> isset($this->session->data['kco_eid']) 					? $this->session->data['kco_eid'] 									: '200',
			'language_id' 				=> $this->config->get('config_language_id'),
			'vouchers'					=> $vouchers,
			'products'					=> $products,
			'totals'					=> $totals,
			'total' 					=> $total,
		);

		$this->load->model('klarna/checkout');

		$order_id 	= $this->model_klarna_checkout->addOrder($order);

		$locale 	= isset($this->session->data['kco_locale']) ? strtolower($this->session->data['kco_locale']) 	: 'sv-se';
		$eid		= isset($this->session->data['kco_eid']) 	? trim($this->session->data['kco_eid']) 			: '200';
		$secret		= isset($this->session->data['kco_secret']) ? trim($this->session->data['kco_secret']) 			: 'test';

		$this->model_klarna_checkout->addKlarnaOrder($order_id, $eid, $secret, $locale);

		$this->session->data['order_id'] = (int)$order_id;

		return (int)$order_id;

	}

	private function getTax($price, $tax_class_id) {

		$value = $this->currency->getValue($this->session->data['kco_currency']);
		$price = ($value) ? ($price * $value) : $price;

		$tax 	= $this->tax->getTax($price, $tax_class_id);
		$sub	= $this->tax->calculate($price, $tax_class_id, 0);
		$sum	= ($sub) ? ((float)$tax / (float)$sub) * 10000 : 0;

		return $sum;

	}

	private function getPrice($price, $tax_class_id=NULL) {

		$value = $this->currency->getValue($this->session->data['kco_currency']);
		$price = ($value) ? ($price * $value) : $price;

		$price = ($tax_class_id) ? $this->tax->calculate($price, $tax_class_id, 1) : $price;

		$price = number_format((float)$price, 2, $this->language->get('decimal_point'), $this->language->get('thousand_point'));
		$price = str_replace($this->language->get('decimal_point'), '', $price);
		$price = str_replace($this->language->get('thousand_point'), '', $price);
		$price = trim($price);

		return $price;

	}

}

?>
