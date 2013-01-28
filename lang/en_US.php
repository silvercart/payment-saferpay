<?php
/**
 * Copyright 2012 pixeltricks GmbH
 *
 * This file is part of SilvercartPaymentPaypal.
 *
 * SilvercartPaypalPayment is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilvercartPaymentPaypal is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilvercartPaymentPaypal.  If not, see <http://www.gnu.org/licenses/>.
 *
 * English (US) language pack
 *
 * @package Silvercart
 * @subpackage i18n
 * @ignore
 */

global $lang;









$lang['en_US']['SilvercartOrderStatus']['SAFERPAY_CANCELED']    = 'Saferpay canceled';
$lang['en_US']['SilvercartOrderStatus']['SAFERPAY_ERROR']       = 'Saferpay error';
$lang['en_US']['SilvercartOrderStatus']['SAFERPAY_SUCCESS']     = 'Paid via Saferpay';

$lang['en_US']['SilvercartPaymentSaferpay']['API_ACCOUNTID']                          = 'Account ID';
$lang['en_US']['SilvercartPaymentSaferpay']['API_PASSWORD']                           = 'API Password (ONLY FOR BUSINESS ACCOUNTS. LEAVE EMPTY IF NOT NEEDED.)';
$lang['en_US']['SilvercartPaymentSaferpay']['API_DEVELOPMENT_MODE']                   = 'API development mode';
$lang['en_US']['SilvercartPaymentSaferpay']['API_LIVE_MODE']                          = 'API live mode';
$lang['en_US']['SilvercartPaymentSaferpay']['AUTOCLOSE']                              = 'number of seconds (0 to n) after which the payer is automatically forwarded';
$lang['en_US']['SilvercartPaymentSaferpay']['CCCVC']                                  = 'Query for card verification value (CVV/CVC2)';
$lang['en_US']['SilvercartPaymentSaferpay']['CCNAME']                                 = 'Ask the name of the card or account holder';
$lang['en_US']['SilvercartPaymentSaferpay']['ENTER_DATA_AT_SAFERPAY']                 = 'Pay at Saferpay';
$lang['en_US']['SilvercartPaymentSaferpay']['INFOTEXT_CHECKOUT']                      = 'payment via Saferpay';
$lang['en_US']['SilvercartPaymentSaferpay']['ORDERSTATUS_CANCELED']                   = 'orderstatus for notification "canceled"';
$lang['en_US']['SilvercartPaymentSaferpay']['ORDERSTATUS_PAYED']                      = 'orderstatus for notification "payed"';
$lang['en_US']['SilvercartPaymentSaferpay']['ORDER_CONFIRMATION_SUBMIT_BUTTON_TITLE'] = 'Proceed to payment via Saferpay';
$lang['en_US']['SilvercartPaymentSaferpay']['PLURALNAME']                             = 'Saferpay';
$lang['en_US']['SilvercartPaymentSaferpay']['SINGULARNAME']                           = 'Saferpay';
$lang['en_US']['SilvercartPaymentSaferpay']['SHOWLANGUAGES']                          = 'Display of the language selection in the menu of the Saferpay VT';
$lang['en_US']['SilvercartPaymentSaferpay']['URL_PAYCONFIRM_GATEWAY']                 = 'Payconfirm gateway URL';
$lang['en_US']['SilvercartPaymentSaferpay']['URL_PAYCOMPLETE_GATEWAY']                = 'Paycomplete gateway URL';
$lang['en_US']['SilvercartPaymentSaferpay']['URL_PAYINIT_GATEWAY']                    = 'Payinit gateway URL';

$lang['en_US']['SilvercartPaymentSaferpayError']['ERROR_1'] = 'Required attributes are missing in request';
$lang['en_US']['SilvercartPaymentSaferpayError']['ERROR_2'] = 'Not all data has been sent by Saferpay';
$lang['en_US']['SilvercartPaymentSaferpayError']['ERROR_3'] = 'A wrong accountId has been sent by Saferpay';
$lang['en_US']['SilvercartPaymentSaferpayError']['ERROR_4'] = 'A wrong token has been sent by Saferpay';
$lang['en_US']['SilvercartPaymentSaferpayError']['ERROR_5'] = 'Saferpay verification failed';
$lang['en_US']['SilvercartPaymentSaferpayError']['ERROR_6'] = 'Canceled by Saferpay';

$lang['en_US']['SilvercartPaymentSaferpayLanguage']['SINGULARNAME'] = 'Translation of the payment method Saferpay';
$lang['en_US']['SilvercartPaymentSaferpayLanguage']['PLURALNAME']   = 'Translations of the payment method Saferpay';