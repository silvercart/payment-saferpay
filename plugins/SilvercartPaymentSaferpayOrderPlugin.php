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
 * @package SilvercartPaymentSaferpay
 * @subpackage Plugins
 */

/**
 * Order plugin.
 *
 * @package SilvercartPaymentSaferpay
 * @subpackage Plugins
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 01.10.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2012 pixeltricks GmbH
 */
class SilvercartPaymentSaferpayOrderPlugin extends DataObjectDecorator {

    /**
     * Injects the saferpay information from the shopping cart into the order
     * object.
     *
     * @param SilvercartOrder        &$silvercartOrder        The SilverCart order object
     * @param SilvercartShoppingCart &$silvercartShoppingCart The shopping cart object
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.10.2012
     */
    public function pluginCreateFromShoppingCart(&$silvercartOrder, &$silvercartShoppingCart) {
        $silvercartOrder->setField('saferpayID',    $silvercartShoppingCart->getSaferpayId());
        $silvercartOrder->setField('saferpayToken', $silvercartShoppingCart->getSaferpayToken());
        $silvercartOrder->write();
    }
}