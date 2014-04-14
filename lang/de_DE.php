<?php
/**
 * Copyright 2012 pixeltricks GmbH
 *
 * This file is part of SilvercartPrepaymentPayment.
 *
 * SilvercartPaypalPayment is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilvercartPrepaymentPayment is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilvercartPrepaymentPayment.  If not, see <http://www.gnu.org/licenses/>.
 *
 * German (Germany) language pack
 *
 * @package Silvercart
 * @subpackage i18n
 * @ignore
 */

global $lang;

$lang['de_DE']['SilvercartOrderStatus']['SAFERPAY_CANCELED']    = 'Saferpay abgebrochen';
$lang['de_DE']['SilvercartOrderStatus']['SAFERPAY_ERROR']       = 'Saferpay Fehler';
$lang['de_DE']['SilvercartOrderStatus']['SAFERPAY_SUCCESS']     = 'Bezahlt via Saferpay';

$lang['de_DE']['SilvercartPaymentSaferpay']['API_ACCOUNTID']                          = 'Account ID';
$lang['de_DE']['SilvercartPaymentSaferpay']['API_PASSWORD']                           = 'API Password (Wird nur für Saferpay Business Accounts benötigt. Wenn nicht benötigt, leer lassen.)';
$lang['de_DE']['SilvercartPaymentSaferpay']['API_DEVELOPMENT_MODE']                   = 'API Entwicklungsmodus';
$lang['de_DE']['SilvercartPaymentSaferpay']['API_LIVE_MODE']                          = 'API Live Modus';
$lang['de_DE']['SilvercartPaymentSaferpay']['AUTOCLOSE']                              = 'Anzahl Sekunden (0 bis n) bis zur automatischen Weiterleitung nach erfolgreicher Zahlung';
$lang['de_DE']['SilvercartPaymentSaferpay']['CCCVC']                                  = 'Prüfsumme der Kreditkarte abfragen';
$lang['de_DE']['SilvercartPaymentSaferpay']['CCNAME']                                 = 'Name des Kreditkartenbesitzers abfragen';
$lang['de_DE']['SilvercartPaymentSaferpay']['ENTER_DATA_AT_SAFERPAY']                 = 'Bezahlung bei Saferpay durchführen';
$lang['de_DE']['SilvercartPaymentSaferpay']['INFOTEXT_CHECKOUT']                      = 'Die Zahlung erfolgt per Saferpay';
$lang['de_DE']['SilvercartPaymentSaferpay']['ORDER_CONFIRMATION_SUBMIT_BUTTON_TITLE'] = 'Kaufen & weiter zur Bezahlung bei Saferpay';
$lang['de_DE']['SilvercartPaymentSaferpay']['ORDERSTATUS_CANCELED']                   = 'Bestellstatus für Meldung "abgebrochen"';
$lang['de_DE']['SilvercartPaymentSaferpay']['ORDERSTATUS_PAYED']                      = 'Bestellstatus für Meldung "bezahlt"';
$lang['de_DE']['SilvercartPaymentSaferpay']['PLURALNAME']                             = 'Saferpay';
$lang['de_DE']['SilvercartPaymentSaferpay']['SINGULARNAME']                           = 'Saferpay';
$lang['de_DE']['SilvercartPaymentSaferpay']['SHOWLANGUAGES']                          = 'Anzeige der Sprachauswahl im Saferpay VT Menü';
$lang['de_DE']['SilvercartPaymentSaferpay']['URL_PAYCONFIRM_GATEWAY']                 = 'Payconfirm Gateway URL';
$lang['de_DE']['SilvercartPaymentSaferpay']['URL_PAYCOMPLETE_GATEWAY']                = 'PaycompleteGateway URL';
$lang['de_DE']['SilvercartPaymentSaferpay']['URL_PAYINIT_GATEWAY']                    = 'Payinit Gateway URL';
$lang['de_DE']['SilvercartPaymentSaferpay']['SaferpayApiData']                        = 'Saferpay Zugangsdaten';
$lang['de_DE']['SilvercartPaymentSaferpay']['SaferpaySettings']                       = 'Saferpay Einstellungen';

$lang['de_DE']['SilvercartPaymentSaferpayOrder']['SAFERPAY_IDENTIFIER'] = 'Saferpay Erkennungsnr.';
$lang['de_DE']['SilvercartPaymentSaferpayOrder']['SAFERPAY_TOKEN']      = 'Saferpay Zeichen';

$lang['de_DE']['SilvercartPaymentSaferpayError']['ERROR_1'] = 'Benötigte Attribute fehlen in Request';
$lang['de_DE']['SilvercartPaymentSaferpayError']['ERROR_2'] = 'Es wurden nicht alle Daten von Saferpay gesendet';
$lang['de_DE']['SilvercartPaymentSaferpayError']['ERROR_3'] = 'Es wurde eine falsche AccountID von Saferpay gesendet';
$lang['de_DE']['SilvercartPaymentSaferpayError']['ERROR_4'] = 'Es wurde ein falsches Token von Saferpay gesendet';
$lang['de_DE']['SilvercartPaymentSaferpayError']['ERROR_5'] = 'Verifikation bei Saferpay ist fehlgeschlagen';
$lang['de_DE']['SilvercartPaymentSaferpayError']['ERROR_6'] = 'Abbruch durch Saferpay';

$lang['de_DE']['SilvercartPaymentSaferpayLanguage']['SINGULARNAME'] = 'Übersetzung der Zahlart Saferpay';
$lang['de_DE']['SilvercartPaymentSaferpayLanguage']['PLURALNAME']   = 'Übersetzungen der Zahlart Saferpay';