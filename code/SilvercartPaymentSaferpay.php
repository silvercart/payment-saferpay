<?php
/**
 * Copyright 2011 pixeltricks GmbH
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
 * Enables payment via saferpay.
 *
 * @package SilvercartPaymentSaferpay
 * @subpackage Base
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 30.09.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartPaymentSaferpay extends SilvercartPaymentMethod {
    
    /**
     * contains module name for display in the admin backend
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 30.09.2011
     */
    protected $moduleName = 'Saferpay';
    
    /**
     * Indicates whether a payment module has multiple payment channels or not.
     *
     * @var bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 30.09.2011
     */
    public static $has_multiple_payment_channels = false;
    
    /**
     * A list of possible payment channels.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 30.09.2011
     */
    public static $possible_payment_channels = array();
    
    // ------------------------------------------------------------------------
    // processing methods
    // ------------------------------------------------------------------------
    
    /**
     * hook to be called before order creation
     *
     * saves the SaferPay token to the session; after that redirects to SaferPay checkout
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 30.09.2011
     */
    public function processPaymentBeforeOrder() {
    }
    
    /**
     * hook to be called after jumpback from payment provider; called before
     * order creation
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 30.09.2011
     */
    public function processReturnJumpFromPaymentProvider() {
    }
    
    /**
     * hook to be called after order creation
     *
     * @param Order $orderObj object to be processed
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 30.09.2011
     */
    public function processPaymentAfterOrder($orderObj = array()) {
    }
    
    // ------------------------------------------------------------------------
    // payment module specific methods
    // ------------------------------------------------------------------------
    
    /**
     * returns CMS fields
     *
     * @param mixed $params optional
     *
     * @return FieldSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 30.09.2011
     */
    public function getCMSFields($params = null) {
        $fields = parent::getCMSFieldsForModules($params);
        $fieldLabels = self::$field_labels;

        $tabApi = new Tab('SaferPayAPI');
        $tabUrls = new Tab('SaferPayURLs');
        $tabOrderStatus = new Tab('OrderStatus', _t('SilvercartPaymentSaferpay.ATTRIBUTED_ORDERSTATUS', 'attributed order status', null, 'Zuordnung Bestellstatus'));

        $fields->fieldByName('Sections')->push($tabApi);
        $fields->fieldByName('Sections')->push($tabUrls);
        $fields->fieldByName('Sections')->push($tabOrderStatus);

        // basic settings -------------------------------------------------
        $fields->addFieldToTab(
                'Sections.Basic',
                new TextField('SaferPaySharedSecret', _t('SilvercartPaymentSaferpay.SHARED_SECRET'))
        );

        // API Tabset ---------------------------------------------------------
        $tabApiTabset = new TabSet('APIOptions');
        $tabApiTabDev = new Tab(_t('SilvercartPaymentSaferpay.API_DEVELOPMENT_MODE', 'API development mode', null, 'API Entwicklungsmodus'));
        $tabApiTabLive = new Tab(_t('SilvercartPaymentSaferpay.API_LIVE_MODE', 'API live mode'));

        // API Tabs -----------------------------------------------------------
        $tabApiTabset->push($tabApiTabDev);
        $tabApiTabset->push($tabApiTabLive);

        $tabApi->push($tabApiTabset);

        // URL Tabset ---------------------------------------------------------
        $tabUrlTabset = new TabSet('URLOptions');
        $tabUrlTabDev = new Tab(_t('SilvercartPaymentSaferpay.URLS_DEV_MODE', 'URLs of dev mode', null, 'URLs Entwicklungsmodus'));
        $tabUrlTabLive = new Tab(_t('SilvercartPaymentSaferpay.URLS_LIVE_MODE', 'URLs of live mode', null, 'URLs Livemodus'));

        // URL Tabs -----------------------------------------------------------
        $tabUrlTabset->push($tabUrlTabDev);
        $tabUrlTabset->push($tabUrlTabLive);

        $tabUrls->push($tabUrlTabset);

        // API Tab Dev fields -------------------------------------------------
        $tabApiTabDev->setChildren(
                new FieldSet(
                        new TextField('SaferPayApiUsername_Dev', _t('SilvercartPaymentSaferpay.API_USERNAME')),
                        new TextField('SaferPayApiPassword_Dev', _t('SilvercartPaymentSaferpay.API_PASSWORD')),
                        new TextField('SaferPayApiSignature_Dev', _t('SilvercartPaymentSaferpay.API_SIGNATURE')),
                        new TextField('SaferPayApiVersion_Dev', _t('SilvercartPaymentSaferpay.API_VERSION'))
                )
        );

        // API Tab Live fields ------------------------------------------------
        $tabApiTabLive->setChildren(
                new FieldSet(
                        new TextField('SaferPayApiUsername_Live', _t('SilvercartPaymentSaferpay.API_USERNAME')),
                        new TextField('SaferPayApiPassword_Live', _t('SilvercartPaymentSaferpay.API_PASSWORD')),
                        new TextField('SaferPayApiSignature_Live', _t('SilvercartPaymentSaferpay.API_SIGNATURE')),
                        new TextField('SaferPayApiVersion_Live', _t('SilvercartPaymentSaferpay.API_VERSION'))
                )
        );

        // URL Tab Dev fields -------------------------------------------------
        $tabUrlTabDev->setChildren(
                new FieldSet(
                        new TextField('SaferPayCheckoutUrl_Dev', _t('SilvercartPaymentSaferpay.CHECKOUT_URL')),
                        new TextField('SaferPayNvpApiServerUrl_Dev', _t('SilvercartPaymentSaferpay.URL_API_NVP')),
                        new TextField('SaferPaySoapApiServerUrl_Dev', _t('SilvercartPaymentSaferpay.URL_API_SOAP'))
                )
        );

        // URL Tab Live fields ------------------------------------------------
        $tabUrlTabLive->setChildren(
                new FieldSet(
                        new TextField('SaferPayCheckoutUrl_Live', _t('SilvercartPaymentSaferpay.CHECKOUT_URL')),
                        new TextField('SaferPayNvpApiServerUrl_Live', _t('SilvercartPaymentSaferpay.URL_API_NVP')),
                        new TextField('SaferPaySoapApiServerUrl_Live', _t('SilvercartPaymentSaferpay.URL_API_SOAP'))
                )
        );

        // Bestellstatus Tab fields -------------------------------------------
        $OrderStatus = DataObject::get('SilvercartOrderStatus');
        $tabOrderStatus->setChildren(
                new FieldSet(
                        new DropdownField('PaidOrderStatus', _t('SilvercartPaymentSaferpay.ORDERSTATUS_PAYED'), $OrderStatus->map('ID', 'Title'), $this->PaidOrderStatus),
                        new DropdownField('CanceledOrderStatus', _t('SilvercartPaymentSaferpay.ORDERSTATUS_CANCELED'), $OrderStatus->map('ID', 'Title'), $this->CanceledOrderStatus),
                        new DropdownField('PendingOrderStatus', _t('SilvercartPaymentSaferpay.ORDERSTATUS_PENDING'), $OrderStatus->map('ID', 'Title'), $this->PendingOrderStatus),
                        new DropdownField('RefundedOrderStatus', _t('SilvercartPaymentSaferpay.ORDERSTATUS_REFUNDED'), $OrderStatus->map('ID', 'Title'), $this->RefundedOrderStatus)
                )
        );

        return $fields;
    }
}
