<?php
/**
 * Copyright 2012 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
 * @subpackage Forms_Checkout
 */

/**
 * CheckoutProcessPaymentBeforeOrder
 *
 * @package Silvercart
 * @subpackage Forms_Checkout
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 01.10.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartPaymentSaferpayCheckoutFormStep1 extends SilvercartCheckoutFormStepPaymentInit {

    /**
     * Here we set some preferences.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.10.2012
     */
    public function preferences() {
        parent::preferences();

        $this->preferences['stepTitle']     = _t('SilvercartPaymentSaferpay.ENTER_DATA_AT_SAFERPAY');
        $this->preferences['stepIsVisible'] = true;
    }

    /**
     * Process the current step
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.10.2012
     */
    public function process() {
        if (parent::process()) {
            $this->paymentMethodObj->setCancelLink(Director::absoluteURL($this->controller->Link()) . 'GotoStep/4');
            $this->paymentMethodObj->setReturnLink(Director::absoluteURL($this->controller->Link()));

            if (!$this->paymentMethodObj->processPaymentBeforeOrder()) {
                return $this->renderError();
            }
        }
    }
}