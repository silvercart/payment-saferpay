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
 * @subpackage Base
 */

/**
 * Extends SilvercartShoppingCart.
 *
 * @package SilvercartPaymentSaferpay
 * @subpackage Base
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 01.10.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2012 pixeltricks GmbH
 */
class SilvercartPaymentSaferpayShoppingCart extends DataObjectDecorator {

    /**
     * Additional datafields and relations.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.10.2012
     */
    public function extraStatics() {
        return array(
            'db' => array(
                'saferpayToken' => 'VarChar(150)',
                'saferpayID'    => 'VarChar(150)'
            )
        );
    }

    /**
     * Returns the saferpay ID.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.10.2012
     */
    public function getSaferpayID() {
        return $this->owner->getField('saferpayID');
    }

    /**
     * Returns the saferpay token.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.10.2012
     */
    public function getSaferpayToken() {
        return $this->owner->getField('saferpayToken');
    }

    /**
     * Writes the given ID into the shoppingcart.
     *
     * @param string $saferpayID The ID to save
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.10.2012
     */
    public function saveSaferpayID($saferpayID) {
        $this->owner->setField('saferpayID', $saferpayID);
        $this->owner->write();
    }

    /**
     * Writes the given token into the shoppingcart.
     *
     * @param string $saferpayToken The token to save
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.10.2012
     */
    public function saveSaferpayToken($saferpayToken) {
        $this->owner->setField('saferpayToken', $saferpayToken);
        $this->owner->write();
    }
}