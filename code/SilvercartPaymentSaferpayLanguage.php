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
 * carries multilingual attributes for SilvercartPaymentPaypal
 *
 * @package Silvercart
 * @subpackage Payment_Saferpay
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 01.10.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartPaymentSaferpayLanguage extends SilvercartPaymentMethodLanguage {
    
    /**
     * Attributes.
     *
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.10.2012
     */
    public static $db = array(
        'saferpayInfotextCheckout' => 'VarChar(255)'
    );
    
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.10.2012
     */
    public static $has_one = array(
        'SilvercartPaymentSaferpay' => 'SilvercartPaymentSaferpay'
    );
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.10.2012
     */
    public function singular_name() {
        if (_t('SilvercartPaymentSaferpayLanguage.SINGULARNAME')) {
            return _t('SilvercartPaymentSaferpayLanguage.SINGULARNAME');
        } else {
            return parent::singular_name();
        } 
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.10.2012
     */
    public function plural_name() {
        if (_t('SilvercartPaymentSaferpayLanguage.PLURALNAME')) {
            return _t('SilvercartPaymentSaferpayLanguage.PLURALNAME');
        } else {
            return parent::plural_name();
        }

    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2012 pixeltricks GmbH
     * @since 01.10.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),             array(
                'saferpayInfotextCheckout' => _t('SilvercartPaymentSaferpay.INFOTEXT_CHECKOUT')
            )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
}

