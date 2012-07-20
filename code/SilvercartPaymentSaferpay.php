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
     */
    protected $moduleName = 'Saferpay';

    /**
     * Indicates whether a payment module has multiple payment channels or not.
     *
     * @var bool
     */
    public static $has_multiple_payment_channels = false;

    /**
     * A list of possible payment channels.
     *
     * @var array
     */
    public static $possible_payment_channels = array();

    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'saferpayAccountId_Dev'     => 'VarChar(100)',
        'saferpayAccountId_Live'    => 'VarChar(100)',
        'saferpayPayinitGateway'    => 'VarChar(100)'
    );

    /**
     * Default records.
     *
     * @var array
     */
    public static $defaults = array(
        'saferpayPayinitGateway'    => 'https://www.saferpay.com/hosting/CreatePayInit.asp'
    );

    /**
     * returns CMS fields
     *
     * @param mixed $params optional
     *
     * @return FieldSet
     */
    public function getCMSFields($params = null) {
        $fields         = parent::getCMSFieldsForModules($params);

        $tabApi     = new Tab('SaferpayAPI');
        $tabUrls    = new Tab('SaferpayURLs');

        $fields->fieldByName('Sections')->push($tabApi);
        $fields->fieldByName('Sections')->push($tabUrls);

        // API Tabset ---------------------------------------------------------
        $tabApiTabset   = new TabSet('APIOptions');
        $tabApiTabDev   = new Tab(_t('SilvercartPaymentSaferpay.API_DEVELOPMENT_MODE', 'API development mode'));
        $tabApiTabLive  = new Tab(_t('SilvercartPaymentSaferpay.API_LIVE_MODE', 'API live mode'));

        // API Tabs -----------------------------------------------------------
        $tabApiTabset->push($tabApiTabDev);
        $tabApiTabset->push($tabApiTabLive);

        $tabApi->push($tabApiTabset);

        // API Tab Dev fields -------------------------------------------------
        $tabApiTabDev->setChildren(
            new FieldSet(
                new TextField('saferpayAccountId_Dev', _t('SilvercartPaymentSaferpay.API_ACCOUNTID'))
            )
        );

        // API Tab Live fields ------------------------------------------------
        $tabApiTabLive->setChildren(
            new FieldSet(
                new TextField('saferpayAccountId_Live', _t('SilvercartPaymentSaferpay.API_ACCOUNTID'))
            )
        );

        // URL fields ------------------------------------------------
        $tabUrls->push(
            new TextField('saferpayPayinitGateway', _t('SilvercartPaymentSaferpay.URL_PAYINIT_GATEWAY'))
        );

        return $fields;
    }

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
        $paymentUrl = $this->getPaymentUrl();
        print $paymentUrl."<br />";
        exit();
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

    /**
     * possibility to return a text at the end of the order process
     * processed after order creation
     *
     * @param Order $orderObj the order object
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 30.09.2011
     */
    public function processPaymentConfirmationText($orderObj) {
    }

    // ------------------------------------------------------------------------
    // payment module specific methods
    // ------------------------------------------------------------------------

    /**
     * Returns the payment URL
     *
     * @return string
     */
    protected function getPaymentUrl() {
        $paymentUrl = '';
        $amount = $this->getShoppingCart()->getAmount();
        
        $successlink    = $this->getReturnLink();
        $faillink       = $this->getCancelLink();
        $backlink       = $this->getReturnLink();
        
        // Mandatory attributes
        $attributes  = "?ACCOUNTID=" . $accountid;
        $attributes .= "&AMOUNT=" . $amount;
        $attributes .= "&CURRENCY=" . $currency;
        $attributes .= "&DESCRIPTION=" . urlencode($description);
        $attributes .= "&SUCCESSLINK=" . urlencode($successlink);
        $attributes .= "&FAILLINK=" . urlencode($faillink);
        $attributes .= "&BACKLINK=" . urlencode($backlink);
        
        // Additional attributes
        $attributes .= "&CCCVC=yes"; // input of cardsecuritynumber mandatory
        $attributes .= "&CCNAME=yes"; // input of cardholder name mandatory

        // Shop specific attributes
        $attributes .= "&ORDERID=" . $orderid;
        
        $payinit_url = $this->saferpayPayinitGateway.$attributes;
        
        // Create CURL session
        $cs = curl_init($payinit_url);
        
        // Set CURL session options
        curl_setopt($cs, CURLOPT_PORT, 443);                // set option for outgoing SSL requests via CURL
        curl_setopt($cs, CURLOPT_SSL_VERIFYPEER, false);	// ignore SSL-certificate-check - session still SSL-safe
        curl_setopt($cs, CURLOPT_HEADER, 0);                // no header in output
        curl_setopt ($cs, CURLOPT_RETURNTRANSFER, true); 	// receive returned characters
        
        // Execute CURL session
        $paymentUrl = curl_exec($cs);
        
        // Close CURL session
        $ce = curl_error($cs);
        curl_close($cs);
        
        // Stop if CURL is not working
        if (strtolower( substr( $payment_url, 0, 24 ) ) != "https://www.saferpay.com") {
            $msg = "<h1>PHP-CURL is not working correctly for outgoing SSL-calls on your server</h1>\r\n";
            $msg .= "<h2><font color=\"red\">".htmlentities($payment_url)."&nbsp;</font></h2>\r\n";
            $msg .= "<h2><font color=\"red\">".htmlentities($ce)."&nbsp;</font></h2>\r\n";
            print $msg;
            exit();
        }
        
        return $paymentUrl;
    }
}
