<?php

class ControllerKlarnaPush extends Controller {

	public function index() {

		$logfile = DIR_LOGS . 'kco/push/' . date('Ymd_His') . '_';

		if (!file_exists(DIR_LOGS . 'kco')) 		{ mkdir(DIR_LOGS . 'kco', 0777, true); }
		if (!file_exists(DIR_LOGS . 'kco/push')) 	{ mkdir(DIR_LOGS . 'kco/push', 0777, true); }

		// CHECK IF TEST MODE IS ON
		if ($this->config->get('kco_log_mode')) { file_put_contents($logfile . 'request.txt', print_r($this->request, true)); }

		// CHECK IF KCO IS ACTIVE AND SECRET IS SET
		if (!$this->config->get('kco_status')) { die($this->language->get('error_push_inactive')); }

		// GET ORDER IDS
		$kid = isset($this->request->get['kid']) ? trim($this->request->get['kid']) : NULL;
		$oid = isset($this->request->get['oid']) ? trim($this->request->get['oid']) : NULL;

		// IF NO ORDER, DIE
		if (is_null($kid)) 		{ die($this->language->get('error_push_no_kco_id')); }
		elseif (is_null($oid)) 	{ die($this->language->get('error_push_no_oc_id')); }

		// LOAD LANGUAGE
		$this->load->language('klarna/checkout');

		// LOAD MODEL
		$this->load->model('checkout/order');
		$this->load->model('klarna/checkout');

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

			if ($this->config->get('kco_log_mode')) { file_put_contents($logfile . 'order_success.txt', print_r($order, true)); }

		}
		catch (Exception $e) {

			if ($this->config->get('kco_log_mode')) { file_put_contents($logfile . 'order_fail.txt', print_r($e, true)); }

			die($this->language->get('error_push_no_kco_order'));

		}

		// START MAKE DATA FOR ORDER
		$oid_kco = isset($order['merchant_reference']['orderid1']) ? (int)$order['merchant_reference']['orderid1'] : NULL;

		if ($oid!=$oid_kco) { die($this->language->get('error_push_oc_id_match')); }

		// MAKE EMPTY ARRAY
		$data = array();

		$payment_country					= $this->model_klarna_checkout->getCountry($order['billing_address']['country']);
		$shipping_country					= $this->model_klarna_checkout->getCountry($order['shipping_address']['country']);

		$data['order_id']					= $oid;
		$data['firstname']					= $order['billing_address']['given_name'];
		$data['lastname']					= $order['billing_address']['family_name'];
		$data['email']						= $order['billing_address']['email'];

		$data['telephone']					= $order['billing_address']['phone'];

		$data['payment_firstname']			= $order['billing_address']['given_name'];
		$data['payment_lastname']			= $order['billing_address']['family_name'];

		if (strtolower($order['purchase_country'])=='de') {

			$street_address 				= isset($order['billing_address']['street_name'])			? $order['billing_address']['street_name']				: '';
			$street_number 			  		= isset($order['billing_address']['street_number'])			? $order['billing_address']['street_number']			: '';
			$street							= ($street_number)											? $street_address . ' ' . $street_number				: $street_address;

			$data['payment_address_1']		= isset($order['billing_address']['care_of']) 				? $order['billing_address']['care_of'] 					: $street;
			$data['payment_address_2']		= isset($order['billing_address']['care_of']) 				? $street 												: '';

		}
		else {

			$data['payment_address_1']		= isset($order['billing_address']['care_of']) 				? 'c/o' . $order['billing_address']['care_of'] 			: $order['billing_address']['street_address'];
			$data['payment_address_2']		= isset($order['billing_address']['care_of']) 				? $order['billing_address']['street_address'] 			: '';

		}

		$data['payment_city']				= $order['billing_address']['city'];
		$data['payment_postcode']			= $order['billing_address']['postal_code'];
		$data['payment_country']			= isset($payment_country->row['name']) 						? $payment_country->row['name'] 						: 'NA';
		$data['payment_country_id']			= isset($payment_country->row['country_id']) 				? $payment_country->row['country_id'] 					: 0;
		$data['payment_address_format']		= isset($payment_country->row['address_format']) 			? $payment_country->row['address_format'] 				: NULL;

		$data['shipping_firstname']			= $order['shipping_address']['given_name'];
		$data['shipping_lastname']			= $order['shipping_address']['family_name'];
		$data['shipping_company']			= '';

		if (strtolower($order['purchase_country'])=='de') {

			$street_address 				= isset($order['shipping_address']['street_name'])			? $order['shipping_address']['street_name']				: '';
			$street_number 			  		= isset($order['shipping_address']['street_number'])		? $order['shipping_address']['street_number']			: '';
			$street							= ($street_number)											? $street_address . ' ' . $street_number				: $street_address;

			$data['shipping_address_1']		= isset($order['shipping_address']['care_of']) 				? $order['shipping_address']['care_of'] 				: $street;
			$data['shipping_address_2']		= isset($order['shipping_address']['care_of']) 				? $street 												: '';

		}
		else {

			$data['shipping_address_1']		= isset($order['shipping_address']['care_of']) 				? 'c/o' . $order['shipping_address']['care_of'] 		: $order['shipping_address']['street_address'];
			$data['shipping_address_2']		= isset($order['shipping_address']['care_of']) 				? $order['shipping_address']['street_address'] 			: '';

		}

		$data['shipping_city']				= $order['shipping_address']['city'];
		$data['shipping_postcode']			= $order['shipping_address']['postal_code'];
		$data['shipping_country']			= isset($shipping_country->row['name']) 					? $shipping_country->row['name'] 						: 'NA';
		$data['shipping_country_id']		= isset($shipping_country->row['country_id']) 				? $shipping_country->row['country_id'] 					: 0;
		$data['shipping_address_format']	= isset($shipping_country->row['address_format']) 			? $shipping_country->row['address_format'] 				: NULL;

		// IF NO ORDER, DIE
		if     ($order['status'] == 'checkout_incomplete') 	{ die($this->language->get('error_push_not_complete')); }
		elseif ($order['status'] == 'created') 				{ die($this->language->get('error_push_already_created')); }

		// UPDATE ORDER
		$this->model_klarna_checkout->updateOrder($data);

		// GET CUSTOMER IF EXISTS
		$customer = $this->model_klarna_checkout->getCustomerFromOrder($oid);

		if ((isset($customer['customer_id'])) AND ($customer['customer_id'])) {

			$customer_data = array(
				'customer_id' 	=> $customer['customer_id'],
				'address_id' 	=> $customer['address_id'],
				'firstname' 	=> $data['firstname'],
				'lastname' 		=> $data['lastname'],
				'telephone' 	=> $data['telephone'],
				'address_1'		=> $data['shipping_address_1'],
				'address_2'		=> $data['shipping_address_2'],
				'city'			=> $data['shipping_city'],
				'postcode'		=> $data['shipping_postcode'],
				'country_id'	=> $data['shipping_country_id'],
			);

			$this->model_klarna_checkout->updateCustomer($customer_data);

		}

		// RESET DATA
		$data = array();

		// ADD DATA FOR ORDER_KCO TABLE
		$data['oid']			= isset($oid) 										? $oid									: NULL;
		$data['kid']			= isset($order['id']) 								? $order['id'] 							: NULL;
		$data['reservation']	= isset($order['reservation']) 						? $order['reservation'] 				: NULL;
		$data['reference']		= isset($order['reference']) 						? $order['reference'] 					: NULL;
		$data['status']			= isset($order['status']) 							? $order['status'] 						: NULL;
		$data['country']		= isset($order['purchase_country']) 				? $order['purchase_country'] 			: NULL;
		$data['currency']		= isset($order['purchase_currency']) 				? $order['purchase_currency'] 			: NULL;
		$data['locale']			= isset($order['locale']) 							? $order['locale'] 						: NULL;

		$data['type']			= isset($order['customer']['type']) 				? $order['customer']['type'] 			: NULL;
		$data['date_of_birth']	= isset($order['customer']['date_of_birth']) 		? $order['customer']['date_of_birth'] 	: NULL;
		$data['gender']			= isset($order['customer']['gender']) 				? $order['customer']['gender'] 			: NULL;

		// UPDATE KLARNA ORDER
		$this->model_klarna_checkout->updateKlarnaOrder($data);

		// GET ORDER STATUS FROM CONFIG
		$order_status_id = $this->config->get('kco_order_status_id');

		// GET RES.NO FROM KLARNA
		$comment = sprintf($this->language->get('text_order_comment'), $order['reservation']);

		// CONFIRM ORDER
		$this->model_checkout_order->addOrderHistory($oid, $order_status_id, $comment, true);

		// UPDATE ORDER IN KLARNA SYSTEM
		$update = array();
		$update['status'] = 'created';

		// LETS TRY UPDATE
		try { $order->update($update); }

		// CATCH ERROR
		catch (Exception $e) {

			if ($this->config->get('kco_log_mode')) { file_put_contents($logfile . 'update_fail.txt', print_r($e, true)); }

			die($this->language->get('error_push_update'));

		}

		// SEND HEADER
		header("HTTP/1.1 200 OK");

		// WE ARE DONE NOW!
		exit;

	}

}
