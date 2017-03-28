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
class SilvercartPaymentSaferpayOrder extends DataExtension {
    
    /**
     * DB attributes.
     *
     * @var array
     */
    private static $db = array(
        'saferpayToken'         => 'VarChar(150)',
        'saferpayIdentifier'    => 'VarChar(150)',
        'saferpayTransactionId' => 'VarChar(150)',
    );

    /**
     * Returns the saferpay ID.
     *
     * @return string
     */
    public function getSaferpayID() {
        return $this->owner->getField('saferpayIdentifier');
    }

    /**
     * Returns the saferpay token.
     *
     * @return string
     */
    public function getSaferpayToken() {
        return $this->owner->getField('saferpayToken');
    }

    /**
     * Returns the saferpay transaction ID.
     *
     * @return string
     */
    public function getSaferpayTransactionId() {
        return $this->owner->getField('saferpayTransactionId');
    }

    /**
     * Writes the given ID to the order.
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
     * Writes the given token to the order.
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
     * Writes the given transaction ID to the order.
     *
     * @param string $saferpayTransactionId The transaction ID to save
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2017
     */
    public function saveSaferpayTransactionId($saferpayTransactionId) {
        $this->owner->setField('saferpayTransactionId', (string) $saferpayTransactionId);
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
        $fieldLabels['saferpayIdentifier']    = _t('SilvercartPaymentSaferpayOrder.SAFERPAY_IDENTIFIER');
        $fieldLabels['saferpayToken']         = _t('SilvercartPaymentSaferpayOrder.SAFERPAY_TOKEN');
        $fieldLAbels['saferpayTransactionId'] = _t('SilvercartPaymentSaferpayOrder.saferpayTransactionId');
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
    
    /**
     * exclude these fields from scaffolding
     * 
     * @param array $fields already excluded fields
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 05.03.2013
     */
    public function updateExcludeFromScaffolding(&$fields) {
        $fields = array_merge(
                    $fields,
                    array(
                        'saferpayToken',
                        'saferpayIdentifier',
                        'saferpayTransactionId',
                    )
                );
    }
}