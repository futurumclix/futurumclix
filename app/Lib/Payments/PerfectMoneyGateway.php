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
App::uses('GatewayInterface', 'Payments');

/**
 * PerfectMoneyGateway
 *
 */
class PerfectMoneyGateway implements GatewayInterface {
	const ENDPOINT = 'https://perfectmoney.is/api/step1.asp';
	const CASHOUT_ENDPOINT = 'https://perfectmoney.is/acct/confirm.asp';

	protected $settings;
	protected $allowedIPs = array(
		'77.109.141.170',
		'91.205.41.208',
		'94.242.216.60',
		'78.41.203.75',
	);

	public function __construct($settings = array()) {
		$this->settings = $settings['api_settings'];
		$this->settings['currency_code'] = $settings['currency_code'];
	}

	public function pay(array $params = array()) {
		$payment = array(
			'gateway' => 'PerfectMoney',
			'url' => self::ENDPOINT,
			'data' => array(
				'PAYEE_ACCOUNT' => $this->settings['merchantName'],
				'PAYEE_NAME' => Configure::read('siteName'),
				'PAYMENT_ID' => $params['id'],
				'PAYMENT_AMOUNT' => round($params['amount'], 2),
				'PAYMENT_UNITS' => $this->settings['currency_code'],
				'STATUS_URL' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'index', 'PerfectMoney'), true),
				'PAYMENT_URL' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'success', 'PerfectMoney'), true),
				'PAYMENT_URL_METHOD' => 'LINK',
				'NOPAYMENT_URL' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'cancel', 'PerfectMoney'), true),
				'NOPAYMENT_URL_METHOD' => 'LINK',
				'SUGGESTED_MEMO' => $params['title'],
				'SUGGESTED_MEMO_NOCHANGE' => 1,
			),
		);

		CakeSession::write('Payment', $payment);

		return array('plugin' => null, 'controller' => 'payments', 'action' => 'paymentRedirect');
	}

	public function getOperationName(array $data = array()) {
		if(!in_array($_SERVER['REMOTE_ADDR'], $this->allowedIPs)) {
			throw new PaymentGatewayException(__d('admin', 'Not allowed'), 403);
		}

		return 'deposit';
	}

	public function depositCallback(array $data = array()) {
		$string = $_POST['PAYMENT_ID'].':'.$_POST['PAYEE_ACCOUNT'].':'.$_POST['PAYMENT_AMOUNT'].':'.$_POST['PAYMENT_UNITS'].':'.
		 $_POST['PAYMENT_BATCH_NUM'].':'.$_POST['PAYER_ACCOUNT'].':'.strtoupper(md5($this->settings['passphrase'])).':'.$_POST['TIMESTAMPGMT'];

		$hash = strtoupper(md5($string));

		if($hash != $_POST['V2_HASH']) {
			throw new PaymentGatewayException(__d('admin', 'Invalid V2_HASH'));
		}

		if($_POST['PAYEE_ACCOUNT'] != $this->settings['merchantName']) {
			throw new PaymentGatewayException(__d('admin', 'Invalid merchant account'));
		}

		if($_POST['PAYMENT_UNITS'] != $this->settings['currency_code']) {
			throw new PaymentGatewayException(__d('admin', 'Invalid currency'));
		}

		return array(PaymentStatus::SUCCESS, $_POST['PAYMENT_ID'], $_POST['PAYMENT_AMOUNT'], $_POST['PAYER_ACCOUNT']);
	}

	public function getDepositGatewayId(array $data = array()) {
		if(isset($_POST['ERROR']) && !empty($_POST['ERROR'])) {
			throw new PaymentGatewayException(__d('admin', 'Gateway error: %s', $_POST['ERROR']));
		}

		return $_POST['PAYMENT_BATCH_NUM'];
	}

	public static function needsSettings() {
		return true;
	}

	public static function validateSettings(array $check = array()) {
		if(strlen($check['cashoutMemo']) > 100) {
			return __d('admin', 'PerfectMoney payment memo cannot be longer than 100 characters');
		}

		return true;
	}

	public static function getAccountValidationRules() {
		return true;
	}

	public static function getSupportedCurrencies($direction) {
		return array(
			'EUR',
			'USD',
		);
	}

	public function cashout($cashout) {
		$data = array(
			'AccountID' => $this->settings['accountID'],
			'PassPhrase' => $this->settings['accountPassword'],
			'Payer_Account' => $this->settings['merchantName'],
			'Payee_Account' => $cashout['Cashout']['payment_account'],
			'Amount' => round($cashout['Cashout']['amount'], 2),
			'Memo' => $this->settings['cashoutMemo'],
			'PAYMENT_ID' => $cashout['Cashout']['id'],
		);

		$req = http_build_query($data);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, self::CASHOUT_ENDPOINT);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_PORT, 443);
		curl_setopt($ch, CURLOPT_VERBOSE, true);

		if(Configure::read('debug') > 0) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		}

		$res = curl_exec($ch);

		if($res === false) {
			throw new PaymentGatewayException(__d('admin', 'Failed to connect'));
		}

		curl_close($ch);

		// searching for hidden fields
		if(!preg_match_all("/<input name='(.*)' type='hidden' value='(.*)'>/", $res, $result, PREG_SET_ORDER)) {
			throw new PaymentGatewayException(__d('admin', 'Invalid response'));
		}

		$ar = "";
		foreach($result as $item) {
			$key = $item[1];
			$ar[$key] = $item[2];
		}

		if(isset($ar['ERROR'])) {
			throw new PaymentGatewayException(__d('admin', 'ERROR: %s', $ar['ERROR']));
		}

		return 'Completed';
	}

	public function supportsRefunds() {
		return false;
	}

	public function refund($transactionId) {
		throw new PaymentGatewayException(__d('admin', 'Not supported'));
	}

/**
 * Payment lists are not supported in PerfectMoney
 *
 */
	public function generatePaymentList($cashouts) {
		return false;
	}

/**
 * getCashoutId() and cashoutCallback() are not needed in PerfectMoney
 *
 */
	public function getCashoutId(array $data = array()) {
		throw new PaymentGatewayException(__d('admin', 'Not needed'));
	}

	public function cashoutCallback(array $data = array()) {
		throw new PaymentGatewayException(__d('admin', 'Not needed'));
	}
}
