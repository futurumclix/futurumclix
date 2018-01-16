<?php
/**
 * Copyright (c) 2018 FuturumClix
 *
 * This program is free software: you can redistribute it and/or  modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Please notice this program incorporates variety of libraries or other
 * programs that may or may not have their own licenses, also they may or
 * may not be modified by FuturumClix. All modifications made by
 * FuturumClix are available under the terms of GNU Affero General Public
 * License, version 3, if original license allows that.
 *
 * @copyright     Copyright (c) 2018 FuturumClix
 * @link          https://github.com/futurumclix/futurumclix
 * @license       https://opensource.org/licenses/AGPL-3.0 AGPLv3
 */
App::uses('PaymentStatus', 'Payments');
App::uses('PaymentGatewayException', 'Payments');

/**
 * GatewayInterface
 *
 */
interface GatewayInterface {
/**
 * Standard constructor
 *
 * @param array $settings - settings fetched from database
 */
	public function __construct($settings = array());

/**
 * This method should return redirect target (URL string 
 * or CakePHP URL array) to Gateway's login page to process
 * the payment. If gateway doesn't support GET redirection
 * one can store payment data in session and use
 * PaymentsController::paymentRedirect() as redirection
 * target. Return false if you don't want to redirect.
 *
 * @param array $params
 * @return mixed array/string/boolean
 */
	public function pay(array $params = array());

/**
 * This method should check received message and return
 * for what operation it refers (deposit or cashout) and
 * return suitable string ("deposit" or "cashout"). Any other
 * value will be treated as error.
 *
 * @return string
 */
	public function getOperationName(array $data = array());

/**
 * This method should return id for Cashout model.
 *
 * @return boolean
 */
	public function getCashoutId(array $data = array());

/**
 * This method should parse an cashout callback request.
 * 
 * @return PaymentStatus
 */
	public function cashoutCallback(array $data = array());

/**
 * This method should return unique id 
 * (in scope of that Gateway) for Deposit model.
 * This id must not be longer than 255 characters.
 *
 * @return boolean
 */
	public function getDepositGatewayId(array $data = array());

/**
 * This method should parse an deposit callback request.
 * This method should return array:
 * array[0] - one of const defined in class PaymentStatus
 * array[1] - string representing id in same format that was given
 *            in pay() call.
 * array[2] - total paid amount (with fees etc.)
 * array[3] - payer email
 * @return array (PaymentStatus, string, string, string, string)
 */
	public function depositCallback(array $data = array());

/**
 * This method should return true if Gateway needs settings page
 * and false otherwise
 *
 * @return boolean
 */
	public static function needsSettings();

/**
 * This method should validate Gateway settings.
 * If settings are valid should return true, otherwise
 * may return false or error message (will be displayed
 * as standard validation error). For Gateways returning false
 * from needsSettings() this method should be never called.
 *
 * @param array $check
 * @return mixed
 */
	public static function validateSettings(array $check = array());

/**
 * This method should return an array of standard validation rules
 * from cakephp to validate user account id (for example an
 * PayPal e-mail). Will be automatically added to UserProfile model.
 * False indicates that gateway don't need a account id field,
 * true means that account id do not need custom validation rules (standard
 * validation rules like maxLength and isUnique are added *always*).
 *
 * @return array/boolean
 */
	public static function getAccountValidationRules();

/**
 * This method should return an array of standard ISO 4217 currencies codes
 * supported by this payment gateway. Value of null will mean all currencies
 * are supported (useful for example in in-website payments). Direction argument
 * specify if returned currencies should be supported in "Cashout" or "Deposit".
 *
 * @return array/null
 */
	public static function getSupportedCurrencies($direction);

/**
 * This method should return a string containing PaymentList compatible 
 * with gateway MassPayment (or something similar) list
 * or false if gateway not supports this.
 *
 * @return string/boolean
 */
	public function generatePaymentList($cashouts);

/**
 * This method should send a cashout request to gateway.
 * If request failed method should return false, otherwise
 * should return Cashout status ('Pending', 'Completed', 
 * 'Failed' or 'Cancelled').
 *
 * @return boolean
 */
	public function cashout($cashout);

/**
 * This method should return true if gateway supports
 * refunds or false otherwise.
 *
 * @return boolean
 */
	public function supportsRefunds();

/**
 * This method should send a refund request to gateway.
 * If request failed method should return false otherwise
 * should return true.
 *
 * @return boolean
 */
	public function refund($transactionId);
}
