<?php

class ModelKlarnaCheckout extends Model {

	public function getCustomersByEmail($email) {

		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer` WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return (isset($query->row['total'])) ? $query->row['total'] : NULL;

	}

	public function getCustomerFromOrder($order_id) {

		$result = $this->db->query("
			SELECT t1.customer_id, t2.address_id FROM `" . DB_PREFIX . "order` AS t1
			LEFT JOIN `" . DB_PREFIX . "customer` AS t2 ON t1.customer_id = t2.customer_id
			WHERE order_id = '".(int)$order_id."'
			LIMIT 1
		");

		return (isset($result->row['customer_id'])) ? $result->row : false;

	}

	public function getPostcode($address_id) {

		$query = $this->db->query("SELECT postcode FROM `" . DB_PREFIX . "address` WHERE address_id = '" . (int)$address_id . "' LIMIT 1");

		return (isset($query->row['postcode'])) ? $query->row['postcode'] : NULL;

	}

	public function getCountry($iso_code) {

		$query = $this->db->query("SELECT country_id, name, address_format FROM `" . DB_PREFIX . "country` WHERE iso_code_2 = '" . $this->db->escape($iso_code) . "' LIMIT 1");

		return $query;

	}

	public function getKlarnaOrder($order_id) {

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_kco` WHERE oid = '" . (int)$order_id . "' LIMIT 1");

		return (isset($query->row['oid'])) ? $query->row : NULL;

	}

	public function getGAOrder($order_id) {

		$query = $this->db->query("
			SELECT
				(SELECT store_name FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "') AS store_name,
				(SELECT SUM(`value`) FROM `" . DB_PREFIX . "order_total` WHERE `code` = 'total' AND order_id = '" . (int)$order_id . "') AS revenue,
				(SELECT SUM(`value`) FROM `" . DB_PREFIX . "order_total` WHERE `code` = 'shipping' AND order_id = '" . (int)$order_id . "') AS shipping,
				(SELECT SUM(`value`) FROM `" . DB_PREFIX . "order_total` WHERE `code` = 'tax' AND order_id = '" . (int)$order_id . "') AS tax
			LIMIT 1
		");

		return (isset($query->row['revenue'])) ? $query->row : NULL;

	}

	public function getGAProducts($order_id) {

		$query = $this->db->query("SELECT product_id, name, model AS sku, (price + tax) AS price, quantity FROM `" . DB_PREFIX . "order_product` WHERE order_id = '" . (int)$order_id . "'");

		return (isset($query->row['product_id'])) ? $query->rows : NULL;

	}

	public function updateCustomer($data) {

		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET
			firstname	= '".$this->db->escape($data['firstname'])."',
			lastname	= '".$this->db->escape($data['lastname'])."',
			telephone	= '".$this->db->escape($data['telephone'])."'
			WHERE customer_id = '".(int)$data['customer_id']."' LIMIT 1
		");

		if ($data['address_id']) {

			$this->db->query("UPDATE `" . DB_PREFIX . "address` SET
				firstname	= '".$this->db->escape($data['firstname'])."',
				lastname	= '".$this->db->escape($data['lastname'])."',
				address_1	= '".$this->db->escape($data['address_1'])."',
				address_2	= '".$this->db->escape($data['address_2'])."',
				city		= '".$this->db->escape($data['city'])."',
				postcode	= '".$this->db->escape($data['postcode'])."',
				country_id	= '".(int)$data['country_id']."',
				zone_id		= '0'
				WHERE customer_id = '".(int)$data['customer_id']."' AND address_id = '".(int)$data['address_id']."' LIMIT 1
			");

		}

		return true;

	}

	public function addComment($order_id, $comment) {

		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET comment = '" . $this->db->escape($comment) . "' WHERE order_id = '" . (int)$order_id . "' LIMIT 1");

	}

	public function addKlarnaOrder($order_id, $eid, $secret, $locale) {

		$this->db->query("
			INSERT INTO `" . DB_PREFIX . "order_kco` SET
			oid 					= '" . (int)$order_id . "',
			eid 					= '" . (int)$eid . "',
			secret 					= '" . $this->db->escape($secret) . "',
			locale 					= '" . $this->db->escape($locale) . "',
			date_added				= NOW(),
			date_modified 			= NOW()

			ON DUPLICATE KEY UPDATE
			eid 					= '" . (int)$eid . "',
			secret 					= '" . $this->db->escape($secret) . "',
			locale 					= '" . $this->db->escape($locale) . "',
			date_modified 			= NOW()
		");

	}

	public function updateKlarnaOrder($data) {

		$this->db->query("
			UPDATE `" . DB_PREFIX . "order_kco` SET
			kid 				= '" . $this->db->escape($data['kid']) . "',
			reservation 		= '" . $this->db->escape($data['reservation']) . "',
			reference 			= '" . $this->db->escape($data['reference']) . "',
			status 				= 'created',
			type 				= '" . $this->db->escape($data['type']) . "',
			date_of_birth 		= '" . $this->db->escape($data['date_of_birth']) . "',
			gender 				= '" . $this->db->escape($data['gender']) . "',
			country				= '" . $this->db->escape($data['country']) . "',
			currency			= '" . $this->db->escape($data['currency']) . "',
			locale				= '" . $this->db->escape($data['locale']) . "',
			date_created 		= NOW(),
			date_modified 		= NOW()

			WHERE oid 			= '" . (int)$data['oid'] . "'
			LIMIT 1
		");

		return true;

	}

	public function updateOrder($data) {

		$this->db->query("
		UPDATE `" . DB_PREFIX . "order` SET

		firstname 				= '" . $this->db->escape($data['firstname']) . "',
		lastname				= '" . $this->db->escape($data['lastname']) . "',
		email 					= '" . $this->db->escape($data['email']) . "',
		telephone 				= '" . $this->db->escape($data['telephone']) . "',

		payment_firstname 		= '" . $this->db->escape($data['payment_firstname']) . "',
		payment_lastname 		= '" . $this->db->escape($data['payment_lastname']) . "',
		payment_address_1 		= '" . $this->db->escape($data['payment_address_1']) . "',
		payment_address_2 		= '" . $this->db->escape($data['payment_address_2']) . "',
		payment_city 			= '" . $this->db->escape($data['payment_city']) . "',
		payment_postcode 		= '" . $this->db->escape($data['payment_postcode']) . "',
		payment_country 		= '" . $this->db->escape($data['payment_country']) . "',
		payment_country_id 		= '" . (int)$data['payment_country_id'] . "',
		payment_address_format	= '" . $this->db->escape($data['payment_address_format']) . "',

		shipping_firstname 		= '" . $this->db->escape($data['shipping_firstname']) . "',
		shipping_lastname 		= '" . $this->db->escape($data['shipping_lastname']) . "',
		shipping_company 		= '" . $this->db->escape($data['shipping_company']) . "',
		shipping_address_1 		= '" . $this->db->escape($data['shipping_address_1']) . "',
		shipping_address_2 		= '" . $this->db->escape($data['shipping_address_2']) . "',
		shipping_city 			= '" . $this->db->escape($data['shipping_city']) . "',
		shipping_postcode 		= '" . $this->db->escape($data['shipping_postcode']) . "',
		shipping_country 		= '" . $this->db->escape($data['shipping_country']) . "',
		shipping_country_id 	= '" . (int)$data['shipping_country_id'] . "',
		shipping_address_format	= '" . $this->db->escape($data['shipping_address_format']) . "',

		date_modified 			= NOW()

		WHERE order_id 			= '" . (int)$data['order_id'] . "' LIMIT 1");

		return true;

	}

	public function addOrder($order) {

		$order_id = (int)$order['order_id'];

		if  ($order_id) {

			$this->db->query("DELETE FROM `" . DB_PREFIX . "order_option` WHERE order_id = '" . (int)$order_id . "'");
			$this->db->query("DELETE FROM `" . DB_PREFIX . "order_product` WHERE order_id = '" . (int)$order_id . "'");
			$this->db->query("DELETE FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = '" . (int)$order_id . "'");
			$this->db->query("DELETE FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "'");

		}

		$this->db->query("
			INSERT INTO `" . DB_PREFIX . "order` SET
			order_id 					= '" . (int)$order['order_id'] . "',
			invoice_prefix 				= '" . $this->db->escape($order['invoice_prefix']) . "',
			store_id 					= '" . (int)$order['store_id'] . "',
			store_name 					= '" . $this->db->escape($order['store_name']) . "',
			store_url 					= '" . $this->db->escape($order['store_url']) . "',
			customer_id 				= '" . (int)$order['customer_id'] . "',
			customer_group_id 			= '" . (int)$order['customer_group_id'] . "',
			firstname 					= '" . $this->db->escape($order['firstname']) . "',
			lastname 					= '" . $this->db->escape($order['lastname']) . "',
			email 						= '" . $this->db->escape($order['email']) . "',
			telephone 					= '" . $this->db->escape($order['telephone']) . "',
			payment_firstname 			= '" . $this->db->escape($order['payment_firstname']) . "',
			payment_lastname 			= '" . $this->db->escape($order['payment_lastname']) . "',
			payment_company 			= '" . $this->db->escape($order['payment_company']) . "',
			payment_address_1 			= '" . $this->db->escape($order['payment_address_1']) . "',
			payment_address_2 			= '" . $this->db->escape($order['payment_address_2']) . "',
			payment_city 				= '" . $this->db->escape($order['payment_city']) . "',
			payment_postcode 			= '" . $this->db->escape($order['payment_postcode']) . "',
			payment_country 			= '" . $this->db->escape($order['payment_country']) . "',
			payment_country_id 			= '" . (int)$order['payment_country_id'] . "',
			payment_zone 				= '" . $this->db->escape($order['payment_zone']) . "',
			payment_zone_id 			= '" . (int)$order['payment_zone_id'] . "',
			payment_address_format 		= '" . $this->db->escape($order['payment_address_format']) . "',
			payment_method 				= '" . $this->db->escape($order['payment_method']) . "',
			payment_code 				= '" . $this->db->escape($order['payment_code']) . "',
			shipping_firstname 			= '" . $this->db->escape($order['shipping_firstname']) . "',
			shipping_lastname 			= '" . $this->db->escape($order['shipping_lastname']) . "',
			shipping_company 			= '" . $this->db->escape($order['shipping_company']) . "',
			shipping_address_1 			= '" . $this->db->escape($order['shipping_address_1']) . "',
			shipping_address_2 			= '" . $this->db->escape($order['shipping_address_2']) . "',
			shipping_city 				= '" . $this->db->escape($order['shipping_city']) . "',
			shipping_postcode 			= '" . $this->db->escape($order['shipping_postcode']) . "',
			shipping_country 			= '" . $this->db->escape($order['shipping_country']) . "',
			shipping_country_id 		= '" . (int)$order['shipping_country_id'] . "',
			shipping_zone 				= '" . $this->db->escape($order['shipping_zone']) . "',
			shipping_zone_id 			= '" . (int)$order['shipping_zone_id'] . "',
			shipping_address_format 	= '" . $this->db->escape($order['shipping_address_format']) . "',
			shipping_method 			= '" . $this->db->escape($order['shipping_method']) . "',
			shipping_code 				= '" . $this->db->escape($order['shipping_code']) . "',
			comment 					= '" . $this->db->escape($order['comment']) . "',
			total 						= '" . (float)$order['total'] . "',
			affiliate_id 				= '" . (int)$order['affiliate_id'] . "',
			commission 					= '" . (float)$order['commission'] . "',
			marketing_id 				= '" . (int)$order['marketing_id'] . "',
			tracking 					= '" . $this->db->escape($order['tracking']) . "',
			language_id 				= '" . (int)$order['language_id'] . "',
			currency_id 				= '" . (int)$order['currency_id'] . "',
			currency_code 				= '" . $this->db->escape($order['currency_code']) . "',
			currency_value 				= '" . (float)$order['currency_value'] . "',
			ip 							= '" . $this->db->escape($order['ip']) . "',
			forwarded_ip 				= '" . $this->db->escape($order['forwarded_ip']) . "',
			user_agent 					= '" . $this->db->escape($order['user_agent']) . "',
			accept_language 			= '" . $this->db->escape($order['accept_language']) . "',
			date_added					= NOW(),
			date_modified 				= NOW()

			ON DUPLICATE KEY UPDATE

			invoice_prefix 				= '" . $this->db->escape($order['invoice_prefix']) . "',
			store_id 					= '" . (int)$order['store_id'] . "',
			store_name 					= '" . $this->db->escape($order['store_name']) . "',
			store_url 					= '" . $this->db->escape($order['store_url']) . "',
			customer_id 				= '" . (int)$order['customer_id'] . "',
			customer_group_id 			= '" . (int)$order['customer_group_id'] . "',
			firstname 					= '" . $this->db->escape($order['firstname']) . "',
			lastname 					= '" . $this->db->escape($order['lastname']) . "',
			email 						= '" . $this->db->escape($order['email']) . "',
			telephone 					= '" . $this->db->escape($order['telephone']) . "',
			payment_firstname 			= '" . $this->db->escape($order['payment_firstname']) . "',
			payment_lastname 			= '" . $this->db->escape($order['payment_lastname']) . "',
			payment_company 			= '" . $this->db->escape($order['payment_company']) . "',
			payment_address_1 			= '" . $this->db->escape($order['payment_address_1']) . "',
			payment_address_2 			= '" . $this->db->escape($order['payment_address_2']) . "',
			payment_city 				= '" . $this->db->escape($order['payment_city']) . "',
			payment_postcode 			= '" . $this->db->escape($order['payment_postcode']) . "',
			payment_country 			= '" . $this->db->escape($order['payment_country']) . "',
			payment_country_id 			= '" . (int)$order['payment_country_id'] . "',
			payment_zone 				= '" . $this->db->escape($order['payment_zone']) . "',
			payment_zone_id 			= '" . (int)$order['payment_zone_id'] . "',
			payment_address_format 		= '" . $this->db->escape($order['payment_address_format']) . "',
			payment_method 				= '" . $this->db->escape($order['payment_method']) . "',
			payment_code 				= '" . $this->db->escape($order['payment_code']) . "',
			shipping_firstname 			= '" . $this->db->escape($order['shipping_firstname']) . "',
			shipping_lastname 			= '" . $this->db->escape($order['shipping_lastname']) . "',
			shipping_company 			= '" . $this->db->escape($order['shipping_company']) . "',
			shipping_address_1 			= '" . $this->db->escape($order['shipping_address_1']) . "',
			shipping_address_2 			= '" . $this->db->escape($order['shipping_address_2']) . "',
			shipping_city 				= '" . $this->db->escape($order['shipping_city']) . "',
			shipping_postcode 			= '" . $this->db->escape($order['shipping_postcode']) . "',
			shipping_country 			= '" . $this->db->escape($order['shipping_country']) . "',
			shipping_country_id 		= '" . (int)$order['shipping_country_id'] . "',
			shipping_zone 				= '" . $this->db->escape($order['shipping_zone']) . "',
			shipping_zone_id 			= '" . (int)$order['shipping_zone_id'] . "',
			shipping_address_format 	= '" . $this->db->escape($order['shipping_address_format']) . "',
			shipping_method 			= '" . $this->db->escape($order['shipping_method']) . "',
			shipping_code 				= '" . $this->db->escape($order['shipping_code']) . "',
			comment 					= '" . $this->db->escape($order['comment']) . "',
			total 						= '" . (float)$order['total'] . "',
			commission 					= '" . (float)$order['commission'] . "',
			tracking 					= '" . $this->db->escape($order['tracking']) . "',
			language_id 				= '" . (int)$order['language_id'] . "',
			currency_id 				= '" . (int)$order['currency_id'] . "',
			currency_code 				= '" . $this->db->escape($order['currency_code']) . "',
			currency_value 				= '" . (float)$order['currency_value'] . "',
			date_modified 				= NOW()

		");

		$order_id = (!$order_id) ? $this->db->getLastId() : $order_id;

		if (isset($order['products'])) {

			foreach ($order['products'] as $product) {

				$this->db->query("
					INSERT INTO `" . DB_PREFIX . "order_product` SET
					order_id	= '" . (int)$order_id . "',
					product_id	= '" . (int)$product['product_id'] . "',
					name		= '" . $this->db->escape($product['name']) . "',
					model		= '" . $this->db->escape($product['model']) . "',
					quantity	= '" . (int)$product['quantity'] . "',
					price		= '" . (float)$product['price'] . "',
					total		= '" . (float)$product['total'] . "',
					tax			= '" . (float)$product['tax'] . "',
					reward		= '" . (int)$product['reward'] . "'
				");

				$order_product_id = $this->db->getLastId();

				foreach ($product['option'] as $option) {
					$this->db->query("
						INSERT INTO `" . DB_PREFIX . "order_option` SET
						order_id				= '" . (int)$order_id . "',
						order_product_id		= '" . (int)$order_product_id . "',
						product_option_id		= '" . (int)$option['product_option_id'] . "',
						product_option_value_id	= '" . (int)$option['product_option_value_id'] . "',
						name					= '" . $this->db->escape($option['name']) . "',
						`value`					= '" . $this->db->escape($option['value']) . "',
						`type`					= '" . $this->db->escape($option['type']) . "'
					");
				}

			}

		}

		$this->load->model('extension/total/voucher');

		$this->model_extension_total_voucher->disableVoucher($order_id);

		if (isset($order['vouchers'])) {

			foreach ($order['vouchers'] as $voucher) {

				$this->db->query("
					INSERT INTO `" . DB_PREFIX . "order_voucher` SET
					order_id			= '" . (int)$order_id . "',
					description			= '" . $this->db->escape($voucher['description']) . "',
					code				= '" . $this->db->escape($voucher['code']) . "',
					from_name			= '" . $this->db->escape($voucher['from_name']) . "',
					from_email			= '" . $this->db->escape($voucher['from_email']) . "',
					to_name				= '" . $this->db->escape($voucher['to_name']) . "',
					to_email			= '" . $this->db->escape($voucher['to_email']) . "',
					voucher_theme_id	= '" . (int)$voucher['voucher_theme_id'] . "',
					message				= '" . $this->db->escape($voucher['message']) . "',
					amount				= '" . (float)$voucher['amount'] . "'
				");

				$order_voucher_id = $this->db->getLastId();

				$voucher_id = $this->model_extensio_total_voucher->addVoucher($order_id, $voucher);

				$this->db->query("UPDATE `" . DB_PREFIX . "order_voucher` SET voucher_id = '" . (int)$voucher_id . "' WHERE order_voucher_id = '" . (int)$order_voucher_id . "'");

			}

		}

		if (isset($order['totals'])) {

			foreach ($order['totals'] as $total) {

				$this->db->query("
					INSERT INTO `" . DB_PREFIX . "order_total` SET
					order_id	= '" . (int)$order_id . "',
					code		= '" . $this->db->escape($total['code']) . "',
					title		= '" . $this->db->escape($total['title']) . "',
					`value`		= '" . (float)$total['value'] . "',
					sort_order	= '" . (int)$total['sort_order'] . "'
				");

			}

		}

		return $order_id;

	}

}

?>
