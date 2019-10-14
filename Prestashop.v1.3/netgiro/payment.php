<?php

/* SSL Management */
$useSSL = true;

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/netgiro.php');

if (!$cookie->isLogged())
    Tools::redirect('authentication.php?back=order.php');
$netgiro = new netgiro();
$netgiro->PaymentOption = $_POST['netgiro_payment_type'];

echo $netgiro->execPayment($cart);

include_once(dirname(__FILE__).'/../../footer.php');

?>