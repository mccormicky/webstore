<?php
/*
  LightSpeed Web Store
 
  NOTICE OF LICENSE
 
  This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@lightspeedretail.com <mailto:support@lightspeedretail.com>
 * so we can send you a copy immediately.
 
  DISCLAIMER
 
 * Do not edit or add to this file if you wish to upgrade Web Store to newer
 * versions in the future. If you wish to customize Web Store for your
 * needs please refer to http://www.lightspeedretail.com for more information.
 
 * @copyright  Copyright (c) 2011 Xsilva Systems, Inc. http://www.lightspeedretail.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 
 */
/**
 * Free shipping module
 *
 *
 *
 */

class free_shipping extends xlsws_class_shipping {
	public function name(){
		$config = $this->getConfigValues(get_class($this));

		if(isset($config['label']))
			return $config['label'];

		return $this->admin_name();
	}

	public function admin_name() {
		return _sp("Free shipping");
	}

	// return the keys for this module
	public function config_fields($objParent) {
		$ret= array();

		$ret['label'] = new XLSTextBox($objParent);
		$ret['label']->Name = _sp('Label');
		$ret['label']->Required = true;
		$ret['label']->Text = $this->admin_name();

		$ret['rate'] = new XLSTextBox($objParent);
		$ret['rate']->Name = _sp('Threshold Amount ($)');
		$ret['rate']->Text = '0';
		$ret['rate']->ToolTip = _sp('The amount the subtotal must be before free shipping is considered');


		$ret['promocode'] = new XLSTextBox($objParent);
		$ret['promocode']->Name = _sp('Optional Promo Code');
		$ret['promocode']->Text = '';
		$ret['promocode']->ToolTip = _sp('When used, Free Shipping option will only appear with valid code entered.');

		$ret['startdate'] = new XLSTextBox($objParent);
		$ret['startdate']->Name = _sp('Optional Start Date (YYYY-MM-DD)');
		$ret['startdate']->Text = '';
		$ret['startdate']->ToolTip = _sp('When used, Free Shipping option will only appear as of this date. May be used with Promo Code.');

		$ret['enddate'] = new XLSTextBox($objParent);
		$ret['enddate']->Name = _sp('Optional End Date (YYYY-MM-DD)');
		$ret['enddate']->Text = '';
		$ret['enddate']->ToolTip = _sp('When used, Free Shipping option will only appear up to this date. May be used with Promo Code.');

		$ret['promocode'] = new XLSTextBox($objParent);
		$ret['promocode']->Name = _sp('Optional Promo Code');
		$ret['promocode']->Text = '';
		$ret['promocode']->ToolTip = _sp('When used, Free Shipping option will only appear with valid code entered.');


		$ret['product'] = new XLSTextBox($objParent);
		$ret['product']->Name = _sp('LightSpeed Product Code');
		$ret['product']->Required = true;
		$ret['product']->Text = 'SHIPPING';

		return $ret;
	}

	public function check_config_fields($fields) {
		//check that rate is numeric
		$val = $fields['rate']->Text;
		if(!is_numeric($val)) {
			QApplication::ExecuteJavaScript("alert('Rate must be a number')");
			return false;
		}

		return true;
	}

	public function total($fields, $cart, $country = '', $zipcode = '', $state = '', $city = '', $address2 = '', $address1 = '', $company = '', $lname = '', $fname = '') {
		$config = $this->getConfigValues('free_shipping');

		$price = 0;

		if ($cart->Subtotal < $config['rate']) {
			_xls_log("FREE SHIPPING: Cart subtotal does not qualify for free shipping");
			$userMsg = _sp("Subtotal does not qualify for free shipping, you must purchase at least " . _xls_currency($config['rate']) . " worth of merchandise.");
			QApplication::ExecuteJavaScript("alert('".$userMsg."')");
			return false;
		}

		return array('price' => $price, 'product' => $config['product']);

		return 0;
	}

	/**
	 * Check if the module is valid or not.
	 * Returning false here will exclude the module from checkout page
	 * Can be used for tests against cart conditions
	 *
	 * @return boolean
	 */
	public function check() {
	
		$vals = $this->getConfigValues(get_class($this));
		
		//Check possible scenarios why we would not offer free shipping
		if ($vals['startdate']>date("Y-m-d")) return false;
		if ($vals['enddate']<date("Y-m-d")) return false;
		if (isset($vals['promocode']))
		{ 
			if ($cart->FkPromoId > 0)
			{
				$pcode = PromoCode::Load($cart->FkPromoId);
				if ($pcode->Code == $vals['promocode']) return true;
				
			}
			return false;
			
		}
	
		return true;
	}
}
