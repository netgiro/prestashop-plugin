<?php

/* SSL Management */
$useSSL = true;

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/netgiro.php');

$netgiro = new netgiro();
$cart = new Cart(intval($cookie->id_cart));

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
$params['PaymentSuccessfulURL'] = Tools::getHttpHost(true, true).__PS_BASE_URI__.'modules/'.$netgiro->name.'/validate.php';
$params['PaymentCancelledURL'] = Tools::getHttpHost(true, true).__PS_BASE_URI__.'order.php?step=2';
$params['MaxNumberOfInstallments'] = Configuration::get('netgiro_MaxNumberOfInstallments');

$params['OrderId'] = intval($cart->id);
$params['ShippingAmount'] = round($cart->getOrderShippingCost());
$params['PaymentOption'] = $_GET['PaymentOption'];

$products = $cart->getProducts();
$discounts = $cart->getDiscounts();
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
foreach ($discounts as $discount)
{
	$items[$item_count++] = array(
		'ProductNo' => $discount['name'],
		'Name' => $discount['name'].' - '.$discount['description'],
		'Description' => strip_tags($discount['description']),
		'UnitPrice' => -1*(round($discount['value'])),
		'Amount' => -1*(round($discount['value'])),
		'Quantity' => 1000,
	);
}

$params['TotalAmount'] = $total_calc;
$signature_string = utf8_encode($secretkey . $params['OrderId'] . $params['TotalAmount'] . $params['ApplicationID']);
$params['Signature'] = hash('sha256', $signature_string);
$params['Items'] = $items;

$smarty->assign(array(
	'params' => $params,
	'total' => $cart->getOrderTotal(true, 3),
));

if (is_file(_PS_THEME_DIR_.'modules/netgiro/iframe.tpl'))
	$smarty->display(_PS_THEME_DIR_.'modules/'.$netgiro->name.'/iframe.tpl');
else
	$smarty->display(_PS_MODULE_DIR_.$netgiro->name.'/iframe.tpl');
?>