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
 * SolidTrustPayGateway
 *
 */
class SolidTrustPayGateway implements GatewayInterface {
	const ENDPOINT = 'https://solidtrustpay.com/handle.php';
	const CASHOUT_ENDPOINT = 'https://solidtrustpay.com/accapi/process.php';
	const CASHOUT_TOKEN = 'CaShOuT';

	protected $settings;
	protected $debug;

	public function __construct($settings = array()) {
		$this->settings = $settings['api_settings'];
		$this->settings['currency_code'] = $settings['currency_code'];
		$this->debug = $this->debug = Configure::read('debug');
	}

	public function pay(array $params = array()) {
		$payment = array(
			'gateway' => 'SolidTrust Pay',
			'url' => self::ENDPOINT,
			'data' => array(
				'merchantAccount' => $this->settings['merchantAccount'],
				'sci_name' => $this->settings['paymentName'],
				'amount' => round($params['amount'], 2),
				'currency' => $this->settings['currency_code'],
				'notify_url' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'index', 'SolidTrustPay'), true),
				'return_url' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'success', 'SolidTrustPay', $params['title']), true),
				'cancel_url' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'cancel', 'SolidTrustPay'), true),
				'item_id' => $params['id'],
			)
		);

		if($this->debug) {
			$payment['data']['testmode'] = 'ON';
		}

		CakeSession::write('Payment', $payment);

		return array('plugin' => null, 'controller' => 'payments', 'action' => 'paymentRedirect');
	}

	public function getOperationName(array $data = array()) {
		if(!$this->debug && $_POST['testmode'] == 'ON') {
			throw new PaymentGatewayException(__d('admin', 'Test mode without debug mode'));
		}

		if(isset($_POST['udf1']) && $_POST['udf1'] == self::CASHOUT_TOKEN) {
			return 'cashout';
		}

		return 'deposit';
	}

	public function depositCallback(array $data = array()) {
		if($_POST['merchantAccount'] != $this->settings['merchantAccount']) {
			throw new PaymentGatewayException(__d('admin', 'Invalid merchant'));
		}

		if(!isset($_POST['amount']) || empty($_POST['amount'])) {
			throw new PaymentGatewayException(__d('admin', 'Invalid amount'));
		}

		if($_POST['currency'] != $this->settings['currency_code']) {
			throw new PaymentGatewayException(__d('admin', 'Invalid currency: %s', $_POST['currency']));
		}

		$string = implode(':', array(
			$_POST['tr_id'],
			md5(md5($this->settings['paymentPassword'].'s+E_a*')),
			$_POST['amount'],
			$_POST['merchantAccount'],
			$_POST['payerAccount'],
		));
		$hash = md5($string);

		if($hash != $_POST['hash']) {
			throw new PaymentGatewayException(__d('admin', 'Invalid security hash'));
		}

		switch($_POST['status']) {
			case 'COMPLETE':
				$status = PaymentStatus::SUCCESS;
			break;

			case 'PENDING':
				$status = PaymentStatus::PENDING;
			break;

			case 'CANCELLED':
				$status = PaymentStatus::FAILED;
			break;
		}

		return array($status, $_POST['item_id'], $_POST['amount'], $_POST['payerAccount']);
	}

	public function getDepositGatewayId(array $data = array()) {
		return $_POST['tr_id'];
	}

	public static function needsSettings() {
		return true;
	}

	public static function validateSettings(array $check = array()) {
		return true;
	}

	public static function getAccountValidationRules() {
		return true;
	}

	public static function getSupportedCurrencies($direction) {
		return array(
			'USD',
			'EUR',
			'GBP',
			'AUD',
			'CAD',
			'NZD',
		);
	}

	public function cashout($cashout) {
		$result = 'Failed';

		$data = array(
			'api_id' => $this->settings['APIName'],
			'api_pwd' => md5($this->settings['APIPassword'].'s+E_a*'),
			'user' => $cashout['Cashout']['payment_account'],
			'amount' => round($cashout['Cashout']['amount'], 2),
			'currency' => $this->settings['currency_code'],
			'item_id' => $cashout['Cashout']['id'],
			'udf1' => self::CASHOUT_TOKEN,
		);

		if($this->debug) {
			$data['testmode'] = 'ON';
		}

		$req = http_build_query($data);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, self::CASHOUT_ENDPOINT);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_PORT, 443);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		if($this->debug) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		}

		$res = curl_exec($ch);

		if($res === false) {
			throw new PaymentGatewayException(__d('admin', 'Failed to connect'));
		}

		if(strstr($res, 'Status is ACCEPTED') !== false) {
			$result = 'Completed';
		}

		curl_close($ch);

		return $result;
	}

	public function getCashoutId(array $data = array()) {
		return $_POST['tr_id'];
	}

	public function cashoutCallback(array $data = array()) {
		if($_POST['status'] == 'ACCEPTED') {
			return PaymentStatus::SUCCESS;
		}

		return PaymentStatus::FAILED;
	}

	public function supportsRefunds() {
		return false;
	}

	public function refund($transactionId) {
		throw new PaymentGatewayException(__d('admin', 'Not supported'));
	}

/**
 * Payment lists are not supported in SolidTrustPay
 *
 */
	public function generatePaymentList($cashouts) {
		return false;
	}
}
