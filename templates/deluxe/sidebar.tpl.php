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
   
 * @copyright  Copyright (c) 2011 Xsilva Systems, Inc. http://www.lightspeedretail.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 
 */

/**
 * Deluxe template: Frame for sidebars, is called for each sidebar added (defaults: Order Lookup, Wish List) 
 *
 * 
 *
 */


foreach($this->arrSidePanels as $name => $pnl){
?>

			<h1 class="rounded" style="color: #111;" onclick="$('#<?= $pnl->ControlId ?>_container').slideToggle('slow');"><?= $name; ?></h1>
			<div class="containers rounded-bottom" id="<?= $pnl->ControlId ?>_container" style="display: none;">
					<?php $pnl->Render(); ?>
			</div>

<?php
	
}
	

?>
