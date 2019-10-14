<?php

/* SSL Management */
$useSSL = true;

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/netgiro.php');

$netgiro = new netgiro();

$secretkey = Configuration::get('netgiro_secret_key');
$ApplicationID = Configuration::get('netgiro_application_id');

$orderid = mysql_real_escape_string($_GET['orderid']);
$confirmationCode = mysql_real_escape_string($_GET['confirmationCode']);
$invoiceNumber = mysql_real_escape_string($_GET['invoiceNumber']);
$signature = mysql_real_escape_string($_GET['signature']);

$hash_string = utf8_encode($secretkey . $orderid);
$hash_result = hash('sha256', $hash_string);

if ($hash_result === $signature)
{
	$cart = new Cart(intval($cookie->id_cart));
	if ($cart->id_customer == 0 OR $cart->id_address_delivery == 0 OR $cart->id_address_invoice == 0 OR !$netgiro->active)
		Tools::redirect(__PS_BASE_URI__.'order.php?step=1');

	// Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
	$authorized = false;
	if (Module::isInstalled('netgiro'))
	{
		$authorized = true;
	}
	if (!$authorized)
		die($netgiro->module->l('This payment method is not available.', 'validation'));

	$customer = new Customer($cart->id_customer);
	if (!Validate::isLoadedObject($customer))
		Tools::redirect('index.php?controller=order&step=1');

	$total = floatval($cart->getOrderTotal(true, 3));
	if (!$cart->OrderExists())
	{
		$netgiro->validateOrder($cart->id, _PS_OS_PAYMENT_, $total, $netgiro->displayName, NULL, array(), $currency->id);
		$order = new Order($netgiro->currentOrder);
		$db_prefix = _DB_PREFIX_;
		$id_order = intval($order->id);
		$cart_id = intval($cart->id);
		$invoice_id = intval($order->invoice_number);
		$query = "
		INSERT INTO `".$db_prefix."order_netgiro` (`id_order`, `cart_id`, `invoice_id`, `netgiro_confirmation_code`, `netgiro_invoice_number`, `netgiro_signature`)
		VALUES(\"".$id_order."\", \"".$cart_id."\", \"".$invoice_id."\", \"".$confirmationCode."\", \"".$invoiceNumber."\", \"".$signature."\")";
		Db::getInstance()->Execute($query);
	}
	Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?id_cart='.$cart->id.'&id_module='.$netgiro->id.'&id_order='.$netgiro->currentOrder.'&key='.$order->secure_key);

}
?>