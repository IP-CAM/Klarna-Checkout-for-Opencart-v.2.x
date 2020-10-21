<?php

class ControllerKlarnaCheckout extends Controller {

	public $version = '3.0.0.4';

	public function index() {

		$status = true;

		$status = (!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) 		? false : $status;
		$status = (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout')) 	? false : $status;
		$status = (!$this->config->get('kco_status')) 											? false : $status;

		$products = $this->cart->getProducts();

		foreach ($products as $product) { $status = ($product['minimum'] > $product['quantity']) ? false : $status;	}

		if (!$status) { $this->response->redirect($this->url->link('checkout/cart')); }

		$this->load->language('klarna/checkout');
		$this->load->model('klarna/checkout');
		$this->load->model('extension/extension');

		$data['locales'] = array();

		if ($this->config->get('kco_au_status')) { $data['locales'][] = array('title'=>$this->language->get('text_au'), 'value'=>'au-de', 'icon'=>'at.png'); }
		if ($this->config->get('kco_dk_status')) { $data['locales'][] = array('title'=>$this->language->get('text_dk'), 'value'=>'da-dk', 'icon'=>'dk.png'); }
		if ($this->config->get('kco_de_status')) { $data['locales'][] = array('title'=>$this->language->get('text_de'), 'value'=>'de-de', 'icon'=>'de.png'); }
		if ($this->config->get('kco_fi_status')) { $data['locales'][] = array('title'=>$this->language->get('text_fi'), 'value'=>'fi-fi', 'icon'=>'fi.png'); }
		if ($this->config->get('kco_no_status')) { $data['locales'][] = array('title'=>$this->language->get('text_no'), 'value'=>'nb-no', 'icon'=>'no.png'); }
		if ($this->config->get('kco_se_status')) { $data['locales'][] = array('title'=>$this->language->get('text_se'), 'value'=>'sv-se', 'icon'=>'se.png'); }

		$locale = isset($this->session->data['locale']) ? strtolower($this->session->data['locale']) : strtolower($this->config->get('config_language'));

		$this->locales($locale);

		// COUNTRIES WITH SHORT POSTCODE
		$short_postcode = array('au-de', 'da-dk', 'nb-no');

		// SET VALID LENGHT TO POSTOCDE
		$data['valid'] = (in_array($this->session->data['kco_locale'], $short_postcode)) ? 4 : 5;

		$data['icon'] = NULL;
		$data['logo'] = NULL;

		if ($this->request->server['HTTPS']) 	{ $server = $this->config->get('config_ssl'); }
		else 									{ $server = $this->config->get('config_url'); }

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$data['icon'] = $server . 'image/' . $this->config->get('config_icon');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		}

		if (!$this->config->get('kco_logo')) {
			$data['logo'] = NULL;
		}

		$data['status_account']			= $this->config->get('kco_status_account');
		$data['status_coupon']			= $this->config->get('kco_status_coupon');
		$data['status_voucher']			= $this->config->get('kco_status_voucher');
		$data['status_comment']			= $this->config->get('kco_status_comment');
		$data['status_checkout']		= $this->config->get('kco_status_checkout');

		$data['title']					= $this->config->get('config_meta_title');

		$data['base']					= $server;
		$data['description']			= $this->config->get('config_meta_description');
		$data['keywords']				= $this->config->get('config_meta_keyword');
		$data['lang']					= $this->language->get('code');
		$data['direction']				= $this->language->get('direction');
		$data['name']					= $this->config->get('config_name');
		$data['canonical']				= $this->url->link('klarna/checkout');
		$data['version']				= sprintf('v.%s', $this->version);

		$data['home']					= $this->url->link('common/home');
		$data['cart']					= $this->url->link('checkout/cart');

		$data['entry_email']			= $this->language->get('entry_email');
		$data['entry_password']			= $this->language->get('entry_password');
		$data['entry_postcode']			= $this->language->get('entry_postcode');
		$data['entry_new_password']		= $this->language->get('entry_new_password');

		$data['heading_order']			= $this->language->get('heading_order');
		$data['heading_shipping']		= $this->language->get('heading_shipping');
		$data['heading_misc']			= $this->language->get('heading_misc');
		$data['heading_cart']			= $this->language->get('heading_cart');
		$data['heading_payment']		= $this->language->get('heading_payment');

		$data['tooltip_shipping']		= $this->language->get('tooltip_shipping');

		$data['text_back']				= $this->language->get('text_back');
		$data['text_login']				= $this->language->get('text_login');
		$data['text_account']			= $this->language->get('text_account');
		$data['text_coupon']			= $this->language->get('text_coupon');
		$data['text_voucher']			= $this->language->get('text_voucher');
		$data['text_comment']			= $this->language->get('text_comment');
		$data['text_information']		= $this->language->get('text_information');
		$data['text_create_account']	= $this->language->get('text_create_account');
		$data['text_safe_shopping']		= sprintf($this->language->get('text_safe_shopping'), $this->config->get('config_name'));
		$data['text_normal_checkout']	= sprintf($this->language->get('text_normal_checkout'), $this->url->link('checkout/checkout/index'));

		$data['shipping_required']		= $this->cart->hasShipping();
		$data['logged']					= $this->customer->isLogged();
		$data['comment']				= (isset($this->session->data['comment'])) ? $this->session->data['comment'] : NULL;
		$data['account']				= (isset($this->session->data['account'])) ? $this->session->data['account'] : NULL;

		if ($this->customer->isLogged()) {

			$data['text_logout'] = sprintf($this->language->get('text_logout'), $this->customer->getFirstName());

			if ($this->customer->getEmail()) {
				$this->session->data['kco_email'] = $this->customer->getEmail();
			}

			if ($this->customer->getAddressId()) {
				$this->session->data['kco_postcode'] = $this->model_klarna_checkout->getPostcode($this->customer->getAddressId());
			}

		}

		$data['kco_email'] 			= isset($this->session->data['kco_email']) 		? $this->session->data['kco_email'] 		: NULL;
		$data['kco_postcode'] 		= isset($this->session->data['kco_postcode']) 	? $this->session->data['kco_postcode'] 		: NULL;

		// GOOGLE ANALYTICS

		// EMPTY ARRAY
		$data['analytics'] = array();

		// GET EXTENSIONS
		$analytics = $this->model_extension_extension->getExtensions('analytics');

		// LOOP THEM AND ADD ENABLED EXTENSIONS TO OUR ARRAY
		foreach ($analytics as $analytic) {
			if ($this->config->get($analytic['code'] . '_status')) {
				$data['analytics'][] = $this->load->controller('analytics/' . $analytic['code'], $this->config->get($analytic['code'] . '_status'));
			}
		}

		// OUTPUT
		$this->response->setOutput($this->load->view('klarna/checkout', $data));

	}

	public function locale() {

		$json = array();

		$this->session->data['locale'] = (isset($this->request->post['locale'])) ? $this->request->post['locale'] : NULL;

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

	private function locales($locale=NULL) {

		$locale = ($locale) ? $locale : 'sv-se';

		if ($locale=='au-de' || $locale=='au') {
			$settings = array(
				'eid'			=> (int)$this->config->get('kco_au_eid'),
				'secret'		=> trim($this->config->get('kco_au_secret')),
				'locale'		=> 'au-de',
				'currency'		=> 'EUR',
				'country'		=> 'AU',
				'country_id'	=> (int)$this->config->get('kco_au_country_id'),
			);
		}
		elseif ($locale=='da-dk' || $locale=='dk' || $locale=='da') {
			$settings = array(
				'eid'			=> (int)$this->config->get('kco_dk_eid'),
				'secret'		=> trim($this->config->get('kco_dk_secret')),
				'locale'		=> 'da-dk',
				'currency'		=> 'EUR',
				'country'		=> 'DK',
				'country_id'	=> (int)$this->config->get('kco_dk_country_id'),
			);
		}
		elseif ($locale=='de-de' || $locale=='de') {
			$settings = array(
				'eid'			=> (int)$this->config->get('kco_de_eid'),
				'secret'		=> trim($this->config->get('kco_de_secret')),
				'locale'		=> 'de-de',
				'currency'		=> 'EUR',
				'country'		=> 'DE',
				'country_id'	=> (int)$this->config->get('kco_de_country_id'),
			);
		}
		elseif ($locale=='fi-fi' || $locale=='fi') {
			$settings = array(
				'eid'			=> (int)$this->config->get('kco_fi_eid'),
				'secret'		=> trim($this->config->get('kco_fi_secret')),
				'locale'		=> 'fi-fi',
				'currency'		=> 'EUR',
				'country'		=> 'FI',
				'country_id'	=> (int)$this->config->get('kco_fi_country_id'),
			);
		}
		elseif ($locale=='nb-no' || $locale=='no' || $locale=='nn') {
			$settings = array(
				'eid'			=> (int)$this->config->get('kco_no_eid'),
				'secret'		=> trim($this->config->get('kco_no_secret')),
				'locale'		=> 'nb-no',
				'currency'		=> 'NOK',
				'country'		=> 'NO',
				'country_id'	=> (int)$this->config->get('kco_no_country_id'),
			);
		}
		elseif ($locale=='sv-se' || $locale=='se' || $locale=='sv') {
			$settings = array(
				'eid'			=> (int)$this->config->get('kco_se_eid'),
				'secret'		=> trim($this->config->get('kco_se_secret')),
				'locale'		=> 'sv-se',
				'currency'		=> 'SEK',
				'country'		=> 'SE',
				'country_id'	=> (int)$this->config->get('kco_se_country_id'),
			);
		} else {
			$settings = array(
				'eid'			=> (int)$this->config->get('kco_se_eid'),
				'secret'		=> trim($this->config->get('kco_se_secret')),
				'locale'		=> 'sv-se',
				'currency'		=> 'SEK',
				'country'		=> 'SE',
				'country_id'	=> (int)$this->config->get('kco_se_country_id'),
			);
		}

		if ($this->config->get('kco_test_mode')) {
			$settings['eid'] 	= '200';
			$settings['secret'] = 'test';
		}

		if (isset($this->session->data['kco_eid']) AND ($this->session->data['kco_eid']!=$settings['eid'])) {
			$this->session->data['kco_order_id'] = NULL;
		}

		$this->session->data['kco_eid']			= $settings['eid'];
		$this->session->data['kco_secret']		= $settings['secret'];
		$this->session->data['kco_locale']		= $settings['locale'];
		$this->session->data['kco_currency']	= $settings['currency'];
		$this->session->data['kco_country']		= $settings['country'];
		$this->session->data['kco_country_id']	= $settings['country_id'];

		$this->session->data['currency']		= $settings['currency'];

	}

}
