<?php

class NetgiroValidateModuleFrontController extends ModuleFrontController
{
	public $ssl = true;

	public function postProcess()
	{
		$secretkey = Configuration::get('netgiro_secret_key');
		$ApplicationID = Configuration::get('netgiro_application_id');

		$orderid = pSQL(Tools::getValue('orderid'));
		$confirmationCode =  pSQL(Tools::getValue('confirmationCode'));
		$invoiceNumber =  pSQL(Tools::getValue('invoiceNumber'));
		$signature =  pSQL(Tools::getValue('signature'));

		$hash_string = utf8_encode($secretkey . $orderid);
		$hash_result = hash('sha256', $hash_string);

		if ($hash_result === $signature)
		{
			$cart = $this->context->cart;
			if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
				Tools::redirect('index.php?controller=order&step=1');

			// Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
			$authorized = false;
			foreach (Module::getPaymentModules() as $module)
				if ($module['name'] == 'netgiro')
				{
					$authorized = true;
					break;
				}
			if (!$authorized)
				die($this->module->l('This payment method is not available.', 'validation'));

			$customer = new Customer($cart->id_customer);
			if (!Validate::isLoadedObject($customer))
				Tools::redirect('index.php?controller=order&step=1');

			$currency = $this->context->currency;
			$total = (float)$cart->getOrderTotal(true, Cart::BOTH);

			if (!$cart->OrderExists())
			{
				$this->module->validateOrder($cart->id, Configuration::get('PS_OS_PAYMENT'), $total, 'Netgiro', null, array(), (int)$currency->id, false, $customer->secure_key);
				$order = new Order(Order::getOrderByCartId($cart->id));
				$db_prefix = _DB_PREFIX_;
				$id_order = intval($order->id);
				$cart_id = intval($cart->id);
				$invoice_id = intval($order->invoice_number);

				$SQL = <<<SQL
INSERT INTO `{$db_prefix}order_netgiro` (`id_order`, `cart_id`, `invoice_id`, `netgiro_confirmation_code`, `netgiro_invoice_number`, `netgiro_signature`)
VALUES("{$id_order}", "{$cart_id}", "{$invoice_id}", "{$confirmationCode}", "{$invoiceNumber}", "{$signature}");
SQL;
				Db::getInstance()->Execute($SQL);
			}
			Tools::redirect('index.php?controller=order-confirmation&id_cart='.$cart->id.'&id_module='.$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$order->secure_key);
		}
	}
}