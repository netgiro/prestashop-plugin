<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__) . '/netgiro.php');

$netgiro = new netgiro();
//echo $korta->execPayment($cart);

include_once(dirname(__FILE__).'/../../footer.php');

Tools::redirect(Context::getContext()->link->getModuleLink('netgiro', 'payment'));
?>