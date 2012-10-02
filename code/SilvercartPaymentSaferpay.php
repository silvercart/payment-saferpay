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
     * contains description of the shopping cart content for display at the
     * saferpay site.
     *
     * @var string
     */
    protected $description = null;

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
     * contains all strings of the saferpay answer which declare the
     * transaction status false
     *
     * @var array
     */
    public $failedStatus = array(
        'failed',
    );
    /**
     * contains all strings of the saferpay answer which declare the
     * transaction status true
     *
     * @var array
     */
    public $successStatus = array(
        'PayConfirm',
    );

    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'canceledOrderStatus'       => 'Int',
        'paidOrderStatus'           => 'Int',
        'successOrderStatus'        => 'Int',

        'saferpayAccountId_Dev'      => 'VarChar(100)',
        'saferpayAccountId_Live'     => 'VarChar(100)',
        'saferpayPayinitGateway'     => 'VarChar(100)',
        'saferpayPayconfirmGateway'  => 'VarChar(100)',
        'saferpayPaycompleteGateway' => 'VarChar(100)',

        'autoclose'     => 'Int',
        'showLanguages' => 'Boolean(0)',
        'cccvc'         => 'Boolean(1)',
        'ccname'        => 'Boolean(1)',
    );


    /**
     * 1:n relationships.
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartPaymentSaferpayLanguages' => 'SilvercartPaymentSaferpayLanguage'
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
     * Creates a unique saferpay token.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.10.2012
     */
    public function createSaferpayToken() {
        $member       = Member::currentUser();
        $shoppingCart = $this->getShoppingCart();
        $token        = crypt(
            $member->FirstName.'-'.
            $member->Surame.'-'.
            $member->email.'-'.
            $shoppingCart->getAmountTotal()->getAmount().'-'.
            count($shoppingCart->SilvercartShoppingCartPositions()).'-'.
            time()
        );

        return $token;
    }

    /**
     * Returns the saferpay account ID
     *
     * @return string The account ID
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.10.2012
     */
    public function getAccountId() {
        if ($this->mode == 'live') {
            return $this->saferpayAccountId_Live;
        } else {
            return $this->saferpayAccountId_Dev;
        }
    }

    /**
     * Returns the description of the order.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.10.2012
     */
    public function getDescription() {
        if ($this->description == null) {
            $templateVariables = new ArrayData(array(
                'SilvercartShoppingCart' => $this->getShoppingCart()
            ));
            $template          = new SSViewer('saferpayDescription');
            $this->description = HTTP::absoluteURLs($template->process($templateVariables));
        }

        return $this->description;
    }

    /**
     * returns CMS fields
     *
     * @param mixed $params optional
     *
     * @return FieldSet
     */
    public function getCMSFields($params = null) {
        $fields     = parent::getCMSFieldsForModules($params);
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
        $tabUrls->push(
            new TextField('saferpayPayconfirmGateway', _t('SilvercartPaymentSaferpay.URL_PAYCONFIRM_GATEWAY'))
        );
        $tabUrls->push(
            new TextField('saferpayPaycompleteGateway', _t('SilvercartPaymentSaferpay.URL_PAYCOMPLETE_GATEWAY'))
        );
        $fields->addFieldToTab(
            'Sections.Basic',
            new TextField('autoclose', _t('SilvercartPaymentSaferpay.AUTOCLOSE'))
        );
        $fields->addFieldToTab(
            'Sections.Basic',
            new CheckboxField('showLanguages', _t('SilvercartPaymentSaferpay.SHOWLANGUAGES'))
        );
        $fields->addFieldToTab(
            'Sections.Basic',
            new CheckboxField('cccvc', _t('SilvercartPaymentSaferpay.CCCVC'))
        );
        $fields->addFieldToTab(
            'Sections.Basic',
            new CheckboxField('ccname', _t('SilvercartPaymentSaferpay.CCNAME'))
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

        if ($paymentUrl === false) {
            return false;
        } else {
            $this->controller->addCompletedStep($this->controller->getCurrentStep());
            $this->controller->setCurrentStep($this->controller->getNextStep());

            Director::redirect($paymentUrl);
        }
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
        $shoppingCart   = $this->getShoppingCart();
        $error          = 0;
        $msgType        = null;
        $saferpayId     = null;
        $saferpayToken  = null;
        $providerId     = null;
        $providerName   = null;
        $accountId      = null;
        $signature      = null;
        $data           = null;
        $eci            = null;
        $ecimsg         = null;

        if (array_key_exists('SIGNATURE', $_REQUEST)) {
            $signature = urldecode($_REQUEST['SIGNATURE']);
        } else {
            $error = 1;
        }
        if (array_key_exists('DATA', $_REQUEST)) {
            $data = urldecode($_REQUEST['DATA']);
        } else {
            $error = 1;
        }

        if ($error == 0) {
            $xml = new SimpleXMLElement(urldecode($_REQUEST['DATA']));

            if ($xml['ID']) {
                $saferpayId = $xml['ID'];
            } else {
                $error = 2;
            }
            if ($xml['MSGTYPE']) {
                $msgType = $xml['MSGTYPE'];
            } else {
                $error = 2;
            }
            if ($xml['ORDERID']) {
                $saferpayToken = $xml['ORDERID'];
            } else {
                $error = 2;
            }
            if ($xml['PROVIDERID']) {
                $providerId = $xml['PROVIDERID'];
            } else {
                $error = 2;
            }
            if ($xml['PROVIDERNAME']) {
                $providerName = $xml['PROVIDERNAME'];
            } else {
                $error = 2;
            }
            if ($xml['ACCOUNTID']) {
                $accountId = $xml['ACCOUNTID'];
            } else {
                $error = 2;
            }
            if ($xml['ECI']) {
                $eci = $xml['ECI'];
            }

            if ($accountId != $this->getAccountId()) {
                $error = 3;
            }
            if ($saferpayToken != $shoppingCart->getSaferpayToken()) {
                $error = 4;
            }

            if ($error == 0) {
                if (in_array($msgType, $this->successStatus)) {
                    $payconfirm_url = $this->getConfirmationUrl($data, $signature);

                    $cs = curl_init($payconfirm_url);
                    curl_setopt($cs, CURLOPT_PORT, 443); // set option for outgoing SSL requests via CURL
                    curl_setopt($cs, CURLOPT_SSL_VERIFYPEER, false); // ignore SSL-certificate-check - session still SSL-safe
                    curl_setopt($cs, CURLOPT_HEADER, 0); // no header in output
                    curl_setopt ($cs, CURLOPT_RETURNTRANSFER, true); // receive returned characters

                    $verification = curl_exec($cs);
                    curl_close($cs);

                    if (strtoupper(substr($verification, 0, 3)) != "OK:") {
                        $error = 5;
                    } else {
                        $shoppingCart->saveSaferpayID($saferpayId);
                    }
                } else {
                    $error = 6;
                }
            }
        }

        if ($error > 0) {
            $this->Log('processReturnJumpFromPaymentProvider', var_export($_REQUEST, true));
            $errorMsg = _t('SilvercartPaymentSaferpayError.ERROR_'.$error);

            $this->addError($errorMsg);

            return false;
        } else {
            parent::processReturnJumpFromPaymentProvider();
        }
    }

    /**
     * hook to be called after order creation
     *
     * @param array $orderObj object to be processed
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.10.2012
     */
    public function processPaymentAfterOrder($orderObj = array()) {
        $saferpayId     = $this->order->getSaferpayId();
        $saferpayToken  = $this->order->getSaferpayToken();

        $paycomplete_url = $this->getCompleteUrl($saferpayId, null);

        $cs = curl_init($paycomplete_url);
        curl_setopt($cs, CURLOPT_PORT, 443); // set option for outgoing SSL requests via CURL
        curl_setopt($cs, CURLOPT_SSL_VERIFYPEER, false); // ignore SSL-certificate-check - session still SSL-safe
        curl_setopt($cs, CURLOPT_HEADER, 0); // no header in output
        curl_setopt ($cs, CURLOPT_RETURNTRANSFER, true); // receive returned characters

        $answer = curl_exec($cs);
        curl_close($cs);

        if (strtoupper($answer) != "OK") {
            $this->Log('processPaymentAfterOrder', $answer);
            $this->addError($answer);
            $this->order->setOrderStatusByID($this->canceledOrderStatus);
            $this->order->sendConfirmationMail();

            return false;
        } else {
            $this->order->setOrderStatusByID($this->successOrderStatus);
            $this->order->sendConfirmationMail();

            parent::processPaymentAfterOrder($this->order);
        }
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
     * Get the message for the given ECI identifier.
     *
     * @param string $eci The ECI identifier
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.10.2012
     */
    public function getEciMsg($eci) {
        switch($eci) {
            case "2":
                $ecimsg = "Liability-shift without authentication ! (ECI = '2')";
                break;
            case "1":
                $ecimsg = "Liability-shift granted - with authentication ! (ECI = '1')";
                break;
            default:
                $ecimsg = "Liability-shift not granted ! (ECI = '".$eci."')";
                break;
        }

        return $ecimsg;
    }

    /**
     * Set the title for the submit button on the order confirmation step.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.10.2012
     */
    public function getOrderConfirmationSubmitButtonTitle() {
        return _t('SilvercartPaymentSaferpay.ORDER_CONFIRMATION_SUBMIT_BUTTON_TITLE');
    }

    /**
     * getter for the multilingual attribute paypalInfotextCheckout
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.10.2012
     */
    public function getSaferpayInfotextCheckout() {
        $text = '';
        if ($this->getLanguage()) {
            $text = $this->getLanguage()->saferpayInfotextCheckout;
        }
        return $text;
    }

    /**
     * Returns the payment URL
     *
     * @param string $id    The ID for the request
     * @param string $token The token for the request
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@standardized.de>
     * @since 01.10.2012
     */
    protected function getCompleteUrl($id, $token) {
        $attributes  = "?ACCOUNTID=".$this->getAccountId();
        $attributes .= "&ID=".urlencode($id);
        $attributes .= "&TOKEN=".urlencode($token);

        $paycomplete_url = $this->saferpayPaycompleteGateway.$attributes;

        // **************************************************
        // * Special for testaccount: Passwort for hosting-capture neccessary.
        // * Not needed for standard-saferpay-eCommerce-accounts
        // **************************************************
        if (substr($this->getAccountId(), 0, 6) == "99867-") {
            $paycomplete_url .= "&spPassword=XAjc3Kna";
        }

        return $paycomplete_url;
    }

    /**
     * Returns the payment URL
     *
     * @param string $data      The data string from saferpay
     * @param string $signature The signature string from saferpay
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@standardized.de>
     * @since 01.10.2012
     */
    protected function getConfirmationUrl($data, $signature) {
        $attributes  = "?DATA=".urlencode($data);
        $attributes .= "&SIGNATURE=".urlencode($signature);

        $payconfirm_url = $this->saferpayPayconfirmGateway.$attributes;

        return $payconfirm_url;
    }

    /**
     * Returns the payment URL
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@standardized.de>
     * @since 01.10.2012
     */
    protected function getPaymentUrl() {
        $checkoutData = $this->controller->getCombinedStepData();
        $shoppingCart = $this->getShoppingCart();

        if (array_key_exists('ShippingMethod', $checkoutData)) {
            $shoppingCart->setShippingMethodID($checkoutData['ShippingMethod']);
        }
        if (array_key_exists('PaymentMethod', $checkoutData)) {
            $shoppingCart->setPaymentMethodID($checkoutData['PaymentMethod']);
        }

        $totalAmount   = $shoppingCart->getAmountTotal();
        $saferpayToken = $this->createSaferpayToken();
        $shoppingCart->saveSaferpayToken($saferpayToken);

        $showLanguages = $this->showLanguages ? 'yes' : 'no';
        $cccvc         = $this->cccvc ?  'yes' : 'no';
        $ccname        = $this->ccname ? 'yes' : 'no';

        // Mandatory attributes
        $attributes  = "?ACCOUNTID=".       $this->getAccountId();
        $attributes .= "&AMOUNT=".          $totalAmount->getAmount() * 100;
        $attributes .= "&CURRENCY=".        $totalAmount->getCurrency();
        $attributes .= "&DELIVERY=no";
        $attributes .= "&DESCRIPTION=".     urlencode($this->getDescription());
        $attributes .= "&SUCCESSLINK=".     $this->getReturnLink();
        $attributes .= "&FAILLINK=".        $this->getReturnLink();
        $attributes .= "&BACKLINK=".        $this->getReturnLink().'Goto/Step/2';
        $attributes .= "&NOTIFIYURL=".      $this->getNotificationUrl();
        $attributes .= "&AUTOCLOSE=".       (int) $this->autoclose;
        $attributes .= "&SHOWLANGUAGES=".   $showLanguages;
        $attributes .= "&CCCVC=".           $cccvc;
        $attributes .= "&CCNAME=".          $ccname;

        // Shop specific attributes
        $attributes .= "&ORDERID=".urlencode($saferpayToken);

        $payinit_url = $this->saferpayPayinitGateway.$attributes;

        // Create CURL session
        $cs = curl_init($payinit_url);
        
        // Set CURL session options
        curl_setopt($cs, CURLOPT_PORT, 443);                // set option for outgoing SSL requests via CURL
        curl_setopt($cs, CURLOPT_SSL_VERIFYPEER, false);    // ignore SSL-certificate-check - session still SSL-safe
        curl_setopt($cs, CURLOPT_HEADER, 0);                // no header in output
        curl_setopt($cs, CURLOPT_RETURNTRANSFER, true);     // receive returned characters
        
        // Execute CURL session
        $paymentUrl = curl_exec($cs);
        
        // Close CURL session
        $ce = curl_error($cs);
        curl_close($cs);
        
        // Stop if CURL is not working
        if (strtolower(substr($paymentUrl, 0, 24)) != "https://www.saferpay.com") {
            $msg = "<p>PHP-CURL is not working correctly for outgoing SSL-calls on your server:<br/>";
            $msg .= htmlentities($paymentUrl)."<br/>";
            $msg .= htmlentities($ce)."</p>";
            $this->addError($msg);

            return false;
        }
        
        return $paymentUrl;
    }

    /**
     * Creates and relates required order status and logo images.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@standardized.de>
     * @since 01.10.2012
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        $requiredStatus = array(
            'payed'             => _t('SilvercartOrderStatus.PAYED'),
            'saferpay_success'  => _t('SilvercartOrderStatus.SAFERPAY_SUCCESS'),
            'saferpay_error'    => _t('SilvercartOrderStatus.SAFERPAY_ERROR'),
            'saferpay_canceled' => _t('SilvercartOrderStatus.SAFERPAY_CANCELED')
        );
        $paymentLogos = array(
            'Saferpay'  => SilvercartTools::getBaseURLSegment().'/silvercart_payment_saferpay/images/saferpay.png',
        );

        parent::createRequiredOrderStatus($requiredStatus);
        parent::createLogoImageObjects($paymentLogos, 'SilvercartPaymentSaferpay');

        $paymentMethods = DataObject::get('SilvercartPaymentSaferpay', "`paidOrderStatus`=0");
        if ($paymentMethods) {
            foreach ($paymentMethods as $paymentMethod) {
                $paymentMethod->paidOrderStatus    = DataObject::get_one('SilvercartOrderStatus', "`Code`='payed'")->ID;
                $paymentMethod->successOrderStatus = DataObject::get_one('SilvercartOrderStatus', "`Code`='saferpay_success'")->ID;
                $paymentMethod->failedOrderStatus  = DataObject::get_one('SilvercartOrderStatus', "`Code`='saferpay_error'")->ID;

                $paymentMethod->setField('saferpayPayinitGateway',     'https://www.saferpay.com/hosting/CreatePayInit.asp');
                $paymentMethod->setField('saferpayPayconfirmGateway',  'https://www.saferpay.com/hosting/VerifyPayConfirm.asp');
                $paymentMethod->setField('saferpayPaycompleteGateway', 'https://www.saferpay.com/hosting/PayComplete.asp');
                $paymentMethod->setField('autoclose',                  0);
                $paymentMethod->setField('showLanguages',              0);
                $paymentMethod->setField('cccvc',                      1);
                $paymentMethod->setField('ccname',                     1);

                $paymentMethod->write();
            }
        }
    }
}
