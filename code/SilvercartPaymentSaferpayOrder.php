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
 * @subpackage Payment_Saferpay
 */

/**
 * Order plugin.
 *
 * @package Silvercart
 * @subpackage Payment_Saferpay
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 01.10.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2012 pixeltricks GmbH
 */
class SilvercartPaymentSaferpayOrder extends DataObjectDecorator {
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
                'saferpayToken'      => 'VarChar(150)',
                'saferpayIdentifier' => 'VarChar(150)'
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
        return $this->owner->getField('saferpayIdentifier');
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
        $this->owner->setField('saferpayIdentifier', (string) $saferpayID);
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
        $this->owner->setField('saferpayToken', (string) $saferpayToken);
        $this->owner->write();
    }

    /**
     * Update field labels
     *
     * @param array &$fieldLabels The field labels
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 2013-02-12
     */
    public function updateFieldLabels(&$fieldLabels) {
        $fieldLabels['saferpayIdentifier']  = _t('SilvercartPaymentSaferpayOrder.SAFERPAY_IDENTIFIER');
        $fieldLabels['saferpayToken']       = _t('SilvercartPaymentSaferpayOrder.SAFERPAY_TOKEN');
    }

    /**
     * Add saferpayToken to searchable fields
     *
     * @param array &$searchableFields The searchable fields
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 2013-02-12
     */
    public function updateSearchableFields(&$searchableFields) {
        $searchableFields['saferpayToken'] = array(
            'title'     => $this->owner->fieldLabel('saferpayToken'),
            'filter'    => 'PartialMatchFilter'
        );
    }
}

/**
 * Extends SilvercartOrder_CollectionController
 *
 * @package Silvercart
 * @subpackage Payment_Saferpay
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 2013-02-12
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartPaymentSaferpayOrder_CollectionController extends DataObjectDecorator {

    /**
     * Add search field for the saferpay token.
     *
     * @param Form &$searchForm The search form object
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 2013-02-12
     */
    public function updateSearchForm(&$searchForm) {
        $fields = $searchForm->Fields();
        $fields->insertAfter($fields->dataFieldByName('saferpayToken'), 'OrderNumber');
    }
}