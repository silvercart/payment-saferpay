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
 * @package Silvercart
 * @subpackage Payment_Saferpay
 */

/**
 * Enables payment via saferpay.
 *
 * @package Silvercart
 * @subpackage Payment_Saferpay
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
     * transaction status true
     *
     * @var array
     */
    protected $successStatus = array(
        'AUTHORIZED',
        'CAPTURED',
    );

    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'sfCanceledOrderStatus'     => 'Int',
        'sfPaidOrderStatus'         => 'Int',
        'sfSuccessOrderStatus'      => 'Int',

        'RestrictPaymentMethods'        => 'VarChar(100)',
        'saferpayAccountId_Dev'         => 'VarChar(100)',
        'saferpayTerminalId_Dev'        => 'VarChar(100)',
        'saferpayApiUsername_Dev'       => 'VarChar(100)',
        'saferpayAccountPassword_Dev'   => 'VarChar(100)',
        'saferpayAccountId_Live'        => 'VarChar(100)',
        'saferpayTerminalId_Live'       => 'VarChar(100)',
        'saferpayApiUsername_Live'      => 'VarChar(100)',
        'saferpayAccountPassword_Live'  => 'VarChar(100)',
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
        'saferpayAccountId_Dev'       => '401860',
        'saferpayTerminalId_Dev'      => '17795484',
        'saferpayApiUsername_Dev'     => 'API_401860_80003225',
        'saferpayAccountPassword_Dev' => 'C-y*bv8346Ze5-T8',
    );

    const SAFERPAY_BASE_URL          = 'https://www.saferpay.com/api';
    const SAFERPAY_BASE_URL_TEST     = 'https://test.saferpay.com/api';
    const PAYMENTPAGE_INITIALIZE_URL = '/Payment/v1/PaymentPage/Initialize';
    const PAYMENTPAGE_ASSERT_URL     = '/Payment/v1/PaymentPage/Assert';
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.04.2014
     */
    public function singular_name() {
        SilvercartTools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.04.2014
     */
    public function plural_name() {
        SilvercartTools::plural_name_for($this);
    }

    /**
     * Creates a unique saferpay token.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.04.2014
     */
    public function createSaferpayToken() {
        $member = Member::currentUser();
        $token  = $member->ID . '-' . time();

        return $token;
    }

    /**
     * Returns the saferpay account ID
     *
     * @return string The account ID
     */
    public function getAccountId() {
        $accountID = null;
        if ($this->mode == 'Live') {
            $accountID = $this->saferpayAccountId_Live;
        } else {
            $accountID = $this->saferpayAccountId_Dev;
        }
        return $accountID;
    }

    /**
     * Returns the saferpay TermnalId
     *
     * @return string
     */
    public function getTerminalId() {
        $accountID = null;
        if ($this->mode == 'Live') {
            $accountID = $this->saferpayTerminalId_Live;
        } else {
            $accountID = $this->saferpayTerminalId_Dev;
        }
        return $accountID;
    }

    /**
     * Returns the saferpay API username
     *
     * @return string
     */
    public function getApiUsername() {
        $apiUsername = null;
        if ($this->mode == 'Live') {
            $apiUsername = $this->saferpayApiUsername_Live;
        } else {
            $apiUsername = $this->saferpayApiUsername_Dev;
        }
        return $apiUsername;
    }

    /**
     * Returns the saferpay password dependant on dev/live mode
     * 
     * Special for testaccount: password for hosting-capture neccessary.
     * Special for bussiness account: password for hosting-capture neccessary.
     * Not needed for standard-saferpay-eCommerce-accounts
     *
     * @return string
     */
    public function getPassword() {
        $password = null;
        if ($this->mode == 'Live') {
            $password = $this->saferpayAccountPassword_Live;
            if (empty($password)) {
                $password = null;
            }
        } else {
            $password = $this->saferpayAccountPassword_Dev;
        }
        return $password;
    }

    /**
     * Returns the restricted payment methods as an array.
     *
     * @return array
     */
    public function getRestrictPaymentMethodsArray() {
        $restrictPaymentMethodsArray = array();
        $restrictPaymentMethods      = trim($this->RestrictPaymentMethods);
        if (!empty($restrictPaymentMethods)) {
            $restrictPaymentMethodsArray = explode(',', $restrictPaymentMethods);
            foreach ($restrictPaymentMethodsArray as $key => $value) {
                $restrictPaymentMethodsArray[$key] = trim($value);
            }
        }
        return $restrictPaymentMethodsArray;
    }

    /**
     * Returns the description of the order.
     *
     * @return string
     */
    public function getDescription() {
        if ($this->description == null) {
            $templateVariables = new ArrayData(array(
                'SilvercartShoppingCart' => $this->getShoppingCart()
            ));
            $template          = new SSViewer('saferpayDescription');
            $this->description = HTTP::absoluteURLs($template->process($templateVariables));
        }

        return str_replace(PHP_EOL, '', trim($this->description));
    }

    /**
     * i18n for field labels
     *
     * @param boolean $includerelations a boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.04.2014
     */
    public function fieldLabels($includerelations = true) {
        $labels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'SaferpayApiData'                     => _t('SilvercartPaymentSaferpay.SaferpayApiData'),
                    'SaferpaySettings'                    => _t('SilvercartPaymentSaferpay.SaferpaySettings'),
                    'saferpayAccountId_Dev'               => _t('SilvercartPaymentSaferpay.API_ACCOUNTID'),
                    'saferpayTerminalId_Dev'              => _t('SilvercartPaymentSaferpay.ApiTerminalId'),
                    'saferpayApiUsername_Dev'             => _t('SilvercartPaymentSaferpay.ApiUsername'),
                    'saferpayAccountPassword_Dev'         => _t('SilvercartPaymentSaferpay.API_PASSWORD'),
                    'saferpayAccountId_Live'              => _t('SilvercartPaymentSaferpay.API_ACCOUNTID'),
                    'saferpayTerminalId_Live'             => _t('SilvercartPaymentSaferpay.ApiTerminalId'),
                    'saferpayApiUsername_Live'            => _t('SilvercartPaymentSaferpay.ApiUsername'),
                    'saferpayAccountPassword_Live'        => _t('SilvercartPaymentSaferpay.API_PASSWORD'),
                    'saferpayAccountPasswordDesc'         => _t('SilvercartPaymentSaferpay.ApiPasswordDesc'),
                    'RestrictPaymentMethods'              => _t('SilvercartPaymentSaferpay.RestrictPaymentMethods'),
                    'RestrictPaymentMethodsDesc'          => _t('SilvercartPaymentSaferpay.RestrictPaymentMethodsDesc'),
                    'sfPaidOrderStatus'                   => _t('SilvercartPaymentSaferpay.sfPaidOrderStatus'),
                    'sfCanceledOrderStatus'               => _t('SilvercartPaymentSaferpay.sfCanceledOrderStatus'),
                    'sfSuccessOrderStatus'                => _t('SilvercartPaymentSaferpay.sfSuccessOrderStatus'),
                    'SilvercartPaymentSaferpayLanguages'  => _t('SilvercartPaymentSaferpayLanguage.PLURALNAME'),
                    'TabOrderStatus'                      => _t('SilvercartPaymentSaferpay.TabOrderStatus'),
                )
        );
        
        return $labels;
    }
    
    /**
     * Adds the fields for the Saferpay API
     *
     * @param FieldList $fields FieldList to add fields to
     * @param bool      $forDev Add fields for dev or live mode?
     * 
     * @return void
     */
    protected function getFieldsForAPI($fields, $forDev = false) {
        if ($forDev) {
            $mode = 'Dev';
            $passwordField = new TextField('saferpayAccountPassword_Dev', $this->fieldLabel('saferpayAccountPassword_Dev'));
            $passwordField->setDescription($this->fieldLabel('saferpayAccountPasswordDesc'));
            $fieldlist = array(
                new TextField('saferpayAccountId_Dev',       $this->fieldLabel('saferpayAccountId_Dev')),
                new TextField('saferpayTerminalId_Dev',      $this->fieldLabel('saferpayTerminalId_Dev')),
                new TextField('saferpayApiUsername_Dev',     $this->fieldLabel('saferpayApiUsername_Dev')),
                $passwordField,
            );
        } else {
            $mode = 'Live';
            $passwordField = new TextField('saferpayAccountPassword_Live', $this->fieldLabel('saferpayAccountPassword_Live'));
            $passwordField->setDescription($this->fieldLabel('saferpayAccountPasswordDesc'));
            $fieldlist = array(
                new TextField('saferpayAccountId_Live',       $this->fieldLabel('saferpayAccountId_Live')),
                new TextField('saferpayTerminalId_Live',      $this->fieldLabel('saferpayTerminalId_Live')),
                new TextField('saferpayApiUsername_Live',     $this->fieldLabel('saferpayApiUsername_Live')),
                $passwordField,
            );
        }
        
        $apiDataToggle = ToggleCompositeField::create(
                'SaferpayAPI' . $mode,
                $this->fieldLabel('SaferpayApiData') . ' "' . $this->fieldLabel('mode' . $mode) . '"',
                $fieldlist
        )->setHeadingLevel(4)->setStartClosed(true);
        
        $fields->addFieldToTab('Root.Basic', $apiDataToggle);
    }
    
    /**
     * Adds the fields for the Saferpay settings
     *
     * @param FieldList $fields FieldList to add fields to
     * 
     * @return void
     */
    protected function getFieldsForSettings($fields) {
        $restrictPaymentMethodsField = new TextField('RestrictPaymentMethods', $this->fieldLabel('RestrictPaymentMethods'));
        $restrictPaymentMethodsField->setDescription($this->fieldLabel('RestrictPaymentMethodsDesc'));
        $fieldlist = array(
            $restrictPaymentMethodsField,
        );
        
        $settingsDataToggle = ToggleCompositeField::create(
                'Settings',
                $this->fieldLabel('SaferpaySettings'),
                $fieldlist
        )->setHeadingLevel(4)->setStartClosed(true);
        
        $fields->addFieldToTab('Root.Basic', $settingsDataToggle);
    }
    
    /**
     * Adds the fields for the PayPal order status
     *
     * @param FieldList $fields FieldList to add fields to
     * 
     * @return void
     */
    protected function getFieldsForOrderStatus($fields) {
        $orderStatus = DataObject::get('SilvercartOrderStatus');
        $fieldlist = array(
                $fields->dataFieldByName('orderStatus'),
                new DropdownField('sfPaidOrderStatus',     $this->fieldLabel('sfPaidOrderStatus'),     $orderStatus->map('ID', 'Title'), $this->sfPaidOrderStatus),
                new DropdownField('sfCanceledOrderStatus', $this->fieldLabel('sfCanceledOrderStatus'), $orderStatus->map('ID', 'Title'), $this->sfCanceledOrderStatus),
                new DropdownField('sfSuccessOrderStatus',  $this->fieldLabel('sfSuccessOrderStatus'),  $orderStatus->map('ID', 'Title'), $this->sfSuccessOrderStatus),
        );
        
        $orderStatusDataToggle = ToggleCompositeField::create(
                'OrderStatus',
                $this->fieldLabel('TabOrderStatus'),
                $fieldlist
        )->setHeadingLevel(4)->setStartClosed(true);
        
        $fields->removeByName('orderStatus');
        
        $fields->addFieldToTab('Root.Basic', $orderStatusDataToggle);
    }

    /**
     * returns CMS fields
     *
     * @param mixed $params optional
     *
     * @return FieldList
     */
    public function getCMSFields($params = null) {
        $fields = parent::getCMSFieldsForModules($params);

        $this->getFieldsForOrderStatus($fields);
        $this->getFieldsForAPI($fields, true);
        $this->getFieldsForAPI($fields);
        $this->getFieldsForSettings($fields);
        
        $translations = new GridField(
                'SilvercartPaymentSaferpayLanguages',
                $this->fieldLabel('SilvercartPaymentSaferpayLanguages'),
                $this->SilvercartPaymentSaferpayLanguages(),
                SilvercartGridFieldConfig_ExclusiveRelationEditor::create()
        );
        $fields->addFieldToTab('Root.Translations', $translations);

        return $fields;
    }

    // ------------------------------------------------------------------------
    // Saferpay URLs
    // ------------------------------------------------------------------------
    
    /**
     * Returns the Saferpay API base URL.
     * 
     * @return string
     */
    public function getSaferpayApiBaseUrl() {
        $baseURL = null;
        if ($this->mode == 'Live') {
            $baseURL = self::SAFERPAY_BASE_URL;
        } else {
            $baseURL = self::SAFERPAY_BASE_URL_TEST;
        }
        return $baseURL;
    }
    
    /**
     * Returns the Saferpay API PaymentPage Initialize URL.
     * 
     * @return string
     */
    public function getSaferpayApiPaymentPageInitializeUrl() {
        return $this->getSaferpayApiBaseUrl() . self::PAYMENTPAGE_INITIALIZE_URL;
    }
    
    /**
     * Returns the Saferpay API PaymentPage Initialize URL.
     * 
     * @return string
     */
    public function getSaferpayApiPaymentPageAssertUrl() {
        return $this->getSaferpayApiBaseUrl() . self::PAYMENTPAGE_ASSERT_URL;
    }

    // ------------------------------------------------------------------------
    // Saferpay API connections
    // ------------------------------------------------------------------------
    
    /**
     * Saferpay PaymentPage Initialize API call.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.11.2016
     */
    protected function paymentPageInitialize() {
        $checkoutData  = $this->controller->getCombinedStepData();
        $shoppingCart  = $this->getShoppingCart();
        $totalAmount   = $shoppingCart->getAmountTotal();
        $saferpayToken = $this->createSaferpayToken();
        $shoppingCart->saveSaferpayToken($saferpayToken);

        if (array_key_exists('ShippingMethod', $checkoutData)) {
            $shoppingCart->setShippingMethodID($checkoutData['ShippingMethod']);
        }
        if (array_key_exists('PaymentMethod', $checkoutData)) {
            $shoppingCart->setPaymentMethodID($checkoutData['PaymentMethod']);
        }
        
        $jsonArray = array(
            'RequestHeader' => array(
                'SpecVersion'    => '1.3',
                'CustomerId'     => $this->getAccountId(),
                'RequestId'      => $saferpayToken,
                'RetryIndicator' => 0,
            ),
            'TerminalId' => $this->getTerminalId(),
            'Payment' => array(
                'Amount' => array(
                    'Value'        => $totalAmount->getAmount() * 100,
                    'CurrencyCode' => $totalAmount->getCurrency(),
                ),
                'OrderId'     => $saferpayToken,
                'Description' => $this->getDescription(),
            ),
            'ReturnUrls'  => array(
                'Success' => $this->getReturnLink(),
                'Fail'    => $this->getCancelLink(),
                'Abort'   => $this->getCancelLink(),
            ),
        );
        
        $restrictPaymentMethodsArray = $this->getRestrictPaymentMethodsArray();
        if (count($restrictPaymentMethodsArray) > 0) {
            $jsonArray['PaymentMethods'] = $restrictPaymentMethodsArray;
        }
        $jsonRequest = json_encode($jsonArray);
                
        $cs = curl_init($this->getSaferpayApiPaymentPageInitializeUrl());
        curl_setopt($cs, CURLOPT_PORT, 443);
        curl_setopt($cs, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($cs, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($cs, CURLOPT_HEADER, 0);
        curl_setopt($cs, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cs, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
        ));
        curl_setopt($cs, CURLOPT_POST, true);
        curl_setopt($cs, CURLOPT_POSTFIELDS, $jsonRequest);
        curl_setopt($cs, CURLOPT_USERPWD, $this->getApiUsername() . ":" . $this->getPassword());
        $jsonResponse = curl_exec($cs);
        $httpStatus   = curl_getinfo($cs, CURLINFO_HTTP_CODE);
        $errorOccured = $this->isSaferpayApiError($httpStatus, $this->getSaferpayApiPaymentPageAssertUrl(), $jsonRequest, $jsonResponse, $cs);
        curl_close($cs);
        
        if ($errorOccured) {
            return false;
        }
        
        $response = json_decode($jsonResponse, true);
        return $response;
    }
    
    /**
     * Saferpay PaymentPage Initialize API call.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.11.2016
     */
    protected function paymentPageAssert() {
        $shoppingCart  = $this->getShoppingCart();
        $saferpayToken = $shoppingCart->getSaferpayToken();
        $saferpayID    = $shoppingCart->getSaferpayID();
        
        $jsonArray = array(
            'RequestHeader' => array(
                'SpecVersion'    => '1.3',
                'CustomerId'     => $this->getAccountId(),
                'RequestId'      => $saferpayToken,
                'RetryIndicator' => 0,
            ),
            'Token' => $saferpayID,
        );
        $jsonRequest = json_encode($jsonArray);
                
        $cs = curl_init($this->getSaferpayApiPaymentPageAssertUrl());
        curl_setopt($cs, CURLOPT_PORT, 443);
        curl_setopt($cs, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($cs, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($cs, CURLOPT_HEADER, 0);
        curl_setopt($cs, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cs, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
        ));
        curl_setopt($cs, CURLOPT_POST, true);
        curl_setopt($cs, CURLOPT_POSTFIELDS, $jsonRequest);
        curl_setopt($cs, CURLOPT_USERPWD, $this->getApiUsername() . ":" . $this->getPassword());
        $jsonResponse = curl_exec($cs);
        $httpStatus   = curl_getinfo($cs, CURLINFO_HTTP_CODE);
        $errorOccured = $this->isSaferpayApiError($httpStatus, $this->getSaferpayApiPaymentPageAssertUrl(), $jsonRequest, $jsonResponse, $cs);
        curl_close($cs);
        
        if ($errorOccured) {
            return false;
        }
        
        $response = json_decode($jsonResponse, true);
        return $response;
    }
    
    /**
     * Checks if a Saferpay API error occured.
     * Logs the errors.
     * 
     * @param int      $httpStatus     HTTP status
     * @param string   $targetUrl      Target URL
     * @param string   $jsonRequest    JSON request data
     * @param string   $jsonResponse   JSON response data
     * @param recource $curlConnection CURL connection resource
     * 
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.12.2016
     */
    protected function isSaferpayApiError($httpStatus, $targetUrl, $jsonRequest, $jsonResponse, $curlConnection) {
        if ($httpStatus != 200) {
            $this->Log('paymentPageAssert', 'Error: call to URL ' . $targetUrl . ' failed with status ' . $httpStatus . '.');
            $this->Log('paymentPageAssert', ' - JSON request: ' . $jsonRequest);
            $this->Log('paymentPageAssert', ' - JSON response: ' . $jsonResponse);
            $this->Log('paymentPageAssert', ' - CURL error: ' . curl_error($curlConnection));
            $this->Log('paymentPageAssert', ' - CURL errno: ' . curl_errno($curlConnection));
            $this->Log('paymentPageAssert', ' - HTTP-Status: ' . $httpStatus);
            $msg = "<p>An error occured while trying to connect to Saferpay. Please try again.</p>";
            $this->addError($msg);
            return true;
        }
        return false;
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.12.2016
     */
    public function processPaymentBeforeOrder() {
        $paymentData   = $this->paymentPageInitialize();
        $shoppingCart  = $this->getShoppingCart();
        $paymentUrl    = $paymentData['RedirectUrl'];
        $saferpayToken = $paymentData['Token'];
        $shoppingCart->saveSaferpayID($saferpayToken);

        if ($paymentUrl === false) {
            return false;
        } else {
            $this->controller->addCompletedStep($this->controller->getCurrentStep());
            $this->controller->setCurrentStep($this->controller->getNextStep());

            $this->controller->redirect($paymentUrl);
        }
    }
    
    /**
     * hook to be called after jumpback from payment provider; called before
     * order creation
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.12.2016
     */
    public function processReturnJumpFromPaymentProvider() {
        $paymentResult     = $this->paymentPageAssert();
        $transactionStatus = $paymentResult['Transaction']['Status'];

        if (in_array($transactionStatus, $this->successStatus)) {
            $this->Log('processReturnJumpFromPaymentProvider', 'PAYMENT SUCCESSFUL');
            $this->Log('processReturnJumpFromPaymentProvider', ' - ' . var_export($paymentResult, true));
            parent::processReturnJumpFromPaymentProvider();
        } else {
            $this->Log('processReturnJumpFromPaymentProvider', 'AN ERROR OCCURED');
            $this->Log('processReturnJumpFromPaymentProvider', ' - ' . var_export($paymentResult, true));
            $errorMsg = _t('SilvercartPaymentSaferpayError.ERROR_6');
            $this->addError($errorMsg);
            return false;
        }
    }

    /**
     * hook to be called after order creation
     *
     * @param array $orderObj object to be processed
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.12.2016
     */
    public function processPaymentAfterOrder($orderObj = array()) {
        $this->order->setOrderStatusByID($this->sfSuccessOrderStatus);
        $this->order->sendConfirmationMail();
        parent::processPaymentAfterOrder($this->order);
    }

    /**
     * possibility to return a text at the end of the order process
     * processed after order creation
     *
     * @param Order $orderObj the order object
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.12.2016
     */
    public function processPaymentConfirmationText($orderObj) {
    }

    // ------------------------------------------------------------------------
    // payment module specific methods
    // ------------------------------------------------------------------------

    /**
     * Set the title for the submit button on the order confirmation step.
     *
     * @return string
     */
    public function getOrderConfirmationSubmitButtonTitle() {
        return _t('SilvercartPaymentSaferpay.ORDER_CONFIRMATION_SUBMIT_BUTTON_TITLE');
    }

    /**
     * Creates and relates required order status and logo images.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
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
            'Saferpay'  => '/silvercart_payment_saferpay/images/saferpay.png',
        );

        parent::createRequiredOrderStatus($requiredStatus);
        parent::createLogoImageObjects($paymentLogos, 'SilvercartPaymentSaferpay');

        $paymentMethods = SilvercartPaymentSaferpay::get()->filter("sfPaidOrderStatus", 0);
        if ($paymentMethods->exists()) {
            foreach ($paymentMethods as $paymentMethod) {
                $paymentMethod->sfPaidOrderStatus    = SilvercartOrderStatus::get()->filter("Code", "payed")->first()->ID;
                $paymentMethod->sfPaidOrderStatus    = SilvercartOrderStatus::get()->filter("Code", "saferpay_success")->first()->ID;
                $paymentMethod->sfPaidOrderStatus    = SilvercartOrderStatus::get()->filter("Code", "saferpay_error")->first()->ID;
                $paymentMethod->write();
            }
        }
    }
}
