<?php

class NetgiroIframeModuleFrontController extends ModuleFrontController
{
	public $ssl = true;

	public function initContent()
	{
		$this->display_column_left = false;
		$this->display_header = false;
		$this->display_footer = false;
		$this->display_column_right = false;

		parent::initContent();

		$description = '';
		$secretkey = Configuration::get('netgiro_secret_key');
		$params['gateway_url'] = Configuration::get('netgiro_gateway_url');
		$params['ApplicationID'] = Configuration::get('netgiro_application_id');
		if ((int)Configuration::get('netgiro_iframe') == 1) {
			$params['Iframe'] = 'true';
		}
		else {
			$params['Iframe'] = 'false';
		}
		//$params['Iframe'] = (int)Configuration::get('netgiro_iframe') == 1 ? true : false;
		$params['PaymentSuccessfulURL'] = $this->context->link->getModuleLink('netgiro', 'validate');
		$params['PaymentCancelledURL'] = Tools::getShopDomain(true, true).__PS_BASE_URI__.'index.php?controller=order&step=3&multi-shipping=0';
		$params['MaxNumberOfInstallments'] = Configuration::get('netgiro_MaxNumberOfInstallments');

		$params['OrderId'] = $this->context->cart->id;
		$params['ShippingAmount'] = round($this->context->cart->getTotalShippingCost());
		$params['PaymentOption'] = Tools::getValue('paymentoption');

		$products = $this->context->cart->getProducts();
		$discounts = $this->context->cart->getSummaryDetails();
		$item_count = 0;
		$total_calc = $params['ShippingAmount'];
		$items = array();
		foreach ($products as $product)
		{
			$description .= $product['name'] . '<br />';
			$items[$item_count++] = array(
				'ProductNo' => $product['reference'],
				'Name' => $product['name'],
				'Description' => strip_tags($product['description_short']),
				'UnitPrice' => round($product['price_wt']),
				'Amount' => round($product['price_wt']) * (int)$product['cart_quantity'],
				'Quantity' => (int)$product['cart_quantity'] * 1000,
			);
			$total_calc += round($product['price_wt'] * (int)$product['cart_quantity']);
		}
		foreach ($discounts['discounts'] as $discount)
		{
			$items[$item_count++] = array(
				'ProductNo' => $discount['code'],
				'Name' => $discount['code'].' - '.$discount['description'],
				'Description' => strip_tags($discount['description']),
				'UnitPrice' => -1*(round($discount['value_real'])),
				'Amount' => -1*(round($discount['value_real'])),
				'Quantity' => 1000,
			);
		}

		$params['TotalAmount'] = $total_calc;
		$signature_string = utf8_encode($secretkey . $params['OrderId'] . $params['TotalAmount'] . $params['ApplicationID']);
		$params['Signature'] = hash('sha256', $signature_string);
		$params['Items'] = $items;

		$this->context->smarty->assign(array(
			'params' => $params,
			'total' => $this->context->cart->getOrderTotal(true, Cart::BOTH),
			'this_path' => $this->module->getPathUri(),
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/'
		));

		$this->setTemplate('iframe.tpl');
	}
}
