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
 * @ignore
 */
if (!class_exists('SS_Object')) {
    class_alias('Object', 'SS_Object');
}

SS_Object::add_extension('SilvercartPaymentSaferpay',              'SilvercartDataObjectMultilingualDecorator');
SS_Object::add_extension('SilvercartOrder',                        'SilvercartPaymentSaferpayOrder');
SS_Object::add_extension('SilvercartShoppingCart',                 'SilvercartPaymentSaferpayShoppingCart');

SS_Object::add_extension('SilvercartOrderPluginProvider', 'SilvercartPaymentSaferpayOrderPlugin');