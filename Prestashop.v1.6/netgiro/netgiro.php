<?php

	class netgiro extends PaymentModule
	{
		private $_html = '';
		private $_postErrors = array();
		private $key;

		function __construct()
		{
			$this->name = 'netgiro';
			$this->tab = 'payments_gateways';
			$this->version = '0.9';
            $this->author = 'Netgíró';
			$this->displayName = 'Netgíró';

			$this->currencies = true;
			$this->currencies_mode = 'checkbox';

			$this->PaymentOption = '1';
	
			parent::__construct();

			$this->page = basename(__FILE__, '.php');
			$this->displayName = $this->l('netgiro');
			$this->description = $this->l('Accepts payments using Netgíró online payments.');
			$this->confirmUninstall = $this->l('Are you sure you want to delete Netgíró details?');
		}

		public function install()
		{
			if (!parent::install() || !$this->registerHook('invoice') || !$this->registerHook('payment') || !$this->registerHook('paymentReturn'))
			{
				return false;
			}

			$db = Db::getInstance();
			$query = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."order_netgiro` (
			`id_payment` int(11) NOT NULL AUTO_INCREMENT,
			`id_order` int(11) NOT NULL,
			`cart_id` int(11) NOT NULL,
			`invoice_id` varchar(255) NOT NULL,
			`netgiro_confirmation_code` varchar(255) NOT NULL,
			`netgiro_invoice_number` varchar(255) NOT NULL,
			`netgiro_signature` varchar(255) NOT NULL,
			`timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id_payment`),
			UNIQUE KEY `invoice_id` (`invoice_id`)
			) ENGINE="._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

			$db->Execute($query);

			return true;
		}

		public function uninstall()
		{
			return parent::uninstall();
		}

		function hookPayment($params)
		{
			if (!$this->active)
				return ;

			//global $smarty;

            $this->smarty->assign(array(
			'netgiro_application_id' => Configuration::get('netgiro_application_id'),
			'netgiro_method1' => (Configuration::get('netgiro_PaymentOption_1') ? 'showP1: true,' : ''),
			'netgiro_method2' => (Configuration::get('netgiro_PaymentOption_2') ? 'showP2: true,' : ''),
			'netgiro_method3' => (Configuration::get('netgiro_PaymentOption_3') ? 'showP3: true,' : ''),
			'this_path' => $this->_path,
			'this_path_ssl' => Configuration::get('PS_FO_PROTOCOL').$_SERVER['HTTP_HOST'].__PS_BASE_URI__."modules/{$this->name}/"));

			return $this->display(__FILE__, 'payment.tpl');
		}

		function hookInvoice($params)
		{
			//global $smarty;

			if (!$this->active)
				return;

            $this->smarty->assign(array(
				'status' => 'ok',
				'id_order' => $params['id_order'],
			));
			return $this->display(__FILE__, 'payment_return.tpl');
		}

		function hookpaymentReturn($params)
		{
			//global $smarty;

			if (!$this->active)
				return;

			$query = "
			SELECT `netgiro_confirmation_code`, `netgiro_invoice_number` FROM `"._DB_PREFIX_."order_netgiro` WHERE `id_order` = \"".$params['objOrder']->id."\"";
			$nrow = Db::getInstance()->getRow($query);

			$state = $params['objOrder']->getCurrentState();
			$this->smarty->assign('status', 'failed');
			if ($state == Configuration::get('PS_OS_PAYMENT'))
			{
                $this->smarty->assign(array(
					'total_to_pay' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
					'netgiro_invoiceNumber' => nl2br($nrow['netgiro_invoice_number']),
					'netgiro_confirmationcode' => nl2br($nrow['netgiro_confirmation_code']),
					'status' => 'ok',
					'id_order' => $params['objOrder']->id,
                    'this_path' => $this->_path
				));
				if (isset($params['objOrder']->reference) && !empty($params['objOrder']->reference))
                    $this->smarty->assign('reference', $params['objOrder']->reference);
			}
			else
                $this->smarty->assign('status', 'failed');
			return $this->display(__FILE__, 'payment_return.tpl');
		}


		public function getContent()
		{
			$this->_html .= '<h2>'.$this->l('Netgíró').'</h2>';

			$this->_postProcess();
			$this->_setConfigurationForm();

			return $this->_html;
		}


		private function _setConfigurationForm()
		{
			$this->_html .= '
			<form method="post" action="'.htmlentities($_SERVER['REQUEST_URI']).'">
				<script type="text/javascript">
					var pos_select = '.(($tab = (int)Tools::getValue('tabs')) ? $tab : '0').';
				</script>
				<script type="text/javascript" src="'._PS_BASE_URL_._PS_JS_DIR_.'tabpane.js"></script>
				<link type="text/css" rel="stylesheet" href="'._PS_BASE_URL_._PS_CSS_DIR_.'tabpane.css" />
				<input type="hidden" name="tabs" id="tabs" value="0" />
				<div class="tab-pane" id="tab-pane-1" style="width:100%;">
					<div class="tab-page" id="step1">
						'.$this->_getSettingsTabHtml().'
					</div>
				</div>
				<div class="clear"></div>
				<script type="text/javascript">
					function loadTab(id){}
					setupAllTabs();
				</script>
			</form>';
		}

		private function _getSettingsTabHtml()
		{
			//global $cookie;

			$html = '
			<h2>'.$this->l('Settings').'</h2>
			<div class="margin-form">
			    <label style="clear:both;">'.$this->l('Netgíró Gateway URL&nbsp').'</label>
				<input style="width: 300px;" type="text" name="netgiro_gateway_url" value="'.htmlentities(Tools::getValue('netgiro_gateway_url', Configuration::get('netgiro_gateway_url')), ENT_COMPAT, 'UTF-8').'" />
				<br><br>
			    <label style="clear:both;">'.$this->l('Application ID&nbsp').'</label>
				<input style="width: 300px;" type="text" name="netgiro_application_id" value="'.htmlentities(Tools::getValue('netgiro_application_id', Configuration::get('netgiro_application_id')), ENT_COMPAT, 'UTF-8').'" />
				<br><br>
			    <label style="clear:both;">'.$this->l('Secret Key&nbsp').'</label>
				<textarea style="width: 500px; height: 50px;" name="netgiro_secret_key">' . htmlentities(Tools::getValue('netgiro_secret_key', Configuration::get('netgiro_secret_key')), ENT_COMPAT, 'UTF-8') . '</textarea>
				<br><br>
				<label style="clear:both;">'.$this->l('Allowed payment options&nbsp').'</label>
				<input type="checkbox" name="netgiro_PaymentOption_1" id="netgiro_PaymentOption_1" style="vertical-align: middle;" value="1" '.(Configuration::get('netgiro_PaymentOption_1') ? 'checked="checked"' : '').' /> <label class="t" for="netgiro_PaymentOption_1">'.$this->l('14 days').'</label>
				<input type="checkbox" name="netgiro_PaymentOption_2" id="netgiro_PaymentOption_2" style="vertical-align: middle;" value="1" '.(Configuration::get('netgiro_PaymentOption_2') ? 'checked="checked"' : '').' /> <label class="t" for="netgiro_PaymentOption_1">'.$this->l('Partial payments').'</label>
				<input type="checkbox" name="netgiro_PaymentOption_3" id="netgiro_PaymentOption_3" style="vertical-align: middle;" value="1" '.(Configuration::get('netgiro_PaymentOption_3') ? 'checked="checked"' : '').' /> <label class="t" for="netgiro_PaymentOption_1">'.$this->l('Partial payments without interest').'</label>
				<br><br>
			    <label style="clear:both;">'.$this->l('Maximum number of installments&nbsp').'</label>
				<input style="width: 300px;" type="text" name="netgiro_MaxNumberOfInstallments" value="'.htmlentities(Tools::getValue('netgiro_MaxNumberOfInstallments', Configuration::get('netgiro_MaxNumberOfInstallments')), ENT_COMPAT, 'UTF-8').'" />
				<br><br>
			    <label style="clear:both;">'.$this->l('IFrame&nbsp').'</label>
				<input type="radio" name="netgiro_iframe" id="netgiro_iframe_on" value="1" '.(Tools::getValue('netgiro_iframe', Configuration::get('netgiro_iframe')) ? 'checked="checked" ' : '').'/>
				<label class="t" for="netgiro_iframe_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" /></label>
				<input type="radio" name="netgiro_iframe" id="netgiro_iframe_off" value="0" '.(!Tools::getValue('netgiro_iframe', Configuration::get('netgiro_iframe')) ? 'checked="checked" ' : '').'/>
				<label class="t" for="netgiro_iframe_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'" /></label>
			</div>
			<p class="center"><input class="button" type="submit" name="submitnetgiro" value="'.$this->l('Save settings').'" /></p>';
			return $html;
		}

		private function _postProcess()
		{
			//global $currentIndex, $cookie;

			if (Tools::isSubmit('submitnetgiro'))
			{
				$this->_errors = array();

				if (Tools::getValue('netgiro_application_id') == NULL)
				{
					$this->_errors[] = $this->l('Missing Application ID');
				}

				if (count($this->_errors) > 0)
				{
					$error_msg = '';
					foreach ($this->_errors AS $error)
						$error_msg .= $error.'<br />';
					$this->_html = $this->displayError($error_msg);
				}
				else
				{
					Configuration::updateValue('netgiro_gateway_url', trim(Tools::getValue('netgiro_gateway_url')));
					Configuration::updateValue('netgiro_application_id', trim(Tools::getValue('netgiro_application_id')));
					Configuration::updateValue('netgiro_secret_key', trim(Tools::getValue('netgiro_secret_key')));
					Configuration::updateValue('netgiro_iframe', trim(Tools::getValue('netgiro_iframe')));
					Configuration::updateValue('netgiro_PaymentOption_1', trim(Tools::getValue('netgiro_PaymentOption_1')));
					Configuration::updateValue('netgiro_PaymentOption_2', trim(Tools::getValue('netgiro_PaymentOption_2')));
					Configuration::updateValue('netgiro_PaymentOption_3', trim(Tools::getValue('netgiro_PaymentOption_3')));
					Configuration::updateValue('netgiro_MaxNumberOfInstallments', trim(Tools::getValue('netgiro_MaxNumberOfInstallments')));

					$this->_html = $this->displayConfirmation($this->l('Settings updated'));
				}
			}
		}

	}
