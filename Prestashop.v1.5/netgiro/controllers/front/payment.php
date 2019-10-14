<?php

class NetgiroPaymentModuleFrontController extends ModuleFrontController
{
	public $ssl = true;

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		$this->display_column_left = false;
		$this->display_column_right = false;

		parent::initContent();

		$PaymentOption = $_POST['netgiro_payment_type'];
		$iframelink = '&paymentoption='.$PaymentOption;

		$this->context->smarty->assign(array(
			'iframelink' => $iframelink,
			'paymentoption' => $PaymentOption,
			'total' => $this->context->cart->getOrderTotal(true),
			'this_path' => $this->module->getPathUri(),
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/',
			'iframe' => (int)Configuration::get('netgiro_iframe') == 1 ? true : false,
		));

		// Wrapping fees
		$wrapping_fees = $this->context->cart->getGiftWrappingPrice(false);
		$wrapping_fees_tax_inc = $wrapping_fees = $this->context->cart->getGiftWrappingPrice();

		// TOS
		$cms = new CMS(Configuration::get('PS_CONDITIONS_CMS_ID'), $this->context->language->id);
		$this->link_conditions = $this->context->link->getCMSLink($cms, $cms->link_rewrite, false);
		if (!strpos($this->link_conditions, '?'))
			$this->link_conditions .= '?content_only=1';
		else
			$this->link_conditions .= '&content_only=1';

		$free_shipping = false;
		foreach ($this->context->cart->getCartRules() as $rule)
		{
			if ($rule['free_shipping'] && !$rule['carrier_restriction'])
			{
				$free_shipping = true;
				break;
			}
		}
		$this->context->smarty->assign(array(
			'free_shipping' => $free_shipping,
			'checkedTOS' => (int)($this->context->cookie->checkedTOS),
			'recyclablePackAllowed' => (int)(Configuration::get('PS_RECYCLABLE_PACK')),
			'giftAllowed' => (int)(Configuration::get('PS_GIFT_WRAPPING')),
			'cms_id' => (int)(Configuration::get('PS_CONDITIONS_CMS_ID')),
			'conditions' => (int)(Configuration::get('PS_CONDITIONS')),
			'link_conditions' => $this->link_conditions,
			'recyclable' => (int)($this->context->cart->recyclable),
			'delivery_option_list' => $this->context->cart->getDeliveryOptionList(),
			'carriers' => $this->context->cart->simulateCarriersOutput(),
			'checked' => $this->context->cart->simulateCarrierSelectedOutput(),
			'address_collection' => $this->context->cart->getAddressCollection(),
			'delivery_option' => $this->context->cart->getDeliveryOption(null, false),
			'gift_wrapping_price' => (float)$wrapping_fees,
			'total_wrapping_cost' => Tools::convertPrice($wrapping_fees_tax_inc, $this->context->currency),
			'total_wrapping_tax_exc_cost' => Tools::convertPrice($wrapping_fees, $this->context->currency))
		);

		$this->context->smarty->assign(array(
			'token_cart' => Tools::getToken(false),
			'isVirtualCart' => $this->context->cart->isVirtualCart(),
			'productNumber' => $this->context->cart->nbProducts(),
			'voucherAllowed' => CartRule::isFeatureActive(),
			'shippingCost' => $this->context->cart->getOrderTotal(true, Cart::ONLY_SHIPPING),
			'shippingCostTaxExc' => $this->context->cart->getOrderTotal(false, Cart::ONLY_SHIPPING),
			'CUSTOMIZE_FILE' => Product::CUSTOMIZE_FILE,
			'CUSTOMIZE_TEXTFIELD' => Product::CUSTOMIZE_TEXTFIELD,
			'lastProductAdded' => $this->context->cart->getLastProduct(),
			'currencySign' => $this->context->currency->sign,
			'currencyRate' => $this->context->currency->conversion_rate,
			'currencyFormat' => $this->context->currency->format,
			'currencyBlank' => $this->context->currency->blank,
		));

		$this->context->smarty->assign($this->context->cart->getSummaryDetails());

		$this->setTemplate('payment.tpl');
	}
}
