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

require(__DATAGEN_CLASSES__ . '/PromoCodeGen.class.php');

/**
 * The PromoCode class defined here contains any
 * customized code for the PromoCode class in the
 * Object Relational Model.
 */
class PromoCode extends PromoCodeGen {
	// String representation of the Object
	public function __toString() {
		return sprintf('Promo Code Object %s',  $this->strCode);
	}

	protected function IsActive() {
		if ($this->IsEnabled() &&
			$this->IsStarted() &&
			!$this->IsExpired() &&
			$this->HasRemaining())
				return true;
		return false;
	}

	protected function HasRemaining() {
		// Above 0 we have some left, below 0 is unlimited
		if ($this->intQtyRemaining == 0)
			return false;
		return true;
	}

	protected function IsEnabled() {
		if ($this->blnEnabled)
			return true;
		return false;
	}

	protected function IsExcept() {
		if ($this->blnExcept)
			return true;
		return false;
	}

	protected function IsStarted() {
		if (date("Y-m-d")>=date("Y-m-d",strtotime($this->strValidFrom)))
			return true;
		return false;
	}

	protected function IsExpired() {
		if (date("Y-m-d",strtotime($this->strValidUntil))<date("Y-m-d"))
			return true;
		return false;
	}

	public function IsProductAffected($objItem) {
		$arrCode = $this->LsCodeArray;
		error_log("here1");
		
		if (empty($arrCode)) //no product restrictions
			return true;
		error_log("here2");

		//We normally return true if it's a match. If this code uses Except, then the logic is reversed
		$boolReturn = false;
		if ($this->IsExcept()) $boolReturn = true;

        foreach($arrCode as $strCode) {
            $strCode=strtolower($strCode);
            error_log("comapring ".substr($strCode, 0,8));
            if (substr($strCode, 0,7) == "family:" && 
                substr($strCode,7,255) == strtolower($objItem->Product->Family)) 
            { $boolReturn = $this->IsExcept() ? false : true; error_log("bool is now ".$boolReturn); }
            
            if (substr($strCode, 0,6) == "class:" && 
                substr($strCode,6,255) == strtolower($objItem->Product->ClassName)) 
            { $boolReturn = $this->IsExcept() ? false : true; error_log("bool is now ".$boolReturn); }
             
            if (substr($strCode, 0,8) == "keyword:" && (
                substr($strCode,8,255) == strtolower($objItem->Product->WebKeyword1) ||
                substr($strCode,8,255) == strtolower($objItem->Product->WebKeyword2) ||
                substr($strCode,8,255) == strtolower($objItem->Product->WebKeyword3) )              
                ) 
            { $boolReturn = $this->IsExcept() ? false : true; error_log("bool is now ".$boolReturn); }
            
        }  
		error_log("here3");
		  
		  if (_xls_array_search_begin($objItem->Code, $arrCode))
			$boolReturn = $this->IsExcept() ? false : true;
		error_log("here4");

		return $boolReturn; 
	}

	/**
	 * Load a PromoCode from code
	 * @param string $strCode
	 * @return PromoCode
	 */
	public static function LoadByCode($strCode) {
		return PromoCode::QuerySingle(
			QQ::Equal(QQN::PromoCode()->Code, $strCode)
		);
	}

	/**
	 * Load a PromoCode from cart id
	 * @param string $intCart
	 * @return PromoCode
	 */
	public static function LoadByFkCartId($intCart) {
		return PromoCode::QuerySingle(
			QQ::Equal(QQN::PromoCode()->FkCartId, $intCart)
		);
	}

	public function __get($strName) {
		switch ($strName) {
			case 'Code':
				return $this->strCode;

			case 'LsCodeArray':
				$arrCodes = explode(",", $this->strLscodes);
				array_walk($arrCodes, '_xls_trim');
				return $arrCodes;

			case 'Active':
				return $this->IsActive();

			case 'Except':
				return $this->IsExcept();

			case 'HasRemaining':
				return $this->HasRemaining();

			case 'Started':
				return $this->IsStarted();

			case 'Expired':
				return $this->IsExpired();

			case 'Threshold':
				return $this->strThreshold;

			default:
				try {
					return parent::__get($strName);
				} catch (QCallerException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}
		}
	}

	public function __set($strName, $mixValue) {
		switch ($strName) {
			case 'Code':
				$mixValue = trim($mixValue);
				try {
					return parent::__set($strName, $mixValue);
				} catch (QCallerException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}

			default:
				try {
					return parent::__set($strName, $mixValue);
				} catch (QCallerException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}
		}
	}
}
