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
 * CheckoutProcessPaymentAfterOrder
 *
 * @package Silvercart
 * @subpackage Forms_Checkout
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 06.04.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartPaymentSaferpayCheckoutFormStep4 extends SilvercartCheckoutFormStepPaymentInit {

    /**
     * Process the current step
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 06.04.2011
     */
    public function process() {
        if (parent::process()) {
            $paymentSuccessful  = false;
            $checkoutData       = $this->controller->getCombinedStepData();
            $orderObj           = DataObject::get_by_id(
                'SilvercartOrder',
                $checkoutData['orderId']
            );

            if ($this->paymentMethodObj &&
                $orderObj) {
                $this->paymentMethodObj->setOrder($orderObj);
                $paymentSuccessful = $this->paymentMethodObj->processPaymentAfterOrder();
            }

            if ($paymentSuccessful) {
                $this->controller->addCompletedStep();
                $this->controller->NextStep();
            } else {
                return $this->renderError();
            }
        }
    }
}